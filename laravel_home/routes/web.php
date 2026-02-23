<?php

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

$mapLocaleToWpmlCode = function (string $locale): string {
    return strtolower($locale) === 'en' ? 'en' : 'ar';
};

$resolveWpmlProductLocalization = function (string $slug, string $locale) use ($mapLocaleToWpmlCode): array {
    $result = [
        'has_wpml_table' => Schema::hasTable('wp_icl_translations'),
        'wpml_code' => $mapLocaleToWpmlCode($locale),
        'slug' => $slug,
        'base_product_id' => null,
        'trid' => null,
        'localized_product_id' => null,
        'matched_by' => 'slug',
    ];

    if (!$result['has_wpml_table']) {
        return $result;
    }

    $baseProductId = DB::table('wp_posts')
        ->where('post_type', 'product')
        ->where('post_status', 'publish')
        ->where('post_name', $slug)
        ->value('ID');

    if (!$baseProductId) {
        return $result;
    }

    $result['base_product_id'] = (int) $baseProductId;

    $trid = DB::table('wp_icl_translations')
        ->where('element_type', 'post_product')
        ->where('element_id', (int) $baseProductId)
        ->value('trid');

    if (!$trid) {
        return $result;
    }

    $result['trid'] = (int) $trid;

    $localizedProductId = DB::table('wp_icl_translations')
        ->where('element_type', 'post_product')
        ->where('trid', (int) $trid)
        ->where('language_code', 'like', $result['wpml_code'] . '%')
        ->value('element_id');

    if ($localizedProductId) {
        $result['localized_product_id'] = (int) $localizedProductId;
        $result['matched_by'] = 'wpml';
    }

    return $result;
};

$localizeProductsCollectionByWpml = function ($rows, string $locale, bool $includeContentFields = false) use ($mapLocaleToWpmlCode) {
    $collection = collect($rows);

    if ($collection->isEmpty() || !Schema::hasTable('wp_icl_translations')) {
        return $collection;
    }

    $productIds = $collection
        ->pluck('ID')
        ->map(fn ($id) => (int) $id)
        ->filter(fn ($id) => $id > 0)
        ->unique()
        ->values();

    if ($productIds->isEmpty()) {
        return $collection;
    }

    $wpmlCode = $mapLocaleToWpmlCode($locale);

    $translationRows = DB::table('wp_icl_translations as base')
        ->join('wp_icl_translations as localized', function ($join) {
            $join->on('base.trid', '=', 'localized.trid')
                ->where('localized.element_type', 'post_product');
        })
        ->where('base.element_type', 'post_product')
        ->whereIn('base.element_id', $productIds->all())
        ->where('localized.language_code', 'like', $wpmlCode . '%')
        ->select('base.element_id as source_id', 'localized.element_id as localized_id')
        ->get();

    if ($translationRows->isEmpty()) {
        return $collection;
    }

    $localizedIdBySourceId = [];
    foreach ($translationRows as $translationRow) {
        $sourceId = (int) ($translationRow->source_id ?? 0);
        $localizedId = (int) ($translationRow->localized_id ?? 0);
        if ($sourceId > 0 && $localizedId > 0 && !array_key_exists($sourceId, $localizedIdBySourceId)) {
            $localizedIdBySourceId[$sourceId] = $localizedId;
        }
    }

    if (empty($localizedIdBySourceId)) {
        return $collection;
    }

    $localizedIds = array_values(array_unique(array_map(fn ($id) => (int) $id, $localizedIdBySourceId)));

    $localizedPosts = DB::table('wp_posts')
        ->whereIn('ID', $localizedIds)
        ->where('post_type', 'product')
        ->where('post_status', 'publish')
        ->select('ID', 'post_title', 'post_name', 'post_excerpt', 'post_content')
        ->get()
        ->keyBy('ID');

    if ($localizedPosts->isEmpty()) {
        return $collection;
    }

    return $collection->map(function ($row) use ($localizedIdBySourceId, $localizedPosts, $includeContentFields) {
        $sourceId = (int) ($row->ID ?? 0);
        $localizedId = $localizedIdBySourceId[$sourceId] ?? null;
        if (!$localizedId) {
            return $row;
        }

        $localizedPost = $localizedPosts->get((int) $localizedId);
        if (!$localizedPost) {
            return $row;
        }

        $row->ID = (int) $localizedPost->ID;
        $row->post_title = (string) ($localizedPost->post_title ?? $row->post_title ?? '');
        $row->post_name = (string) ($localizedPost->post_name ?? $row->post_name ?? '');

        if ($includeContentFields) {
            if (property_exists($row, 'post_excerpt')) {
                $row->post_excerpt = (string) ($localizedPost->post_excerpt ?? $row->post_excerpt ?? '');
            }
            if (property_exists($row, 'post_content')) {
                $row->post_content = (string) ($localizedPost->post_content ?? $row->post_content ?? '');
            }
        }

        return $row;
    });
};

$homeHandler = function (string $locale = 'ar') use ($localizeProductsCollectionByWpml) {
    $currentLocale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    $localePrefix = $currentLocale === 'en' ? '/en' : '/ar';

    $reviewFiles = collect(array_merge(
        glob(public_path('google-reviews/*.{png,jpg,jpeg,webp,avif,gif}'), GLOB_BRACE) ?: [],
        glob(base_path('Google Reviews/*.{png,jpg,jpeg,webp,avif,gif}'), GLOB_BRACE) ?: []
    ))
        ->filter(fn ($path) => is_file($path))
        ->unique(fn ($path) => mb_strtolower(basename($path)))
        ->sortBy(fn ($path) => basename($path), SORT_NATURAL | SORT_FLAG_CASE)
        ->values();

    $reviewImages = $reviewFiles
        ->map(function ($path) {
            $version = @filemtime($path) ?: time();
            return '/google-reviews/' . rawurlencode(basename($path)) . '?v=' . $version;
        })
        ->values();

    $products = Cache::remember('home_products_' . $currentLocale, 300, function () use ($currentLocale, $localizeProductsCollectionByWpml) {

        $rows = DB::table('wp_posts as p')
            ->leftJoin('wp_postmeta as price', function ($join) {
                $join->on('p.ID', '=', 'price.post_id')
                     ->where('price.meta_key', '_price');
            })
            ->leftJoin('wp_postmeta as regular', function ($join) {
                $join->on('p.ID', '=', 'regular.post_id')
                     ->where('regular.meta_key', '_regular_price');
            })
            ->leftJoin('wp_postmeta as sale', function ($join) {
                $join->on('p.ID', '=', 'sale.post_id')
                     ->where('sale.meta_key', '_sale_price');
            })
            ->leftJoin('wp_postmeta as thumb', function ($join) {
                $join->on('p.ID', '=', 'thumb.post_id')
                     ->where('thumb.meta_key', '_thumbnail_id');
            })
            ->leftJoin('wp_posts as img', 'thumb.meta_value', '=', 'img.ID')
            ->where('p.post_type', 'product')
            ->where('p.post_status', 'publish')
            ->orderBy('p.post_date', 'desc')
            ->select(
                'p.ID',
                'p.post_title',
                'p.post_name',
                'price.meta_value as price',
                'regular.meta_value as regular_price',
                'sale.meta_value as sale_price',
                'img.guid as image'
            )
            ->limit(12)
            ->get();

            return $localizeProductsCollectionByWpml($rows, $currentLocale);

    });

    $stats = Cache::remember('home_stats', 300, function () {
        $base = DB::table('wp_posts')
            ->where('post_type', 'product')
            ->where('post_status', 'publish');

        $totalProducts = (clone $base)->count();

        $saleProducts = DB::table('wp_posts as p')
            ->join('wp_postmeta as sale', function ($join) {
                $join->on('p.ID', '=', 'sale.post_id')
                    ->where('sale.meta_key', '_sale_price');
            })
            ->where('p.post_type', 'product')
            ->where('p.post_status', 'publish')
            ->whereNotNull('sale.meta_value')
            ->where('sale.meta_value', '!=', '')
            ->count();

        $prices = DB::table('wp_posts as p')
            ->join('wp_postmeta as price', function ($join) {
                $join->on('p.ID', '=', 'price.post_id')
                    ->where('price.meta_key', '_price');
            })
            ->where('p.post_type', 'product')
            ->where('p.post_status', 'publish')
            ->whereNotNull('price.meta_value')
            ->where('price.meta_value', '!=', '')
            ->pluck('price.meta_value')
            ->map(fn ($value) => (float) $value)
            ->filter(fn ($value) => $value > 0)
            ->values();

        return [
            'total_products' => $totalProducts,
            'sale_products' => $saleProducts,
            'min_price' => $prices->isNotEmpty() ? $prices->min() : null,
            'max_price' => $prices->isNotEmpty() ? $prices->max() : null,
        ];
    });

    return view('home', compact('products', 'stats', 'reviewImages', 'currentLocale', 'localePrefix'));
};

Route::get('/', fn () => $homeHandler('ar'));
Route::get('/ar', fn () => $homeHandler('ar'));
Route::get('/en', fn () => $homeHandler('en'));

$shopDataHandler = function (Request $request, string $locale = 'ar') use ($localizeProductsCollectionByWpml) {
    $search = trim((string) $request->query('q', ''));
    $sort = (string) $request->query('sort', 'newest');

    if (!in_array($sort, ['newest', 'price_asc', 'price_desc'], true)) {
        $sort = 'newest';
    }

    $query = DB::table('wp_posts as p')
        ->leftJoin('wp_postmeta as price', function ($join) {
            $join->on('p.ID', '=', 'price.post_id')
                ->where('price.meta_key', '_price');
        })
        ->leftJoin('wp_postmeta as regular', function ($join) {
            $join->on('p.ID', '=', 'regular.post_id')
                ->where('regular.meta_key', '_regular_price');
        })
        ->leftJoin('wp_postmeta as thumb', function ($join) {
            $join->on('p.ID', '=', 'thumb.post_id')
                ->where('thumb.meta_key', '_thumbnail_id');
        })
        ->leftJoin('wp_posts as img', 'thumb.meta_value', '=', 'img.ID')
        ->where('p.post_type', 'product')
        ->where('p.post_status', 'publish');

    if ($search !== '') {
        $query->where('p.post_title', 'like', "%{$search}%");
    }

    if ($sort === 'price_asc') {
        $query->orderByRaw('CAST(NULLIF(price.meta_value, "") AS DECIMAL(10,2)) ASC')
            ->orderBy('p.post_date', 'desc');
    } elseif ($sort === 'price_desc') {
        $query->orderByRaw('CAST(NULLIF(price.meta_value, "") AS DECIMAL(10,2)) DESC')
            ->orderBy('p.post_date', 'desc');
    } else {
        $query->orderBy('p.post_date', 'desc');
    }

    $products = $query->select(
        'p.ID',
        'p.post_title',
        'p.post_name',
        'price.meta_value as price',
        'regular.meta_value as regular_price',
        'img.guid as image'
    )->paginate(16)->withQueryString();

    $products->setCollection($localizeProductsCollectionByWpml($products->getCollection(), $locale));

    return [$products, $search, $sort];
};

$shopHandler = function (Request $request, string $locale = 'ar') use ($shopDataHandler) {
    $currentLocale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    $localePrefix = $currentLocale === 'en' ? '/en' : '/ar';

    [$products, $search, $sort] = $shopDataHandler($request, $currentLocale);

    if ($request->expectsJson() || $request->wantsJson() || strtolower((string) $request->header('X-Requested-With')) === 'xmlhttprequest') {
        $items = $products->getCollection()->map(function ($product) {
            $price = (float) ($product->price ?? 0);
            $regular = (float) ($product->regular_price ?? 0);
            $isSale = $regular > 0 && $price > 0 && $regular > $price;
            $discount = $isSale ? (int) round((($regular - $price) / $regular) * 100) : 0;
            $saving = $isSale ? ($regular - $price) : 0;

            return [
                'id' => (int) $product->ID,
                'title' => $product->post_title,
                'slug' => $product->post_name,
                'price' => $price,
                'regular_price' => $regular,
                'is_sale' => $isSale,
                'discount' => $discount,
                'saving' => $saving,
                'image' => $product->image ?: 'https://styliiiish.com/wp-content/uploads/woocommerce-placeholder.png',
            ];
        })->values();

        return response()->json([
            'filters' => [
                'q' => $search,
                'sort' => $sort,
            ],
            'products' => $items,
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
                'per_page' => $products->perPage(),
                'next_page' => $products->hasMorePages() ? $products->currentPage() + 1 : null,
                'prev_page' => $products->onFirstPage() ? null : $products->currentPage() - 1,
            ],
        ]);
    }

    return view('shop', compact('search', 'sort', 'currentLocale', 'localePrefix'));
};

Route::get('/shop', fn (Request $request) => $shopHandler($request, 'ar'));
Route::get('/ar/shop', fn (Request $request) => $shopHandler($request, 'ar'));
Route::get('/en/shop', fn (Request $request) => $shopHandler($request, 'en'));

$singleProductHandler = function (Request $request, string $slug, string $locale = 'ar') use ($localizeProductsCollectionByWpml, $resolveWpmlProductLocalization) {
    $currentLocale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    $localePrefix = $currentLocale === 'en' ? '/en' : '/ar';
    $wpBaseUrl = rtrim((string) (env('WP_PUBLIC_URL') ?: $request->getSchemeAndHttpHost()), '/');
    $isWpmlDebug = $request->boolean('debug_wpml');

    $wpmlResolution = $resolveWpmlProductLocalization($slug, $currentLocale);
    $localizedProductId = (int) ($wpmlResolution['localized_product_id'] ?? 0);

    $product = DB::table('wp_posts as p')
        ->leftJoin('wp_postmeta as price', function ($join) {
            $join->on('p.ID', '=', 'price.post_id')
                ->where('price.meta_key', '_price');
        })
        ->leftJoin('wp_postmeta as regular', function ($join) {
            $join->on('p.ID', '=', 'regular.post_id')
                ->where('regular.meta_key', '_regular_price');
        })
        ->leftJoin('wp_postmeta as sale', function ($join) {
            $join->on('p.ID', '=', 'sale.post_id')
                ->where('sale.meta_key', '_sale_price');
        })
        ->leftJoin('wp_postmeta as thumb', function ($join) {
            $join->on('p.ID', '=', 'thumb.post_id')
                ->where('thumb.meta_key', '_thumbnail_id');
        })
        ->leftJoin('wp_posts as img', 'thumb.meta_value', '=', 'img.ID')
        ->where('p.post_type', 'product')
        ->where('p.post_status', 'publish')
        ->where(function ($query) use ($slug, $localizedProductId) {
            if (!empty($localizedProductId)) {
                $query->where('p.ID', (int) $localizedProductId);
            } else {
                $query->where('p.post_name', $slug);
            }
        })
        ->select(
            'p.ID',
            'p.post_title',
            'p.post_name',
            'p.post_content',
            'p.post_excerpt',
            'price.meta_value as price',
            'regular.meta_value as regular_price',
            'sale.meta_value as sale_price',
            'img.guid as image'
        )
        ->first();

    if ($product) {
        $product = $localizeProductsCollectionByWpml([$product], $currentLocale, true)->first();
    }

    if ($isWpmlDebug) {
        $wpmlRows = collect();
        if (!empty($wpmlResolution['trid']) && Schema::hasTable('wp_icl_translations')) {
            $wpmlRows = DB::table('wp_icl_translations')
                ->where('element_type', 'post_product')
                ->where('trid', (int) $wpmlResolution['trid'])
                ->orderBy('language_code')
                ->get(['element_id', 'language_code', 'source_language_code', 'trid']);
        }

        $candidateIds = collect([
            (int) ($wpmlResolution['base_product_id'] ?? 0),
            (int) ($wpmlResolution['localized_product_id'] ?? 0),
            (int) ($product->ID ?? 0),
        ])->merge($wpmlRows->pluck('element_id')->map(fn ($id) => (int) $id))
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        $posts = collect();
        if ($candidateIds->isNotEmpty()) {
            $posts = DB::table('wp_posts')
                ->whereIn('ID', $candidateIds->all())
                ->select('ID', 'post_title', 'post_name', 'post_status', 'post_type')
                ->orderBy('ID')
                ->get();
        }

        return response()->json([
            'locale' => $currentLocale,
            'slug' => $slug,
            'found_product' => (bool) $product,
            'product' => $product ? [
                'ID' => (int) ($product->ID ?? 0),
                'post_title' => (string) ($product->post_title ?? ''),
                'post_name' => (string) ($product->post_name ?? ''),
            ] : null,
            'resolution' => $wpmlResolution,
            'wpml_rows' => $wpmlRows,
            'posts' => $posts,
        ], $product ? 200 : 404);
    }

    if (!$product) {
        abort(404);
    }

    $metaRows = DB::table('wp_postmeta')
        ->where('post_id', (int) $product->ID)
        ->select('meta_key', 'meta_value')
        ->get();

    $metaByKey = [];
    foreach ($metaRows as $metaRow) {
        $metaKey = (string) $metaRow->meta_key;
        $rawValue = (string) ($metaRow->meta_value ?? '');

        if (!array_key_exists($metaKey, $metaByKey)) {
            $metaByKey[$metaKey] = $rawValue;
        }
    }

    $isAbsoluteUrl = function (string $value): bool {
        $lower = strtolower(trim($value));
        return str_starts_with($lower, 'http://') || str_starts_with($lower, 'https://');
    };

    $galleryImages = [];

    if (!empty($product->image) && $isAbsoluteUrl((string) $product->image)) {
        $galleryImages[] = (string) $product->image;
    }

    $galleryMetaValue = trim((string) ($metaByKey['_product_image_gallery'] ?? ''));
    if ($galleryMetaValue !== '') {
        $galleryIds = collect(explode(',', $galleryMetaValue))
            ->map(fn ($value) => (int) trim((string) $value))
            ->filter(fn ($value) => $value > 0)
            ->values();

        if ($galleryIds->isNotEmpty()) {
            $galleryRows = DB::table('wp_posts')
                ->whereIn('ID', $galleryIds->all())
                ->where('post_type', 'attachment')
                ->select('ID', 'guid')
                ->get()
                ->keyBy('ID');

            foreach ($galleryIds as $galleryId) {
                $galleryRow = $galleryRows->get($galleryId);
                $url = trim((string) ($galleryRow->guid ?? ''));
                if ($url !== '' && $isAbsoluteUrl($url)) {
                    $galleryImages[] = $url;
                }
            }
        }
    }

    $galleryImages = array_values(array_unique($galleryImages));

    $normalizeMeta = function (?string $value): string {
        if ($value === null) {
            return '';
        }

        $trimmed = trim($value);
        if ($trimmed === '') {
            return '';
        }

        $decoded = @unserialize($trimmed);
        if ($decoded !== false || $trimmed === 'b:0;') {
            if (is_array($decoded)) {
                $flat = [];
                array_walk_recursive($decoded, function ($item) use (&$flat) {
                    if (is_scalar($item)) {
                        $text = trim(strip_tags((string) $item));
                        if ($text !== '') {
                            $flat[] = $text;
                        }
                    }
                });
                return implode(', ', array_values(array_unique($flat)));
            }

            if (is_scalar($decoded)) {
                return trim(strip_tags((string) $decoded));
            }
        }

        return trim(strip_tags($trimmed));
    };

    $findMetaByNeedles = function (array $needles) use ($metaByKey, $normalizeMeta): string {
        foreach ($metaByKey as $metaKey => $metaValue) {
            $lowerKey = strtolower((string) $metaKey);
            foreach ($needles as $needle) {
                if (str_contains($lowerKey, strtolower($needle))) {
                    $normalized = $normalizeMeta($metaValue);
                    if ($normalized !== '') {
                        return $normalized;
                    }
                }
            }
        }

        return '';
    };

    $attributeRows = DB::table('wp_term_relationships as tr')
        ->join('wp_term_taxonomy as tt', 'tr.term_taxonomy_id', '=', 'tt.term_taxonomy_id')
        ->join('wp_terms as t', 'tt.term_id', '=', 't.term_id')
        ->where('tr.object_id', (int) $product->ID)
        ->where('tt.taxonomy', 'like', 'pa\\_%')
        ->select('tt.taxonomy', 't.name', 't.slug')
        ->get();

    $attributesByTaxonomy = [];
    $attributeOptionsByTaxonomy = [];
    foreach ($attributeRows as $attributeRow) {
        $taxonomy = (string) $attributeRow->taxonomy;
        $name = trim((string) $attributeRow->name);
        $slugValue = trim((string) ($attributeRow->slug ?? ''));

        if ($name === '') {
            continue;
        }

        if (!isset($attributesByTaxonomy[$taxonomy])) {
            $attributesByTaxonomy[$taxonomy] = [];
        }

        if (!in_array($name, $attributesByTaxonomy[$taxonomy], true)) {
            $attributesByTaxonomy[$taxonomy][] = $name;
        }

        if (!isset($attributeOptionsByTaxonomy[$taxonomy])) {
            $attributeOptionsByTaxonomy[$taxonomy] = [];
        }

        $alreadyExists = false;
        foreach ($attributeOptionsByTaxonomy[$taxonomy] as $existingOption) {
            if (($existingOption['slug'] ?? '') === $slugValue) {
                $alreadyExists = true;
                break;
            }
        }

        if (!$alreadyExists && $slugValue !== '') {
            $attributeOptionsByTaxonomy[$taxonomy][] = [
                'slug' => $slugValue,
                'name' => $name,
            ];
        }
    }

    $findAttributeValues = function (array $needles) use ($attributesByTaxonomy): array {
        $values = [];

        foreach ($attributesByTaxonomy as $taxonomy => $items) {
            $lowerTaxonomy = strtolower((string) $taxonomy);
            $matched = false;

            foreach ($needles as $needle) {
                if (str_contains($lowerTaxonomy, strtolower($needle))) {
                    $matched = true;
                    break;
                }
            }

            if (!$matched) {
                continue;
            }

            foreach ($items as $item) {
                if (!in_array($item, $values, true)) {
                    $values[] = $item;
                }
            }
        }

        return $values;
    };

    $wooAttributeLabelTranslations = [
        'ar' => [
            'pa_size' => 'المقاس',
            'pa_color' => 'اللون',
            'pa_colour' => 'اللون',
            'pa_material' => 'الخامة',
            'pa_fabric' => 'الخامة',
            'pa_product-condition' => 'الحالة',
            'pa_condition' => 'الحالة',
        ],
        'en' => [
            'pa_size' => 'Size',
            'pa_color' => 'Color',
            'pa_colour' => 'Color',
            'pa_material' => 'Material',
            'pa_fabric' => 'Material',
            'pa_product-condition' => 'Condition',
            'pa_condition' => 'Condition',
        ],
    ];

    $wooAttributeValueTranslations = [
        'ar' => [
            'global' => [
                'new' => 'جديد',
                'used' => 'مستعمل',
                'excellent' => 'ممتاز',
                'very good' => 'جيد جدًا',
                'good' => 'جيد',
                'new — styliiiish certified🔥' => 'جديد — معتمد من Styliiiish 🔥',
                'new-styliiiish-certified' => 'جديد — معتمد من Styliiiish 🔥',
            ],
            'pa_size' => [
                'xsmall' => 'XS',
                'x-small' => 'XS',
                'xs' => 'XS',
                'small' => 'S',
                's' => 'S',
                'medium' => 'M',
                'med' => 'M',
                'm' => 'M',
                'large' => 'L',
                'l' => 'L',
                'xlarge' => 'XL',
                'x-large' => 'XL',
                'xl' => 'XL',
                '2xl' => 'XXL',
                'xxl' => 'XXL',
                '3xl' => '3XL',
            ],
            'pa_color' => [
                'beige' => 'بيج',
                'black' => 'أسود',
                'white' => 'أبيض',
                'red' => 'أحمر',
                'blue' => 'أزرق',
                'green' => 'أخضر',
                'pink' => 'وردي',
                'gold' => 'ذهبي',
                'silver' => 'فضي',
                'ivory' => 'عاجي',
                'nude' => 'نيود',
                'cream' => 'كريمي',
                'purple' => 'بنفسجي',
                'gray' => 'رمادي',
                'grey' => 'رمادي',
                'brown' => 'بني',
            ],
            'pa_material' => [
                'chiffon' => 'شيفون',
                'satin' => 'ساتان',
                'silk' => 'حرير',
                'tulle' => 'تول',
                'lace' => 'دانتيل',
                'crepe' => 'كريب',
                'velvet' => 'مخمل',
            ],
            'pa_product-condition' => [
                'new' => 'جديد',
                'brand-new' => 'جديد',
                'used' => 'مستعمل',
                'pre-loved' => 'مستعمل',
                'new — styliiiish certified🔥' => 'جديد — معتمد من Styliiiish 🔥',
            ],
        ],
        'en' => [
            'global' => [
                'جديد' => 'New',
                'مستعمل' => 'Used',
                'ممتاز' => 'Excellent',
                'جيد جدًا' => 'Very Good',
                'جيد' => 'Good',
                'جديد — معتمد من styliiiish 🔥' => 'New — Styliiiish Certified🔥',
            ],
            'pa_size' => [
                'اكس سمول' => 'XS',
                'سمول' => 'S',
                'ميديم' => 'M',
                'لارج' => 'L',
                'اكس لارج' => 'XL',
            ],
            'pa_color' => [
                'بيج' => 'Beige',
                'أسود' => 'Black',
                'أبيض' => 'White',
                'أحمر' => 'Red',
                'أزرق' => 'Blue',
                'أخضر' => 'Green',
                'وردي' => 'Pink',
                'ذهبي' => 'Gold',
                'فضي' => 'Silver',
                'عاجي' => 'Ivory',
                'نيود' => 'Nude',
                'كريمي' => 'Cream',
                'بنفسجي' => 'Purple',
                'رمادي' => 'Gray',
                'بني' => 'Brown',
            ],
            'pa_material' => [
                'شيفون' => 'Chiffon',
                'ساتان' => 'Satin',
                'حرير' => 'Silk',
                'تول' => 'Tulle',
                'دانتيل' => 'Lace',
                'كريب' => 'Crepe',
                'مخمل' => 'Velvet',
            ],
            'pa_product-condition' => [
                'جديد' => 'New',
                'مستعمل' => 'Used',
                'جديد — معتمد من styliiiish 🔥' => 'New — Styliiiish Certified🔥',
            ],
        ],
    ];

    $normalizeWooTaxonomyKey = function (string $taxonomy): string {
        $key = strtolower(trim($taxonomy));
        if (str_starts_with($key, 'attribute_')) {
            $key = substr($key, 10);
        }
        return $key;
    };

    $normalizeTranslationKey = function (string $value): string {
        return trim(mb_strtolower(str_replace(['_', '-'], ' ', $value)));
    };

    $translateWooAttributeLabel = function (string $taxonomy, string $fallbackLabel) use ($currentLocale, $wooAttributeLabelTranslations, $normalizeWooTaxonomyKey): string {
        $localeMap = $wooAttributeLabelTranslations[$currentLocale] ?? [];
        $taxonomyKey = $normalizeWooTaxonomyKey($taxonomy);
        return $localeMap[$taxonomyKey] ?? $fallbackLabel;
    };

    $translateWooAttributeValue = function (string $taxonomy, string $slug, string $fallbackValue) use ($currentLocale, $wooAttributeValueTranslations, $normalizeTranslationKey, $normalizeWooTaxonomyKey): string {
        $localeMap = $wooAttributeValueTranslations[$currentLocale] ?? [];
        $taxonomyKey = $normalizeWooTaxonomyKey($taxonomy);
        $taxonomyMap = $localeMap[$taxonomyKey] ?? [];

        $slugKey = $normalizeTranslationKey($slug);
        if ($slugKey !== '' && array_key_exists($slugKey, $taxonomyMap)) {
            return (string) $taxonomyMap[$slugKey];
        }

        $valueKey = $normalizeTranslationKey($fallbackValue);
        if ($valueKey !== '' && array_key_exists($valueKey, $taxonomyMap)) {
            return (string) $taxonomyMap[$valueKey];
        }

        $globalMap = $localeMap['global'] ?? [];
        if ($valueKey !== '' && array_key_exists($valueKey, $globalMap)) {
            return (string) $globalMap[$valueKey];
        }

        return $fallbackValue;
    };

    $materialValues = $findAttributeValues(['material', 'fabric', 'matiere', 'qamash', 'khama']);
    $colorValues = $findAttributeValues(['color', 'colour', 'لون', 'colorway']);
    $sizeValues = $findAttributeValues(['size', 'sizes', 'مقاس']);
    $conditionValues = $findAttributeValues(['condition', 'state', 'status', 'حاله']);

    $materialValues = array_values(array_unique(array_map(function ($value) use ($translateWooAttributeValue) {
        $raw = (string) $value;
        return $translateWooAttributeValue('pa_material', $raw, $raw);
    }, $materialValues)));

    $colorValues = array_values(array_unique(array_map(function ($value) use ($translateWooAttributeValue) {
        $raw = (string) $value;
        return $translateWooAttributeValue('pa_color', $raw, $raw);
    }, $colorValues)));

    $sizeValues = array_values(array_unique(array_map(function ($value) use ($translateWooAttributeValue) {
        $raw = (string) $value;
        return $translateWooAttributeValue('pa_size', $raw, $raw);
    }, $sizeValues)));

    $conditionValues = array_values(array_unique(array_map(function ($value) use ($translateWooAttributeValue) {
        $raw = (string) $value;
        return $translateWooAttributeValue('pa_product-condition', $raw, $raw);
    }, $conditionValues)));

    $material = !empty($materialValues) ? implode(', ', $materialValues) : $findMetaByNeedles(['material', 'fabric', 'khama']);
    $color = !empty($colorValues) ? implode(', ', $colorValues) : $findMetaByNeedles(['color', 'colour', 'لون']);

    if ($material !== '') {
        $material = $translateWooAttributeValue('pa_material', $material, $material);
    }

    if ($color !== '') {
        $color = $translateWooAttributeValue('pa_color', $color, $color);
    }

    if (empty($sizeValues)) {
        $sizeFromMeta = $findMetaByNeedles(['size', 'sizes', 'available_size', 'available_sizes']);
        if ($sizeFromMeta !== '') {
            $splitSizes = preg_split('/\s*[,|\-]\s*/u', $sizeFromMeta) ?: [];
            foreach ($splitSizes as $splitSize) {
                $cleanSize = trim($splitSize);
                if ($cleanSize !== '' && !in_array($cleanSize, $sizeValues, true)) {
                    $sizeValues[] = $cleanSize;
                }
            }
        }
    }

    $sizeValues = array_values(array_unique(array_map(function ($value) use ($translateWooAttributeValue) {
        $raw = (string) $value;
        return $translateWooAttributeValue('pa_size', $raw, $raw);
    }, $sizeValues)));

    $condition = !empty($conditionValues)
        ? implode(', ', $conditionValues)
        : $findMetaByNeedles(['condition', 'state', 'availability', 'certified']);

    if ($condition !== '') {
        $condition = $translateWooAttributeValue('pa_product-condition', $condition, $condition);
    }

    if ($condition === '') {
        $condition = $currentLocale === 'en'
            ? 'New — Styliiiish Certified🔥'
            : 'جديد — معتمد من Styliiiish 🔥';
    }

    $deliveryIntro = $findMetaByNeedles(['delivery_intro', 'delivery_note', 'delivery_text', 'shipping_note', 'ready_size_note']);
    if ($deliveryIntro === '') {
        $deliveryIntro = $currentLocale === 'en'
            ? 'This dress is available in one ready size. If another size is requested, the item will be made to order.'
            : 'هذا الفستان متوفر بمقاس جاهز واحد، وإذا طُلب مقاس مختلف يتم تنفيذه حسب الطلب.';
    }

    $readyDelivery = $findMetaByNeedles(['ready_delivery', 'ready_size_delivery', 'delivery_ready', 'ready_time']);
    if ($readyDelivery === '') {
        $readyDelivery = $currentLocale === 'en'
            ? 'Ready size: Delivery within 2–4 business days'
            : 'المقاس الجاهز: التوصيل خلال 2–4 أيام عمل';
    }

    $customDelivery = $findMetaByNeedles(['custom_delivery', 'made_to_order_delivery', 'delivery_custom', 'custom_time']);
    if ($customDelivery === '') {
        $customDelivery = $currentLocale === 'en'
            ? 'Custom size (Made-to-Order): Delivery within 7–10 business days'
            : 'المقاس الخاص (تفصيل حسب الطلب): التوصيل خلال 7–10 أيام عمل';
    }

    $extractFirstUrl = function (?string $text): string {
        $raw = trim((string) ($text ?? ''));
        if ($raw === '') {
            return '';
        }

        if (preg_match('/https?:\/\/[^\s"\'<>]+/i', $raw, $matches)) {
            return trim((string) ($matches[0] ?? ''));
        }

        return '';
    };

    $sizeGuideUrl = trim($findMetaByNeedles(['size_guide_url', 'size_guide_link', 'guide_url', 'size_chart_url']));

    if ($sizeGuideUrl !== '' && !$isAbsoluteUrl($sizeGuideUrl) && str_starts_with($sizeGuideUrl, '/')) {
        $sizeGuideUrl = $wpBaseUrl . $sizeGuideUrl;
    }

    if ($sizeGuideUrl === '' || !$isAbsoluteUrl($sizeGuideUrl)) {
        $optionCandidates = DB::table('wp_options')
            ->where(function ($query) {
                $query->where('option_name', 'like', '%size%guide%')
                    ->orWhere('option_name', 'like', '%size%chart%');
            })
            ->select('option_name', 'option_value')
            ->limit(20)
            ->get();

        foreach ($optionCandidates as $optionCandidate) {
            $optionValue = trim((string) ($optionCandidate->option_value ?? ''));
            if ($optionValue === '') {
                continue;
            }

            if ($isAbsoluteUrl($optionValue)) {
                $sizeGuideUrl = $optionValue;
                break;
            }

            if (ctype_digit($optionValue)) {
                $guidePost = DB::table('wp_posts')
                    ->where('ID', (int) $optionValue)
                    ->where('post_status', 'publish')
                    ->select('post_name')
                    ->first();

                if ($guidePost && !empty($guidePost->post_name)) {
                    $sizeGuideUrl = $wpBaseUrl . '/' . trim((string) $guidePost->post_name, '/') . '/';
                    break;
                }
            }

            if (str_starts_with($optionValue, '/')) {
                $sizeGuideUrl = $wpBaseUrl . $optionValue;
                break;
            }

            $candidateUrl = $extractFirstUrl($optionValue);
            if ($candidateUrl !== '') {
                $sizeGuideUrl = $candidateUrl;
                break;
            }
        }
    }

    if ($sizeGuideUrl === '' || !$isAbsoluteUrl($sizeGuideUrl)) {
        $guideAttachment = DB::table('wp_posts')
            ->where('post_type', 'attachment')
            ->where('post_status', 'inherit')
            ->where(function ($query) {
                $query->where('post_title', 'like', '%size%guide%')
                    ->orWhere('post_title', 'like', '%size%chart%')
                    ->orWhere('post_title', 'like', '%دليل%المقاس%')
                    ->orWhere('post_name', 'like', '%size%guide%')
                    ->orWhere('post_name', 'like', '%size%chart%');
            })
            ->orderByDesc('ID')
            ->select('guid')
            ->first();

        if ($guideAttachment && !empty($guideAttachment->guid) && $isAbsoluteUrl((string) $guideAttachment->guid)) {
            $sizeGuideUrl = (string) $guideAttachment->guid;
        }
    }

    if ($sizeGuideUrl === '' || !$isAbsoluteUrl($sizeGuideUrl)) {
        $metaUrlCandidate = DB::table('wp_postmeta')
            ->where('post_id', (int) $product->ID)
            ->where(function ($query) {
                $query->where('meta_key', 'like', '%size%guide%')
                    ->orWhere('meta_key', 'like', '%size%chart%');
            })
            ->orderByDesc('meta_id')
            ->value('meta_value');

        $candidateUrl = $extractFirstUrl(is_scalar($metaUrlCandidate) ? (string) $metaUrlCandidate : '');
        if ($candidateUrl !== '') {
            $sizeGuideUrl = $candidateUrl;
        }
    }

    if ($sizeGuideUrl === '' || !$isAbsoluteUrl($sizeGuideUrl)) {
        $guidePage = DB::table('wp_posts')
            ->where('post_status', 'publish')
            ->whereIn('post_type', ['page', 'post'])
            ->where(function ($query) {
                $query->whereIn('post_name', ['size-guide', 'size-chart', 'dlyl-almqasat'])
                    ->orWhere('post_title', 'like', '%Size Guide%')
                    ->orWhere('post_title', 'like', '%Size Chart%')
                    ->orWhere('post_title', 'like', '%دليل المقاسات%');
            })
            ->orderByDesc('ID')
            ->select('post_name')
            ->first();

        if ($guidePage && !empty($guidePage->post_name)) {
            $sizeGuideUrl = $wpBaseUrl . '/' . trim((string) $guidePage->post_name, '/') . '/';
        }
    }

    if ($sizeGuideUrl === '' || !$isAbsoluteUrl($sizeGuideUrl)) {
        $sizeGuideUrl = '';
    }

    $attributeLabelMap = [];
    if (Schema::hasTable('wp_woocommerce_attribute_taxonomies')) {
        $attributeLabelRows = DB::table('wp_woocommerce_attribute_taxonomies')
            ->select('attribute_name', 'attribute_label')
            ->get();

        foreach ($attributeLabelRows as $attributeLabelRow) {
            $attributeLabelMap['pa_' . (string) $attributeLabelRow->attribute_name] = (string) $attributeLabelRow->attribute_label;
        }
    }

    $variationIds = DB::table('wp_posts')
        ->where('post_parent', (int) $product->ID)
        ->where('post_type', 'product_variation')
        ->whereIn('post_status', ['publish', 'private'])
        ->pluck('ID')
        ->map(fn ($id) => (int) $id)
        ->values();

    $hasVariations = $variationIds->isNotEmpty();
    $variationRules = [];
    $variationTaxonomyKeys = [];

    if ($hasVariations) {
        $variationMetaRows = DB::table('wp_postmeta')
            ->whereIn('post_id', $variationIds->all())
            ->where(function ($query) {
                $query->where('meta_key', 'like', 'attribute_pa\\_%')
                    ->orWhereIn('meta_key', ['_price', '_regular_price', '_sale_price', '_stock_status']);
            })
            ->select('post_id', 'meta_key', 'meta_value')
            ->get();

        $variationMap = [];
        foreach ($variationMetaRows as $variationMetaRow) {
            $variationId = (int) $variationMetaRow->post_id;
            $metaKey = (string) $variationMetaRow->meta_key;
            $metaValue = (string) ($variationMetaRow->meta_value ?? '');

            if (!isset($variationMap[$variationId])) {
                $variationMap[$variationId] = [
                    'variation_id' => $variationId,
                    'attributes' => [],
                    'price' => null,
                    'regular_price' => null,
                    'sale_price' => null,
                    'stock_status' => 'instock',
                ];
            }

            if (str_starts_with($metaKey, 'attribute_pa_')) {
                $variationMap[$variationId]['attributes'][$metaKey] = trim($metaValue);
                if (!in_array($metaKey, $variationTaxonomyKeys, true)) {
                    $variationTaxonomyKeys[] = $metaKey;
                }
                continue;
            }

            if (in_array($metaKey, ['_price', '_regular_price', '_sale_price'], true)) {
                $variationMap[$variationId][ltrim($metaKey, '_')] = $metaValue;
                continue;
            }

            if ($metaKey === '_stock_status') {
                $variationMap[$variationId]['stock_status'] = trim($metaValue) !== '' ? trim($metaValue) : 'instock';
            }
        }

        foreach ($variationMap as $variationItem) {
            if (empty($variationItem['attributes'])) {
                continue;
            }
            $variationRules[] = $variationItem;
        }
    }

    $selectionTaxonomies = $hasVariations ? $variationTaxonomyKeys : array_keys($attributeOptionsByTaxonomy);
    $selectionTaxonomies = array_values(array_unique(array_filter($selectionTaxonomies, fn ($value) => is_string($value) && $value !== '')));

    $productAttributesForSelection = [];
    foreach ($selectionTaxonomies as $selectionTaxonomy) {
        $options = $attributeOptionsByTaxonomy[$selectionTaxonomy] ?? [];
        if (empty($options) && $hasVariations) {
            $variationSlugs = [];
            foreach ($variationRules as $variationRule) {
                $candidateSlug = trim((string) ($variationRule['attributes'][$selectionTaxonomy] ?? ''));
                if ($candidateSlug !== '' && !in_array($candidateSlug, $variationSlugs, true)) {
                    $variationSlugs[] = $candidateSlug;
                }
            }
            foreach ($variationSlugs as $variationSlug) {
                $options[] = [
                    'slug' => $variationSlug,
                    'name' => strtoupper($variationSlug),
                ];
            }
        }

        if (empty($options)) {
            continue;
        }

        $localizedOptions = array_values(array_map(function ($option) use ($selectionTaxonomy, $translateWooAttributeValue) {
            $slug = trim((string) ($option['slug'] ?? ''));
            $name = trim((string) ($option['name'] ?? ''));
            $displayName = $translateWooAttributeValue($selectionTaxonomy, $slug, $name !== '' ? $name : strtoupper($slug));

            return [
                'slug' => $slug,
                'name' => $displayName,
            ];
        }, $options));

        $normalizedTaxonomyForLabel = $normalizeWooTaxonomyKey($selectionTaxonomy);
        $baseLabel = $attributeLabelMap[$normalizedTaxonomyForLabel] ?? ucwords(str_replace(['pa_', 'attribute_', '_', '-'], ['', '', ' ', ' '], $selectionTaxonomy));

        $productAttributesForSelection[] = [
            'taxonomy' => $selectionTaxonomy,
            'label' => $translateWooAttributeLabel($selectionTaxonomy, $baseLabel),
            'options' => $localizedOptions,
        ];
    }

    $productCategoryIds = DB::table('wp_term_relationships as tr')
        ->join('wp_term_taxonomy as tt', 'tr.term_taxonomy_id', '=', 'tt.term_taxonomy_id')
        ->where('tr.object_id', (int) $product->ID)
        ->where('tt.taxonomy', 'product_cat')
        ->pluck('tt.term_id')
        ->map(fn ($id) => (int) $id)
        ->filter(fn ($id) => $id > 0)
        ->unique()
        ->values();

    $relatedProducts = collect();
    if ($productCategoryIds->isNotEmpty()) {
        $relatedProducts = DB::table('wp_posts as p')
            ->join('wp_term_relationships as tr', 'p.ID', '=', 'tr.object_id')
            ->join('wp_term_taxonomy as tt', 'tr.term_taxonomy_id', '=', 'tt.term_taxonomy_id')
            ->leftJoin('wp_postmeta as price', function ($join) {
                $join->on('p.ID', '=', 'price.post_id')
                    ->where('price.meta_key', '_price');
            })
            ->leftJoin('wp_postmeta as regular', function ($join) {
                $join->on('p.ID', '=', 'regular.post_id')
                    ->where('regular.meta_key', '_regular_price');
            })
            ->leftJoin('wp_postmeta as thumb', function ($join) {
                $join->on('p.ID', '=', 'thumb.post_id')
                    ->where('thumb.meta_key', '_thumbnail_id');
            })
            ->leftJoin('wp_posts as img', 'thumb.meta_value', '=', 'img.ID')
            ->where('p.post_type', 'product')
            ->where('p.post_status', 'publish')
            ->where('p.ID', '!=', (int) $product->ID)
            ->where('tt.taxonomy', 'product_cat')
            ->whereIn('tt.term_id', $productCategoryIds->all())
            ->select(
                'p.ID',
                'p.post_title',
                'p.post_name',
                'price.meta_value as price',
                'regular.meta_value as regular_price',
                'img.guid as image'
            )
            ->distinct('p.ID')
            ->orderBy('p.post_date', 'desc')
            ->limit(8)
            ->get();

            $relatedProducts = $localizeProductsCollectionByWpml($relatedProducts, $currentLocale);
    }

    $viewData = [
        'product' => $product,
        'currentLocale' => $currentLocale,
        'localePrefix' => $localePrefix,
        'wpBaseUrl' => $wpBaseUrl,
        'material' => $material,
        'color' => $color,
        'condition' => $condition,
        'sizeValues' => $sizeValues,
        'deliveryIntro' => $deliveryIntro,
        'readyDelivery' => $readyDelivery,
        'customDelivery' => $customDelivery,
        'sizeGuideUrl' => $sizeGuideUrl,
        'hasVariations' => $hasVariations,
        'variationRules' => $variationRules,
        'productAttributesForSelection' => $productAttributesForSelection,
        'relatedProducts' => $relatedProducts,
        'galleryImages' => $galleryImages,
    ];

    return response()
        ->view('product-single', $viewData)
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
};

Route::get('/item/{slug}', fn (Request $request, string $slug) => $singleProductHandler($request, $slug, 'ar'));
Route::get('/ar/item/{slug}', fn (Request $request, string $slug) => $singleProductHandler($request, $slug, 'ar'));
Route::get('/en/item/{slug}', fn (Request $request, string $slug) => $singleProductHandler($request, $slug, 'en'));

Route::get('/debug/wpml-product/{slug}', function (Request $request, string $slug) use ($resolveWpmlProductLocalization) {
    $locale = strtolower((string) $request->query('locale', 'ar'));
    $locale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';

    $resolution = $resolveWpmlProductLocalization($slug, $locale);

    $wpmlRows = collect();
    if (!empty($resolution['trid']) && Schema::hasTable('wp_icl_translations')) {
        $wpmlRows = DB::table('wp_icl_translations')
            ->where('element_type', 'post_product')
            ->where('trid', (int) $resolution['trid'])
            ->orderBy('language_code')
            ->get(['element_id', 'language_code', 'source_language_code', 'trid']);
    }

    $candidateIds = collect([
        (int) ($resolution['base_product_id'] ?? 0),
        (int) ($resolution['localized_product_id'] ?? 0),
    ])->merge($wpmlRows->pluck('element_id')->map(fn ($id) => (int) $id))->filter(fn ($id) => $id > 0)->unique()->values();

    $posts = collect();
    if ($candidateIds->isNotEmpty()) {
        $posts = DB::table('wp_posts')
            ->whereIn('ID', $candidateIds->all())
            ->select('ID', 'post_title', 'post_name', 'post_status', 'post_type')
            ->orderBy('ID')
            ->get();
    }

    return response()->json([
        'locale' => $locale,
        'slug' => $slug,
        'resolution' => $resolution,
        'wpml_rows' => $wpmlRows,
        'posts' => $posts,
    ]);
});

$adsHandler = function (string $locale = 'ar') use ($localizeProductsCollectionByWpml) {
    $currentLocale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    $localePrefix = $currentLocale === 'en' ? '/en' : '/ar';

    $products = Cache::remember('ads_products_' . $currentLocale, 300, function () use ($currentLocale, $localizeProductsCollectionByWpml) {
        $rows = DB::table('wp_posts as p')
            ->leftJoin('wp_postmeta as price', function ($join) {
                $join->on('p.ID', '=', 'price.post_id')
                    ->where('price.meta_key', '_price');
            })
            ->leftJoin('wp_postmeta as regular', function ($join) {
                $join->on('p.ID', '=', 'regular.post_id')
                    ->where('regular.meta_key', '_regular_price');
            })
            ->leftJoin('wp_postmeta as thumb', function ($join) {
                $join->on('p.ID', '=', 'thumb.post_id')
                    ->where('thumb.meta_key', '_thumbnail_id');
            })
            ->leftJoin('wp_posts as img', 'thumb.meta_value', '=', 'img.ID')
            ->where('p.post_type', 'product')
            ->where('p.post_status', 'publish')
            ->orderBy('p.post_date', 'desc')
            ->select(
                'p.ID',
                'p.post_title',
                'p.post_name',
                'price.meta_value as price',
                'regular.meta_value as regular_price',
                'img.guid as image'
            )
            ->limit(12)
            ->get();

            return $localizeProductsCollectionByWpml($rows, $currentLocale);
    });

    $total = Cache::remember('ads_total_products', 300, function () {
        return DB::table('wp_posts')
            ->where('post_type', 'product')
            ->where('post_status', 'publish')
            ->count();
    });

    return view('ads-landing', compact('products', 'total', 'currentLocale', 'localePrefix'));
};

Route::get('/ads', fn () => $adsHandler('ar'));
Route::get('/ar/ads', fn () => $adsHandler('ar'));
Route::get('/en/ads', fn () => $adsHandler('en'));

$blogHandler = function (Request $request, string $locale = 'ar') {
    $currentLocale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    $localePrefix = $currentLocale === 'en' ? '/en' : '/ar';
    $wpBaseUrl = rtrim((string) (env('WP_PUBLIC_URL') ?: $request->getSchemeAndHttpHost()), '/');
    $arBlogArchivePath = env('WP_AR_BLOG_ARCHIVE_PATH', '/ar/%d9%85%d8%af%d9%88%d9%86%d8%a9/');
    $arBlogArchivePath = '/' . ltrim((string) $arBlogArchivePath, '/');
    $page = max(1, (int) $request->query('page', 1));
    $perPage = 9;

    $wpApiCandidates = $currentLocale === 'ar'
        ? [
            [$wpBaseUrl . '/wp-json/wp/v2/posts', ['lang' => 'ar']],
            [$wpBaseUrl . '/ar/wp-json/wp/v2/posts', []],
        ]
        : [
            [$wpBaseUrl . '/wp-json/wp/v2/posts', []],
        ];

    if ($currentLocale === 'ar') {
        $arArchiveUrl = rtrim($wpBaseUrl . $arBlogArchivePath, '/');
        if ($page > 1) {
            $arArchiveUrl .= '/page/' . $page . '/';
        } else {
            $arArchiveUrl .= '/';
        }

        try {
            $archiveResponse = Http::timeout(10)->get($arArchiveUrl);
            if ($archiveResponse->successful() && trim((string) $archiveResponse->body()) !== '') {
                $html = (string) $archiveResponse->body();
                $dom = new \DOMDocument();
                @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
                $xpath = new \DOMXPath($dom);

                $articleNodes = $xpath->query('//article');
                $items = collect();

                foreach ($articleNodes as $index => $article) {
                    $titleNode = $xpath->query('.//h1//a | .//h2//a | .//h3//a | .//*[contains(@class,"entry-title")]//a', $article)->item(0);
                    $link = $titleNode ? trim((string) $titleNode->getAttribute('href')) : '';
                    $title = $titleNode ? trim(html_entity_decode((string) $titleNode->textContent, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')) : '';

                    if ($link === '' || $title === '') {
                        continue;
                    }

                    if (!str_contains(mb_strtolower(rawurldecode($link)), '/ar/')) {
                        continue;
                    }

                    $timeNode = $xpath->query('.//time', $article)->item(0);
                    $dateValue = $timeNode ? (string) ($timeNode->getAttribute('datetime') ?: $timeNode->textContent) : '';

                    $excerptNode = $xpath->query('.//*[contains(@class,"entry-summary") or contains(@class,"entry-content") or contains(@class,"post-excerpt")]', $article)->item(0);
                    $excerpt = $excerptNode ? trim(html_entity_decode(strip_tags((string) $excerptNode->textContent), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')) : '';

                    $imgNode = $xpath->query('.//img[@src]', $article)->item(0);
                    $image = $imgNode ? trim((string) $imgNode->getAttribute('src')) : null;

                    $items->push((object) [
                        'ID' => $index + 1,
                        'post_title' => $title,
                        'post_name' => basename(parse_url($link, PHP_URL_PATH) ?: ''),
                        'post_excerpt' => $excerpt,
                        'post_content' => $excerpt,
                        'post_date' => $dateValue !== '' ? date('Y-m-d H:i:s', strtotime($dateValue)) : now()->toDateTimeString(),
                        'image' => $image,
                        'permalink' => $link,
                    ]);
                }

                if ($items->isNotEmpty()) {
                    $hasNextPage = $xpath->query('//a[contains(@class,"next") or contains(@class,"nextpostslink") or contains(@aria-label,"Next") or contains(@aria-label,"التالي")]')->length > 0;
                    $estimatedTotal = ($page - 1) * $perPage + $items->count() + ($hasNextPage ? 1 : 0);

                    $posts = new LengthAwarePaginator(
                        $items,
                        $estimatedTotal,
                        $perPage,
                        $page,
                        [
                            'path' => $request->url(),
                            'query' => $request->query(),
                        ]
                    );

                    return view('blog', compact('posts', 'currentLocale', 'localePrefix', 'wpBaseUrl', 'arBlogArchivePath'));
                }
            }
        } catch (\Throwable $exception) {
        }

        $arFeedCandidates = [
            rtrim($wpBaseUrl . $arBlogArchivePath, '/') . '/feed/',
            $wpBaseUrl . '/ar/feed/',
        ];

        foreach ($arFeedCandidates as $feedUrl) {
            try {
                $feedResponse = Http::timeout(8)->get($feedUrl);
                if (!$feedResponse->successful() || trim((string) $feedResponse->body()) === '') {
                    continue;
                }

                $feedXml = @simplexml_load_string((string) $feedResponse->body(), 'SimpleXMLElement', LIBXML_NOCDATA);
                if (!$feedXml || !isset($feedXml->channel->item)) {
                    continue;
                }

                $allItems = collect($feedXml->channel->item)->map(function ($item, $index) {
                    $title = html_entity_decode(trim((string) ($item->title ?? '')), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                    $link = trim((string) ($item->link ?? ''));
                    $description = html_entity_decode(strip_tags((string) ($item->description ?? '')), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                    $pubDate = trim((string) ($item->pubDate ?? ''));

                    $contentEncoded = '';
                    $namespaces = $item->getNameSpaces(true);
                    if (isset($namespaces['content'])) {
                        $contentNode = $item->children($namespaces['content']);
                        $contentEncoded = (string) ($contentNode->encoded ?? '');
                    }

                    $image = null;
                    if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $contentEncoded ?: (string) $item->description, $m)) {
                        $image = $m[1];
                    }

                    return (object) [
                        'ID' => $index + 1,
                        'post_title' => $title,
                        'post_name' => basename(parse_url($link, PHP_URL_PATH) ?: ''),
                        'post_excerpt' => trim($description),
                        'post_content' => trim(html_entity_decode(strip_tags($contentEncoded), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')),
                        'post_date' => $pubDate !== '' ? date('Y-m-d H:i:s', strtotime($pubDate)) : now()->toDateTimeString(),
                        'image' => $image,
                        'permalink' => $link,
                    ];
                })->filter(function ($post) {
                    return trim((string) ($post->post_title ?? '')) !== '';
                })->values();

                if ($allItems->isEmpty()) {
                    continue;
                }

                $total = $allItems->count();
                $offset = ($page - 1) * $perPage;
                $items = $allItems->slice($offset, $perPage)->values();

                $posts = new LengthAwarePaginator(
                    $items,
                    $total,
                    $perPage,
                    $page,
                    [
                        'path' => $request->url(),
                        'query' => $request->query(),
                    ]
                );

                return view('blog', compact('posts', 'currentLocale', 'localePrefix', 'wpBaseUrl'));
            } catch (\Throwable $exception) {
            }
        }
    }

    try {
        foreach ($wpApiCandidates as [$apiUrl, $extraParams]) {
            $apiResponse = Http::timeout(8)->get($apiUrl, array_merge([
                'per_page' => $perPage,
                'page' => $page,
                '_embed' => 1,
                'orderby' => 'date',
                'order' => 'desc',
                'status' => 'publish',
            ], $extraParams));

            if (!$apiResponse->successful()) {
                continue;
            }

            $postsData = collect($apiResponse->json());
            $items = $postsData->map(function ($post) {
                $embedded = $post['_embedded']['wp:featuredmedia'][0]['source_url'] ?? null;

                return (object) [
                    'ID' => (int) ($post['id'] ?? 0),
                    'post_title' => html_entity_decode(strip_tags((string) ($post['title']['rendered'] ?? '')), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                    'post_name' => (string) ($post['slug'] ?? ''),
                    'post_excerpt' => trim(html_entity_decode(strip_tags((string) ($post['excerpt']['rendered'] ?? '')), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')),
                    'post_content' => trim(html_entity_decode(strip_tags((string) ($post['content']['rendered'] ?? '')), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')),
                    'post_date' => (string) ($post['date'] ?? ''),
                    'image' => $embedded,
                    'permalink' => (string) ($post['link'] ?? ''),
                ];
            });

            if ($items->isEmpty()) {
                continue;
            }

            $total = $items->count();
            $posts = new LengthAwarePaginator(
                $items,
                $total,
                $perPage,
                $page,
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );

            return view('blog', compact('posts', 'currentLocale', 'localePrefix', 'wpBaseUrl', 'arBlogArchivePath'));
        }
    } catch (\Throwable $exception) {
    }

    $baseQuery = DB::table('wp_posts as p')
        ->leftJoin('wp_postmeta as thumb', function ($join) {
            $join->on('p.ID', '=', 'thumb.post_id')
                ->where('thumb.meta_key', '_thumbnail_id');
        })
        ->leftJoin('wp_posts as img', 'thumb.meta_value', '=', 'img.ID')
        ->where('p.post_type', 'post')
        ->where('p.post_status', 'publish')
        ->orderBy('p.post_date', 'desc')
        ->select(
            'p.ID',
            'p.post_title',
            'p.post_name',
            'p.post_excerpt',
            'p.post_content',
            'p.post_date',
            'img.guid as image'
        );

    $appliedLocaleFilter = false;

    if (Schema::hasTable('wp_icl_translations')) {
        $wpmlLocalizedCount = DB::table('wp_posts as p')
            ->join('wp_icl_translations as icl', function ($join) {
                $join->on('p.ID', '=', 'icl.element_id')
                    ->where('icl.element_type', 'post_post');
            })
            ->where('p.post_type', 'post')
            ->where('p.post_status', 'publish')
            ->where('icl.language_code', 'like', $currentLocale . '%')
            ->count('p.ID');

        if ($wpmlLocalizedCount > 0) {
            $baseQuery
                ->join('wp_icl_translations as icl', function ($join) {
                    $join->on('p.ID', '=', 'icl.element_id')
                        ->where('icl.element_type', 'post_post');
                })
                ->where('icl.language_code', 'like', $currentLocale . '%')
                ->distinct('p.ID');

            $appliedLocaleFilter = true;
        }
    }

    if (!$appliedLocaleFilter && Schema::hasTable('wp_term_relationships') && Schema::hasTable('wp_term_taxonomy') && Schema::hasTable('wp_terms')) {
        $localizedQuery = clone $baseQuery;
        $localizedPostsCount = $localizedQuery
            ->join('wp_term_relationships as tr', 'p.ID', '=', 'tr.object_id')
            ->join('wp_term_taxonomy as tt', function ($join) {
                $join->on('tr.term_taxonomy_id', '=', 'tt.term_taxonomy_id')
                    ->where('tt.taxonomy', 'language');
            })
            ->join('wp_terms as t', 'tt.term_id', '=', 't.term_id')
            ->where('t.slug', 'like', $currentLocale . '%')
            ->count('p.ID');

        if ($localizedPostsCount > 0) {
            $baseQuery
                ->join('wp_term_relationships as tr', 'p.ID', '=', 'tr.object_id')
                ->join('wp_term_taxonomy as tt', function ($join) {
                    $join->on('tr.term_taxonomy_id', '=', 'tt.term_taxonomy_id')
                        ->where('tt.taxonomy', 'language');
                })
                ->join('wp_terms as t', 'tt.term_id', '=', 't.term_id')
                ->where('t.slug', 'like', $currentLocale . '%')
                ->distinct('p.ID');
        }
    }

    $posts = $baseQuery->paginate($perPage);

    return view('blog', compact('posts', 'currentLocale', 'localePrefix', 'wpBaseUrl', 'arBlogArchivePath'));
};

Route::get('/blog', fn (Request $request) => $blogHandler($request, 'ar'));
Route::get('/ar/blog', fn (Request $request) => $blogHandler($request, 'ar'));
Route::get('/en/blog', fn (Request $request) => $blogHandler($request, 'en'));

$contactHandler = function (string $locale = 'ar') {
    $currentLocale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    $localePrefix = $currentLocale === 'en' ? '/en' : '/ar';

    return view('contact', compact('currentLocale', 'localePrefix'));
};

Route::get('/contact-us', fn () => $contactHandler('ar'));
Route::get('/ar/contact-us', fn () => $contactHandler('ar'));
Route::get('/en/contact-us', fn () => $contactHandler('en'));

$aboutHandler = function (Request $request, string $locale = 'ar') {
    $currentLocale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    $localePrefix = $currentLocale === 'en' ? '/en' : '/ar';
    $wpBaseUrl = rtrim((string) (env('WP_PUBLIC_URL') ?: $request->getSchemeAndHttpHost()), '/');

    return view('about', compact('currentLocale', 'localePrefix', 'wpBaseUrl'));
};

Route::get('/about-us', fn (Request $request) => $aboutHandler($request, 'ar'));
Route::get('/ar/about-us', fn (Request $request) => $aboutHandler($request, 'ar'));
Route::get('/en/about-us', fn (Request $request) => $aboutHandler($request, 'en'));

$privacyHandler = function (Request $request, string $locale = 'ar') {
    $currentLocale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    $localePrefix = $currentLocale === 'en' ? '/en' : '/ar';
    $wpBaseUrl = rtrim((string) (env('WP_PUBLIC_URL') ?: $request->getSchemeAndHttpHost()), '/');

    return view('privacy-policy', compact('currentLocale', 'localePrefix', 'wpBaseUrl'));
};

Route::get('/privacy-policy', fn (Request $request) => $privacyHandler($request, 'ar'));
Route::get('/ar/privacy-policy', fn (Request $request) => $privacyHandler($request, 'ar'));
Route::get('/en/privacy-policy', fn (Request $request) => $privacyHandler($request, 'en'));
Route::get('/ar/سياسة-الخصوصية', fn (Request $request) => $privacyHandler($request, 'ar'));

$termsHandler = function (Request $request, string $locale = 'ar') {
    $currentLocale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    $localePrefix = $currentLocale === 'en' ? '/en' : '/ar';
    $wpBaseUrl = rtrim((string) (env('WP_PUBLIC_URL') ?: $request->getSchemeAndHttpHost()), '/');

    return view('terms-conditions', compact('currentLocale', 'localePrefix', 'wpBaseUrl'));
};

Route::get('/terms-conditions', fn (Request $request) => $termsHandler($request, 'ar'));
Route::get('/ar/terms-conditions', fn (Request $request) => $termsHandler($request, 'ar'));
Route::get('/en/terms-conditions', fn (Request $request) => $termsHandler($request, 'en'));

$marketplacePolicyHandler = function (Request $request, string $locale = 'ar') {
    $currentLocale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    $localePrefix = $currentLocale === 'en' ? '/en' : '/ar';
    $wpBaseUrl = rtrim((string) (env('WP_PUBLIC_URL') ?: $request->getSchemeAndHttpHost()), '/');

    return view('marketplace-policy', compact('currentLocale', 'localePrefix', 'wpBaseUrl'));
};

Route::get('/marketplace-policy', fn (Request $request) => $marketplacePolicyHandler($request, 'ar'));
Route::get('/ar/marketplace-policy', fn (Request $request) => $marketplacePolicyHandler($request, 'ar'));
Route::get('/en/marketplace-policy', fn (Request $request) => $marketplacePolicyHandler($request, 'en'));
Route::get('/Marketplace-Policy', fn (Request $request) => $marketplacePolicyHandler($request, 'en'));
Route::get('/Marketplace-Policy/', fn (Request $request) => $marketplacePolicyHandler($request, 'en'));

$refundReturnPolicyHandler = function (Request $request, string $locale = 'ar') {
    $currentLocale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    $localePrefix = $currentLocale === 'en' ? '/en' : '/ar';
    $wpBaseUrl = rtrim((string) (env('WP_PUBLIC_URL') ?: $request->getSchemeAndHttpHost()), '/');

    return view('refund-return-policy', compact('currentLocale', 'localePrefix', 'wpBaseUrl'));
};

Route::get('/refund-return-policy', fn (Request $request) => $refundReturnPolicyHandler($request, 'ar'));
Route::get('/ar/refund-return-policy', fn (Request $request) => $refundReturnPolicyHandler($request, 'ar'));
Route::get('/en/refund-return-policy', fn (Request $request) => $refundReturnPolicyHandler($request, 'en'));
Route::get('/Refund-Return-Policy', fn (Request $request) => $refundReturnPolicyHandler($request, 'en'));
Route::get('/Refund-Return-Policy/', fn (Request $request) => $refundReturnPolicyHandler($request, 'en'));

$faqHandler = function (Request $request, string $locale = 'ar') {
    $currentLocale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    $localePrefix = $currentLocale === 'en' ? '/en' : '/ar';
    $wpBaseUrl = rtrim((string) (env('WP_PUBLIC_URL') ?: $request->getSchemeAndHttpHost()), '/');

    return view('faq', compact('currentLocale', 'localePrefix', 'wpBaseUrl'));
};

Route::get('/faq', fn (Request $request) => $faqHandler($request, 'ar'));
Route::get('/ar/faq', fn (Request $request) => $faqHandler($request, 'ar'));
Route::get('/en/faq', fn (Request $request) => $faqHandler($request, 'en'));
Route::get('/styliiiish-faq', fn (Request $request) => $faqHandler($request, 'en'));
Route::get('/styliiiish-faq/', fn (Request $request) => $faqHandler($request, 'en'));

$shippingPolicyHandler = function (Request $request, string $locale = 'ar') {
    $currentLocale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    $localePrefix = $currentLocale === 'en' ? '/en' : '/ar';
    $wpBaseUrl = rtrim((string) (env('WP_PUBLIC_URL') ?: $request->getSchemeAndHttpHost()), '/');

    return view('shipping-delivery-policy', compact('currentLocale', 'localePrefix', 'wpBaseUrl'));
};

Route::get('/shipping-delivery-policy', fn (Request $request) => $shippingPolicyHandler($request, 'ar'));
Route::get('/ar/shipping-delivery-policy', fn (Request $request) => $shippingPolicyHandler($request, 'ar'));
Route::get('/en/shipping-delivery-policy', fn (Request $request) => $shippingPolicyHandler($request, 'en'));
Route::get('/shipping-delivery-policy/', fn (Request $request) => $shippingPolicyHandler($request, 'en'));

$cookiePolicyHandler = function (Request $request, string $locale = 'ar') {
    $currentLocale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    $localePrefix = $currentLocale === 'en' ? '/en' : '/ar';
    $wpBaseUrl = rtrim((string) (env('WP_PUBLIC_URL') ?: $request->getSchemeAndHttpHost()), '/');

    return view('cookie-policy', compact('currentLocale', 'localePrefix', 'wpBaseUrl'));
};

Route::get('/cookie-policy', fn (Request $request) => $cookiePolicyHandler($request, 'ar'));
Route::get('/ar/cookie-policy', fn (Request $request) => $cookiePolicyHandler($request, 'ar'));
Route::get('/en/cookie-policy', fn (Request $request) => $cookiePolicyHandler($request, 'en'));
Route::get('/🍪-cookie-policy', fn (Request $request) => $cookiePolicyHandler($request, 'en'));
Route::get('/🍪-cookie-policy/', fn (Request $request) => $cookiePolicyHandler($request, 'en'));

$categoriesHandler = function (Request $request, string $locale = 'ar') {
    $currentLocale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    $localePrefix = $currentLocale === 'en' ? '/en' : '/ar';
    $wpBaseUrl = rtrim((string) (env('WP_PUBLIC_URL') ?: $request->getSchemeAndHttpHost()), '/');

    $categories = DB::table('wp_terms as t')
        ->join('wp_term_taxonomy as tt', 't.term_id', '=', 'tt.term_id')
        ->leftJoin('wp_termmeta as tm', function ($join) {
            $join->on('t.term_id', '=', 'tm.term_id')
                ->where('tm.meta_key', 'thumbnail_id');
        })
        ->leftJoin('wp_posts as img', 'tm.meta_value', '=', 'img.ID')
        ->where('tt.taxonomy', 'product_cat')
        ->where('tt.count', '>', 0)
        ->where('t.slug', '!=', 'uncategorized')
        ->orderByDesc('tt.count')
        ->orderBy('t.name')
        ->select(
            't.term_id',
            't.name',
            't.slug',
            'tt.description',
            'tt.count as products_count',
            'img.guid as image'
        )
        ->get();

    return view('categories', compact('categories', 'currentLocale', 'localePrefix', 'wpBaseUrl'));
};

Route::get('/categories', fn (Request $request) => $categoriesHandler($request, 'ar'));
Route::get('/ar/categories', fn (Request $request) => $categoriesHandler($request, 'ar'));
Route::get('/en/categories', fn (Request $request) => $categoriesHandler($request, 'en'));
Route::get('/categories/', fn (Request $request) => $categoriesHandler($request, 'en'));

$marketplaceHandler = function (Request $request, string $locale = 'ar') use ($localizeProductsCollectionByWpml) {
    $currentLocale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    $localePrefix = $currentLocale === 'en' ? '/en' : '/ar';
    $wpBaseUrl = rtrim((string) (env('WP_PUBLIC_URL') ?: $request->getSchemeAndHttpHost()), '/');
    $search = trim((string) $request->query('q', ''));
    $sort = (string) $request->query('sort', 'newest');

    if (!in_array($sort, ['newest', 'price_asc', 'price_desc'], true)) {
        $sort = 'newest';
    }

    $preferredCategoryIds = DB::table('wp_terms as t')
        ->join('wp_term_taxonomy as tt', 't.term_id', '=', 'tt.term_id')
        ->where('tt.taxonomy', 'product_cat')
        ->whereIn('t.slug', ['used-dress', 'used-dresses', 'marketplace', 'marketplace-dresses'])
        ->pluck('t.term_id')
        ->map(fn ($id) => (int) $id)
        ->filter(fn ($id) => $id > 0)
        ->unique()
        ->values();

    if ($preferredCategoryIds->isEmpty()) {
        $preferredCategoryIds = DB::table('wp_terms as t')
            ->join('wp_term_taxonomy as tt', 't.term_id', '=', 'tt.term_id')
            ->where('tt.taxonomy', 'product_cat')
            ->where(function ($query) {
                $query->where('t.name', 'like', '%used%')
                    ->orWhere('t.name', 'like', '%market%')
                    ->orWhere('t.name', 'like', '%مستعمل%')
                    ->orWhere('t.name', 'like', '%ماركت%');
            })
            ->pluck('t.term_id')
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();
    }

    $query = DB::table('wp_posts as p')
        ->join('wp_term_relationships as tr', 'p.ID', '=', 'tr.object_id')
        ->join('wp_term_taxonomy as tt', 'tr.term_taxonomy_id', '=', 'tt.term_taxonomy_id')
        ->leftJoin('wp_postmeta as price', function ($join) {
            $join->on('p.ID', '=', 'price.post_id')
                ->where('price.meta_key', '_price');
        })
        ->leftJoin('wp_postmeta as regular', function ($join) {
            $join->on('p.ID', '=', 'regular.post_id')
                ->where('regular.meta_key', '_regular_price');
        })
        ->leftJoin('wp_postmeta as sale', function ($join) {
            $join->on('p.ID', '=', 'sale.post_id')
                ->where('sale.meta_key', '_sale_price');
        })
        ->leftJoin('wp_postmeta as thumb', function ($join) {
            $join->on('p.ID', '=', 'thumb.post_id')
                ->where('thumb.meta_key', '_thumbnail_id');
        })
        ->leftJoin('wp_posts as img', 'thumb.meta_value', '=', 'img.ID')
        ->where('p.post_type', 'product')
        ->where('p.post_status', 'publish')
        ->where('tt.taxonomy', 'product_cat');

    if ($preferredCategoryIds->isNotEmpty()) {
        $query->whereIn('tt.term_id', $preferredCategoryIds->all());
    } else {
        $query->whereRaw('1 = 0');
    }

    if ($search !== '') {
        $query->where('p.post_title', 'like', "%{$search}%");
    }

    if ($sort === 'price_asc') {
        $query->orderByRaw('CAST(NULLIF(price.meta_value, "") AS DECIMAL(10,2)) ASC')
            ->orderBy('p.post_date', 'desc');
    } elseif ($sort === 'price_desc') {
        $query->orderByRaw('CAST(NULLIF(price.meta_value, "") AS DECIMAL(10,2)) DESC')
            ->orderBy('p.post_date', 'desc');
    } else {
        $query->orderBy('p.post_date', 'desc');
    }

    $products = $query
        ->select(
            'p.ID',
            'p.post_title',
            'p.post_name',
            'price.meta_value as price',
            'regular.meta_value as regular_price',
            'sale.meta_value as sale_price',
            'img.guid as image',
            'p.post_date'
        )
        ->distinct('p.ID')
        ->paginate(16)
        ->withQueryString();

    $products->setCollection($localizeProductsCollectionByWpml($products->getCollection(), $currentLocale));

    return view('marketplace', compact('products', 'search', 'sort', 'currentLocale', 'localePrefix', 'wpBaseUrl'));
};

Route::get('/marketplace', fn (Request $request) => $marketplaceHandler($request, 'ar'));
Route::get('/ar/marketplace', fn (Request $request) => $marketplaceHandler($request, 'ar'));
Route::get('/en/marketplace', fn (Request $request) => $marketplaceHandler($request, 'en'));
Route::get('/marketplace/', fn (Request $request) => $marketplaceHandler($request, 'en'));

Route::get('/favicon.ico', function (Request $request) {
    $wpBaseUrl = rtrim((string) (env('WP_PUBLIC_URL') ?: $request->getSchemeAndHttpHost()), '/');
    return redirect()->away($wpBaseUrl . '/wp-content/uploads/2025/11/cropped-ChatGPT-Image-Nov-2-2025-03_11_14-AM-e1762046066547.png');
});