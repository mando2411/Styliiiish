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

$normalizeBrandByLocale = function (?string $text, string $locale): string {
    $value = (string) ($text ?? '');
    return $value;
};

$resolveTranslatePressLanguageCodes = function (string $locale): ?array {
    static $cachedSettings = null;

    if ($cachedSettings === null) {
        $settingsOption = DB::table('wp_options')
            ->where('option_name', 'trp_settings')
            ->value('option_value');

        $parsed = @unserialize((string) $settingsOption);
        $cachedSettings = is_array($parsed) ? $parsed : [];
    }

    $defaultLanguage = (string) ($cachedSettings['default-language'] ?? '');
    $translationLanguages = collect($cachedSettings['translation-languages'] ?? [])
        ->filter(fn ($value) => is_string($value) && trim($value) !== '')
        ->map(fn ($value) => trim((string) $value))
        ->values();

    if ($defaultLanguage === '' || $translationLanguages->isEmpty()) {
        return null;
    }

    $locale = strtolower(trim($locale));
    $preferredPrefix = $locale === 'en' ? 'en' : 'ar';

    $targetLanguage = $translationLanguages->first(function ($languageCode) use ($preferredPrefix) {
        $code = strtolower((string) $languageCode);
        return str_starts_with($code, $preferredPrefix);
    });

    if (!$targetLanguage) {
        return null;
    }

    return [
        'default' => strtolower($defaultLanguage),
        'target' => strtolower((string) $targetLanguage),
    ];
};

$localizeProductsCollectionByTranslatePress = function ($rows, string $locale, bool $includeContentFields = false) use ($resolveTranslatePressLanguageCodes, $normalizeBrandByLocale) {
    $collection = collect($rows);
    if ($collection->isEmpty()) {
        return $collection;
    }

    $getField = function ($row, string $field): string {
        if (is_array($row)) {
            return trim((string) ($row[$field] ?? ''));
        }

        if (is_object($row)) {
            return trim((string) ($row->{$field} ?? ''));
        }

        return '';
    };

    $setField = function (&$row, string $field, string $value): void {
        if (is_array($row)) {
            $row[$field] = $value;
            return;
        }

        if (is_object($row)) {
            $row->{$field} = $value;
        }
    };

    $languageCodes = $resolveTranslatePressLanguageCodes($locale);
    if (!$languageCodes) {
        return $collection;
    }

    $defaultLanguage = (string) ($languageCodes['default'] ?? '');
    $targetLanguage = (string) ($languageCodes['target'] ?? '');

    if ($defaultLanguage === '' || $targetLanguage === '' || $defaultLanguage === $targetLanguage) {
        return $collection;
    }

    $dictionaryTable = 'wp_trp_dictionary_' . $defaultLanguage . '_' . $targetLanguage;
    if (!Schema::hasTable($dictionaryTable)) {
        return $collection;
    }

    $lookupStrings = $collection->flatMap(function ($row) use ($includeContentFields, $getField) {
        $strings = [];
        $title = $getField($row, 'post_title');
        if ($title !== '') {
            $strings[] = $title;
        }

        if ($includeContentFields) {
            $excerpt = $getField($row, 'post_excerpt');
            if ($excerpt !== '') {
                $strings[] = $excerpt;
            }

            $content = $getField($row, 'post_content');
            if ($content !== '') {
                $strings[] = $content;
            }
        }

        return $strings;
    })->map(fn ($value) => (string) $value)->filter(fn ($value) => $value !== '')->unique()->values();

    if ($lookupStrings->isEmpty()) {
        return $collection;
    }

    $dictionaryRows = DB::table($dictionaryTable)
        ->whereIn('original', $lookupStrings->all())
        ->where('status', '!=', 0)
        ->whereNotNull('translated')
        ->where('translated', '!=', '')
        ->select('original', 'translated')
        ->get();

    if ($dictionaryRows->isEmpty()) {
        return $collection;
    }

    $translationMap = [];
    foreach ($dictionaryRows as $dictionaryRow) {
        $original = trim((string) ($dictionaryRow->original ?? ''));
        $translated = trim((string) ($dictionaryRow->translated ?? ''));
        if ($original !== '' && $translated !== '' && !array_key_exists($original, $translationMap)) {
            $translationMap[$original] = $translated;
        }
    }

    if (empty($translationMap)) {
        return $collection;
    }

    return $collection->map(function ($row) use ($translationMap, $includeContentFields, $getField, $setField, $normalizeBrandByLocale, $locale) {
        $title = $getField($row, 'post_title');
        if ($title !== '' && isset($translationMap[$title])) {
            $setField($row, 'post_title', $normalizeBrandByLocale((string) $translationMap[$title], $locale));
        } elseif ($title !== '') {
            $setField($row, 'post_title', $normalizeBrandByLocale($title, $locale));
        }

        if ($includeContentFields) {
            $excerpt = $getField($row, 'post_excerpt');
            if ($excerpt !== '' && isset($translationMap[$excerpt])) {
                $setField($row, 'post_excerpt', $normalizeBrandByLocale((string) $translationMap[$excerpt], $locale));
            }

            $content = $getField($row, 'post_content');
            if ($content !== '' && isset($translationMap[$content])) {
                $setField($row, 'post_content', $normalizeBrandByLocale((string) $translationMap[$content], $locale));
            }
        }

        return $row;
    });
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

$localizeProductsCollectionByWpml = function ($rows, string $locale, bool $includeContentFields = false) use ($mapLocaleToWpmlCode, $localizeProductsCollectionByTranslatePress, $normalizeBrandByLocale) {
    $collection = collect($rows);

    if ($collection->isEmpty() || !Schema::hasTable('wp_icl_translations')) {
        return $localizeProductsCollectionByTranslatePress($collection, $locale, $includeContentFields);
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
        return $localizeProductsCollectionByTranslatePress($collection, $locale, $includeContentFields);
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
        return $localizeProductsCollectionByTranslatePress($collection, $locale, $includeContentFields);
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
        return $localizeProductsCollectionByTranslatePress($collection, $locale, $includeContentFields);
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
        $row->post_title = $normalizeBrandByLocale((string) ($localizedPost->post_title ?? $row->post_title ?? ''), $locale);
        $row->post_name = (string) ($localizedPost->post_name ?? $row->post_name ?? '');

        if ($includeContentFields) {
            if (property_exists($row, 'post_excerpt')) {
                $row->post_excerpt = $normalizeBrandByLocale((string) ($localizedPost->post_excerpt ?? $row->post_excerpt ?? ''), $locale);
            }
            if (property_exists($row, 'post_content')) {
                $row->post_content = $normalizeBrandByLocale((string) ($localizedPost->post_content ?? $row->post_content ?? ''), $locale);
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
        glob(public_path('google-reviews/*/*.{png,jpg,jpeg,webp,avif,gif}'), GLOB_BRACE) ?: [],
        glob(base_path('Google Reviews/*.{png,jpg,jpeg,webp,avif,gif}'), GLOB_BRACE) ?: []
    ))
        ->filter(fn ($path) => is_file($path))
        ->unique(fn ($path) => mb_strtolower(basename($path)))
        ->sortBy(fn ($path) => basename($path), SORT_NATURAL | SORT_FLAG_CASE)
        ->values();

    $publicRoot = realpath(public_path());

    $reviewImages = $reviewFiles
        ->map(function ($path) use ($publicRoot) {
            $version = @filemtime($path) ?: time();

            $realPath = realpath($path);
            if ($publicRoot !== false && $realPath !== false && strpos($realPath, $publicRoot) === 0) {
                $relative = ltrim(str_replace('\\', '/', substr($realPath, strlen($publicRoot))), '/');
                $encodedRelative = implode('/', array_map('rawurlencode', explode('/', $relative)));
                return '/' . $encodedRelative . '?v=' . $version;
            }

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

            $products = collect($products)->shuffle()->values();

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

$shopDataHandler = function (Request $request, string $locale = 'ar') use ($localizeProductsCollectionByWpml, $resolveTranslatePressLanguageCodes) {
    $search = trim((string) $request->query('q', ''));
    $sort = (string) $request->query('sort', 'newest');

    $normalizeSearchText = static function (?string $value): string {
        $text = mb_strtolower(trim((string) $value), 'UTF-8');
        $text = str_replace(['أ', 'إ', 'آ', 'ى', 'ؤ', 'ئ', 'ة'], ['ا', 'ا', 'ا', 'ي', 'و', 'ي', 'ه'], $text);
        $text = preg_replace('/[\x{064B}-\x{065F}\x{0670}\x{06D6}-\x{06ED}]/u', '', $text) ?? $text;
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;
        return trim($text);
    };

    if (!in_array($sort, ['newest', 'best_selling', 'top_rated', 'discount_desc', 'price_asc', 'price_desc', 'random'], true)) {
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
        ->leftJoin('wp_postmeta as sales', function ($join) {
            $join->on('p.ID', '=', 'sales.post_id')
                ->where('sales.meta_key', 'total_sales');
        })
        ->leftJoin('wp_postmeta as rating', function ($join) {
            $join->on('p.ID', '=', 'rating.post_id')
                ->where('rating.meta_key', '_wc_average_rating');
        })
        ->leftJoin('wp_postmeta as thumb', function ($join) {
            $join->on('p.ID', '=', 'thumb.post_id')
                ->where('thumb.meta_key', '_thumbnail_id');
        })
        ->leftJoin('wp_posts as img', 'thumb.meta_value', '=', 'img.ID')
        ->where('p.post_type', 'product')
        ->where('p.post_status', 'publish');

    if ($sort === 'newest') {
        $query->orderBy('p.post_date', 'desc');
    } elseif ($sort === 'best_selling') {
        $query->orderByRaw('CAST(COALESCE(NULLIF(sales.meta_value, ""), "0") AS UNSIGNED) DESC')
            ->orderBy('p.post_date', 'desc');
    } elseif ($sort === 'top_rated') {
        $query->orderByRaw('CAST(COALESCE(NULLIF(rating.meta_value, ""), "0") AS DECIMAL(4,2)) DESC')
            ->orderByRaw('CAST(COALESCE(NULLIF(sales.meta_value, ""), "0") AS UNSIGNED) DESC')
            ->orderBy('p.post_date', 'desc');
    } elseif ($sort === 'discount_desc') {
        $query->orderByRaw('CASE WHEN CAST(COALESCE(NULLIF(regular.meta_value, ""), "0") AS DECIMAL(10,2)) > 0 AND CAST(COALESCE(NULLIF(price.meta_value, ""), "0") AS DECIMAL(10,2)) > 0 AND CAST(COALESCE(NULLIF(regular.meta_value, ""), "0") AS DECIMAL(10,2)) > CAST(COALESCE(NULLIF(price.meta_value, ""), "0") AS DECIMAL(10,2)) THEN ((CAST(COALESCE(NULLIF(regular.meta_value, ""), "0") AS DECIMAL(10,2)) - CAST(COALESCE(NULLIF(price.meta_value, ""), "0") AS DECIMAL(10,2))) / CAST(COALESCE(NULLIF(regular.meta_value, ""), "0") AS DECIMAL(10,2))) ELSE 0 END DESC')
            ->orderBy('p.post_date', 'desc');
    } elseif ($sort === 'price_asc') {
        $query->orderByRaw('CAST(NULLIF(price.meta_value, "") AS DECIMAL(10,2)) ASC')
            ->orderBy('p.post_date', 'desc');
    } elseif ($sort === 'price_desc') {
        $query->orderByRaw('CAST(NULLIF(price.meta_value, "") AS DECIMAL(10,2)) DESC')
            ->orderBy('p.post_date', 'desc');
    } elseif ($sort === 'random') {
        $query->inRandomOrder();
    }

    $products = $query->select(
        'p.ID',
        'p.post_title',
        'p.post_name',
        'p.post_excerpt',
        'p.post_content',
        'price.meta_value as price',
        'regular.meta_value as regular_price',
        'img.guid as image'
    )->get();

    $products = $localizeProductsCollectionByWpml($products, $locale, true);

    if ($search !== '') {
        $needle = $normalizeSearchText($search);

        if ($needle !== '' && $products->isNotEmpty()) {
            $productIds = $products->pluck('ID')
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => $id > 0)
                ->values();

            $categoryNamesByProduct = collect();
            $attributeNamesByProduct = collect();

            if ($productIds->isNotEmpty()) {
                $baseCategoryRows = DB::table('wp_term_relationships as rel')
                    ->join('wp_term_taxonomy as tt', function ($join) {
                        $join->on('rel.term_taxonomy_id', '=', 'tt.term_taxonomy_id')
                            ->where('tt.taxonomy', '=', 'product_cat');
                    })
                    ->join('wp_terms as t', 'tt.term_id', '=', 't.term_id')
                    ->whereIn('rel.object_id', $productIds->all())
                    ->select('rel.object_id as product_id', 't.name as category_name')
                    ->get();

                $categoryNamesByProduct = $baseCategoryRows
                    ->groupBy('product_id')
                    ->map(fn ($rows) => $rows->pluck('category_name')->filter()->values());

                $baseAttributeRows = DB::table('wp_term_relationships as rel')
                    ->join('wp_term_taxonomy as tt', function ($join) {
                        $join->on('rel.term_taxonomy_id', '=', 'tt.term_taxonomy_id')
                            ->where('tt.taxonomy', 'like', 'pa\\_%');
                    })
                    ->join('wp_terms as t', 'tt.term_id', '=', 't.term_id')
                    ->whereIn('rel.object_id', $productIds->all())
                    ->select('rel.object_id as product_id', 't.name as attribute_name')
                    ->get();

                $attributeNamesByProduct = $baseAttributeRows
                    ->groupBy('product_id')
                    ->map(fn ($rows) => $rows->pluck('attribute_name')->filter()->values());

                if (Schema::hasTable('wp_icl_translations')) {
                    $translatedCategoryRows = DB::table('wp_term_relationships as rel')
                        ->join('wp_term_taxonomy as tt', function ($join) {
                            $join->on('rel.term_taxonomy_id', '=', 'tt.term_taxonomy_id')
                                ->where('tt.taxonomy', '=', 'product_cat');
                        })
                        ->join('wp_icl_translations as src_i18n', function ($join) {
                            $join->on('src_i18n.element_id', '=', 'tt.term_taxonomy_id')
                                ->where('src_i18n.element_type', '=', 'tax_product_cat');
                        })
                        ->join('wp_icl_translations as tr_i18n', function ($join) {
                            $join->on('tr_i18n.trid', '=', 'src_i18n.trid')
                                ->where('tr_i18n.element_type', '=', 'tax_product_cat');
                        })
                        ->join('wp_term_taxonomy as tt_tr', 'tt_tr.term_taxonomy_id', '=', 'tr_i18n.element_id')
                        ->join('wp_terms as t_tr', 'tt_tr.term_id', '=', 't_tr.term_id')
                        ->whereIn('rel.object_id', $productIds->all())
                        ->select('rel.object_id as product_id', 't_tr.name as category_name')
                        ->get();

                    if ($translatedCategoryRows->isNotEmpty()) {
                        $translatedMap = $translatedCategoryRows
                            ->groupBy('product_id')
                            ->map(fn ($rows) => $rows->pluck('category_name')->filter()->values());

                        $categoryNamesByProduct = $categoryNamesByProduct
                            ->union($translatedMap)
                            ->map(function ($names, $productId) use ($translatedMap) {
                                $extra = $translatedMap->get($productId, collect());
                                return collect($names)->merge($extra)->filter()->unique()->values();
                            });
                    }
                }

                $languageCodes = $resolveTranslatePressLanguageCodes($locale);
                if ($languageCodes) {
                    $defaultLanguage = (string) ($languageCodes['default'] ?? '');
                    $targetLanguage = (string) ($languageCodes['target'] ?? '');

                    if ($defaultLanguage !== '' && $targetLanguage !== '' && $defaultLanguage !== $targetLanguage) {
                        $dictionaryTable = 'wp_trp_dictionary_' . $defaultLanguage . '_' . $targetLanguage;
                        if (Schema::hasTable($dictionaryTable)) {
                            $lookupTerms = $categoryNamesByProduct
                                ->flatMap(fn ($items) => collect($items))
                                ->merge($attributeNamesByProduct->flatMap(fn ($items) => collect($items)))
                                ->map(fn ($value) => trim((string) $value))
                                ->filter(fn ($value) => $value !== '')
                                ->unique()
                                ->values();

                            if ($lookupTerms->isNotEmpty()) {
                                $dictionaryRows = DB::table($dictionaryTable)
                                    ->whereIn('original', $lookupTerms->all())
                                    ->where('status', '!=', 0)
                                    ->whereNotNull('translated')
                                    ->where('translated', '!=', '')
                                    ->select('original', 'translated')
                                    ->get();

                                if ($dictionaryRows->isNotEmpty()) {
                                    $termTranslationMap = [];
                                    foreach ($dictionaryRows as $dictionaryRow) {
                                        $original = trim((string) ($dictionaryRow->original ?? ''));
                                        $translated = trim((string) ($dictionaryRow->translated ?? ''));
                                        if ($original !== '' && $translated !== '' && !array_key_exists($original, $termTranslationMap)) {
                                            $termTranslationMap[$original] = $translated;
                                        }
                                    }

                                    if (!empty($termTranslationMap)) {
                                        $translateItems = function ($items) use ($termTranslationMap) {
                                            return collect($items)
                                                ->map(function ($item) use ($termTranslationMap) {
                                                    $value = trim((string) $item);
                                                    return $termTranslationMap[$value] ?? $value;
                                                })
                                                ->filter()
                                                ->unique()
                                                ->values();
                                        };

                                        $categoryNamesByProduct = $categoryNamesByProduct
                                            ->map(fn ($items) => $translateItems($items));

                                        $attributeNamesByProduct = $attributeNamesByProduct
                                            ->map(fn ($items) => $translateItems($items));
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $products = $products->filter(function ($product) use ($needle, $normalizeSearchText, $categoryNamesByProduct, $attributeNamesByProduct) {
                $title = $normalizeSearchText((string) ($product->post_title ?? ''));
                if ($title !== '' && str_contains($title, $needle)) {
                    return true;
                }

                $excerpt = $normalizeSearchText((string) ($product->post_excerpt ?? ''));
                if ($excerpt !== '' && str_contains($excerpt, $needle)) {
                    return true;
                }

                $content = $normalizeSearchText((string) ($product->post_content ?? ''));
                if ($content !== '' && str_contains($content, $needle)) {
                    return true;
                }

                $categoryNames = $categoryNamesByProduct->get((int) ($product->ID ?? 0), collect());
                foreach ($categoryNames as $categoryName) {
                    $normalizedCategory = $normalizeSearchText((string) $categoryName);
                    if ($normalizedCategory !== '' && str_contains($normalizedCategory, $needle)) {
                        return true;
                    }
                }

                $attributeNames = $attributeNamesByProduct->get((int) ($product->ID ?? 0), collect());
                foreach ($attributeNames as $attributeName) {
                    $normalizedAttribute = $normalizeSearchText((string) $attributeName);
                    if ($normalizedAttribute !== '' && str_contains($normalizedAttribute, $needle)) {
                        return true;
                    }
                }

                return false;
            })->values();
        }
    }

    return [$products, $search, $sort];
};

$shopHandler = function (Request $request, string $locale = 'ar') use ($shopDataHandler) {
    $currentLocale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    $localePrefix = $currentLocale === 'en' ? '/en' : '/ar';

    [$products, $search, $sort] = $shopDataHandler($request, $currentLocale);

    if ($request->expectsJson() || $request->wantsJson() || strtolower((string) $request->header('X-Requested-With')) === 'xmlhttprequest') {
        $items = $products->map(function ($product) {
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
                'total' => $items->count(),
            ],
        ]);
    }

    return view('shop', compact('search', 'sort', 'currentLocale', 'localePrefix'));
};

Route::get('/shop', fn (Request $request) => $shopHandler($request, 'ar'));
Route::get('/ar/shop', fn (Request $request) => $shopHandler($request, 'ar'));
Route::get('/en/shop', fn (Request $request) => $shopHandler($request, 'en'));

$singleProductHandler = function (Request $request, string $slug, string $locale = 'ar') use ($localizeProductsCollectionByWpml, $resolveWpmlProductLocalization, $normalizeBrandByLocale, $mapLocaleToWpmlCode, $resolveTranslatePressLanguageCodes) {
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
                'new — styliiiish certified🔥' => 'جديد — معتمد من ستايلش 🔥',
                'new-styliiiish-certified' => 'جديد — معتمد من ستايلش 🔥',
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
                'olive' => 'زيتي',
                'olivegreen' => 'زيتي',
                'olive green' => 'زيتي',
                'olive-green' => 'زيتي',
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
                'new — styliiiish certified🔥' => 'جديد — معتمد من ستايلش 🔥',
                'used – very good — styliiiish certified ❤️' => 'مستعمل — جيد جدًا — معتمد من ستايلش ❤️',
                'used - very good - styliiiish certified ❤️' => 'مستعمل — جيد جدًا — معتمد من ستايلش ❤️',
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
                'زيتي' => 'Olive Green',
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
        return trim(mb_strtolower(str_replace(['_', '-', '–', '—'], ' ', $value)));
    };

    $normalizeConditionBrandValue = function (string $value) use ($currentLocale): string {
        if ($currentLocale !== 'ar') {
            return $value;
        }

        $normalized = trim($value);
        $normalized = preg_replace('/\s*[–—-]+\s*/u', ' ', $normalized) ?? $normalized;
        $normalized = preg_replace('/\s+/u', ' ', $normalized) ?? $normalized;
        $normalized = mb_strtolower(trim($normalized));

        $hasBrand = str_contains($normalized, 'styliiiish') || str_contains($normalized, 'ستايلش');
        $hasCertified = str_contains($normalized, 'certified') || str_contains($normalized, 'معتمد');
        $hasNew = preg_match('/\bnew\b/u', $normalized) || str_contains($normalized, 'جديد');
        $hasUsed = preg_match('/\bused\b/u', $normalized) || str_contains($normalized, 'مستعمل');
        $hasVeryGood = preg_match('/very\s*good/u', $normalized) || str_contains($normalized, 'جيد جدًا') || str_contains($normalized, 'جيد جدا');

        if ($hasNew && ($hasBrand || $hasCertified)) {
            return 'جديد — معتمد من ستايلش 🔥';
        }

        if ($hasUsed && $hasVeryGood && ($hasBrand || $hasCertified)) {
            return 'مستعمل — جيد جدًا — معتمد من ستايلش ❤️';
        }

        $normalized = str_replace('styliiiish', 'ستايلش', $normalized);
        $normalized = preg_replace('/\bused\b/u', 'مستعمل', $normalized) ?? $normalized;
        $normalized = preg_replace('/very\s*good/u', 'جيد جدًا', $normalized) ?? $normalized;
        $normalized = preg_replace('/\bcertified\b/u', 'معتمد', $normalized) ?? $normalized;
        $normalized = str_replace('ستايلش معتمد', 'معتمد من ستايلش', $normalized);
        $normalized = preg_replace('/\s+/u', ' ', trim($normalized)) ?? $normalized;
        $normalized = preg_replace('/\s*[–—-]+\s*/u', ' — ', $normalized) ?? $normalized;

        return trim($normalized);
    };

    $translateWooAttributeLabel = function (string $taxonomy, string $fallbackLabel) use ($currentLocale, $wooAttributeLabelTranslations, $normalizeWooTaxonomyKey): string {
        $localeMap = $wooAttributeLabelTranslations[$currentLocale] ?? [];
        $taxonomyKey = $normalizeWooTaxonomyKey($taxonomy);
        return (string) ($localeMap[$taxonomyKey] ?? $fallbackLabel);
    };

    $translateWooAttributeValue = function (string $taxonomy, string $slug, string $fallbackValue) use ($currentLocale, $wooAttributeValueTranslations, $normalizeTranslationKey, $normalizeWooTaxonomyKey, $normalizeConditionBrandValue): string {
        $localeMap = $wooAttributeValueTranslations[$currentLocale] ?? [];
        $taxonomyKey = $normalizeWooTaxonomyKey($taxonomy);
        $taxonomyMap = $localeMap[$taxonomyKey] ?? [];
        $isConditionTaxonomy = in_array($taxonomyKey, ['pa_product-condition', 'pa_condition'], true);

        $slugKey = $normalizeTranslationKey($slug);
        if ($slugKey !== '' && array_key_exists($slugKey, $taxonomyMap)) {
            $translated = (string) $taxonomyMap[$slugKey];
            return $isConditionTaxonomy ? $normalizeConditionBrandValue($translated) : $translated;
        }

        $valueKey = $normalizeTranslationKey($fallbackValue);
        if ($valueKey !== '' && array_key_exists($valueKey, $taxonomyMap)) {
            $translated = (string) $taxonomyMap[$valueKey];
            return $isConditionTaxonomy ? $normalizeConditionBrandValue($translated) : $translated;
        }

        return $isConditionTaxonomy ? $normalizeConditionBrandValue($fallbackValue) : $fallbackValue;
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

    if ($condition !== '' && empty($conditionValues)) {
        $condition = $translateWooAttributeValue('pa_product-condition', $condition, $condition);
    }

    if ($condition === '') {
        $condition = $currentLocale === 'en'
            ? 'New — Styliiiish Certified🔥'
            : 'جديد — معتمد من ستايلش 🔥';
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

    $productCategoryNames = collect();
    if ($productCategoryIds->isNotEmpty()) {
        $productCategoryNames = DB::table('wp_terms')
            ->whereIn('term_id', $productCategoryIds->all())
            ->pluck('name')
            ->map(fn ($name) => trim((string) $name))
            ->filter(fn ($name) => $name !== '')
            ->unique()
            ->values();
    }

    if ($currentLocale === 'ar' && $productCategoryNames->isNotEmpty()) {
        $languageCodes = $resolveTranslatePressLanguageCodes('ar');
        $defaultLanguage = strtolower((string) ($languageCodes['default'] ?? ''));
        $targetLanguage = strtolower((string) ($languageCodes['target'] ?? ''));

        if ($defaultLanguage !== '' && $targetLanguage !== '') {
            $dictionaryTable = 'wp_trp_dictionary_' . $defaultLanguage . '_' . $targetLanguage;

            if (Schema::hasTable($dictionaryTable)) {
                $categoryNames = $productCategoryNames
                    ->map(fn ($value) => trim((string) $value))
                    ->filter(fn ($value) => $value !== '')
                    ->values();

                if ($categoryNames->isNotEmpty()) {
                    $dictionaryRows = DB::table($dictionaryTable)
                        ->whereIn('original', $categoryNames->all())
                        ->whereNotNull('translated')
                        ->where('translated', '!=', '')
                        ->select('original', 'translated')
                        ->get();

                    $translationMap = [];
                    foreach ($dictionaryRows as $dictionaryRow) {
                        $original = trim((string) ($dictionaryRow->original ?? ''));
                        $translated = trim((string) ($dictionaryRow->translated ?? ''));
                        if ($original !== '' && $translated !== '' && !array_key_exists($original, $translationMap)) {
                            $translationMap[$original] = $translated;
                        }
                    }

                    if (!empty($translationMap)) {
                        $productCategoryNames = $productCategoryNames
                            ->map(function ($name) use ($translationMap, $normalizeBrandByLocale) {
                                $value = trim((string) $name);
                                if ($value === '') {
                                    return $value;
                                }

                                $translated = (string) ($translationMap[$value] ?? $value);
                                return $normalizeBrandByLocale($translated, 'ar');
                            })
                            ->filter(fn ($name) => $name !== '')
                            ->unique()
                            ->values();
                    }
                }
            }
        }
    }

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

    $allProductCategories = DB::table('wp_terms as t')
        ->join('wp_term_taxonomy as tt', 't.term_id', '=', 'tt.term_id')
        ->where('tt.taxonomy', 'product_cat')
        ->where('tt.count', '>', 0)
        ->select('t.term_id', 't.name', 't.slug')
        ->orderBy('t.name')
        ->get()
        ->map(function ($row) use ($normalizeBrandByLocale, $currentLocale) {
            $row->name = $normalizeBrandByLocale((string) ($row->name ?? ''), $currentLocale);
            return $row;
        })
        ->filter(fn ($row) => trim((string) ($row->name ?? '')) !== '' && trim((string) ($row->slug ?? '')) !== '')
        ->values();

    if ($allProductCategories->isNotEmpty() && Schema::hasTable('wp_icl_translations')) {
        $wpmlCode = $mapLocaleToWpmlCode($currentLocale);
        $baseTermIds = $allProductCategories
            ->pluck('term_id')
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        if ($baseTermIds->isNotEmpty()) {
            $translationRows = DB::table('wp_icl_translations as base')
                ->join('wp_icl_translations as localized', function ($join) {
                    $join->on('base.trid', '=', 'localized.trid')
                        ->where('localized.element_type', 'tax_product_cat');
                })
                ->where('base.element_type', 'tax_product_cat')
                ->whereIn('base.element_id', $baseTermIds->all())
                ->where('localized.language_code', 'like', $wpmlCode . '%')
                ->select('base.element_id as source_id', 'localized.element_id as localized_id')
                ->get();

            $localizedIdBySourceId = [];
            foreach ($translationRows as $translationRow) {
                $sourceId = (int) ($translationRow->source_id ?? 0);
                $localizedId = (int) ($translationRow->localized_id ?? 0);
                if ($sourceId > 0 && $localizedId > 0 && !array_key_exists($sourceId, $localizedIdBySourceId)) {
                    $localizedIdBySourceId[$sourceId] = $localizedId;
                }
            }

            if (!empty($localizedIdBySourceId)) {
                $localizedTermIds = array_values(array_unique(array_map('intval', array_values($localizedIdBySourceId))));
                $localizedTerms = DB::table('wp_terms')
                    ->whereIn('term_id', $localizedTermIds)
                    ->select('term_id', 'name', 'slug')
                    ->get()
                    ->keyBy('term_id');

                $allProductCategories = $allProductCategories->map(function ($row) use ($localizedIdBySourceId, $localizedTerms, $normalizeBrandByLocale, $currentLocale) {
                    $sourceId = (int) ($row->term_id ?? 0);
                    $localizedId = (int) ($localizedIdBySourceId[$sourceId] ?? 0);
                    $localizedTerm = $localizedTerms->get($localizedId);

                    if ($localizedTerm) {
                        $row->name = $normalizeBrandByLocale((string) ($localizedTerm->name ?? $row->name ?? ''), $currentLocale);
                        $row->slug = (string) ($localizedTerm->slug ?? $row->slug ?? '');
                    }

                    return $row;
                })->values();
            }
        }
    }

    if ($currentLocale === 'ar' && $allProductCategories->isNotEmpty()) {
        $languageCodes = $resolveTranslatePressLanguageCodes('ar');
        $defaultLanguage = strtolower((string) ($languageCodes['default'] ?? ''));
        $targetLanguage = strtolower((string) ($languageCodes['target'] ?? ''));

        if ($defaultLanguage !== '' && $targetLanguage !== '') {
            $dictionaryTable = 'wp_trp_dictionary_' . $defaultLanguage . '_' . $targetLanguage;

            if (Schema::hasTable($dictionaryTable)) {
                $categoryNames = $allProductCategories
                    ->map(fn ($row) => trim((string) ($row->name ?? '')))
                    ->filter(fn ($value) => $value !== '')
                    ->unique()
                    ->values();

                if ($categoryNames->isNotEmpty()) {
                    $dictionaryRows = DB::table($dictionaryTable)
                        ->whereIn('original', $categoryNames->all())
                        ->whereNotNull('translated')
                        ->where('translated', '!=', '')
                        ->select('original', 'translated')
                        ->get();

                    $translationMap = [];
                    foreach ($dictionaryRows as $dictionaryRow) {
                        $original = trim((string) ($dictionaryRow->original ?? ''));
                        $translated = trim((string) ($dictionaryRow->translated ?? ''));
                        if ($original !== '' && $translated !== '' && !array_key_exists($original, $translationMap)) {
                            $translationMap[$original] = $translated;
                        }
                    }

                    if (!empty($translationMap)) {
                        $allProductCategories = $allProductCategories
                            ->map(function ($row) use ($translationMap, $normalizeBrandByLocale) {
                                $name = trim((string) ($row->name ?? ''));
                                if ($name !== '') {
                                    $translated = (string) ($translationMap[$name] ?? $name);
                                    $row->name = $normalizeBrandByLocale($translated, 'ar');
                                }

                                return $row;
                            })
                            ->filter(fn ($row) => trim((string) ($row->name ?? '')) !== '' && trim((string) ($row->slug ?? '')) !== '')
                            ->values();
                    }
                }
            }
        }
    }

    $viewData = [
        'product' => $product,
        'currentLocale' => $currentLocale,
        'localePrefix' => $localePrefix,
        'wpBaseUrl' => $wpBaseUrl,
        'material' => $material,
        'color' => $color,
        'condition' => $condition,
        'productCategoryNames' => $productCategoryNames,
        'sizeValues' => $sizeValues,
        'deliveryIntro' => $deliveryIntro,
        'readyDelivery' => $readyDelivery,
        'customDelivery' => $customDelivery,
        'sizeGuideUrl' => $sizeGuideUrl,
        'hasVariations' => $hasVariations,
        'variationRules' => $variationRules,
        'productAttributesForSelection' => $productAttributesForSelection,
        'relatedProducts' => $relatedProducts,
        'allProductCategories' => $allProductCategories,
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

$resolveProductForAjaxSections = function (string $slug, string $locale = 'ar') use ($resolveWpmlProductLocalization, $localizeProductsCollectionByWpml) {
    $currentLocale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    $wpmlResolution = $resolveWpmlProductLocalization($slug, $currentLocale);
    $localizedProductId = (int) ($wpmlResolution['localized_product_id'] ?? 0);

    $product = DB::table('wp_posts as p')
        ->where('p.post_type', 'product')
        ->where('p.post_status', 'publish')
        ->where(function ($query) use ($slug, $localizedProductId) {
            if ($localizedProductId > 0) {
                $query->where('p.ID', $localizedProductId);
            } else {
                $query->where('p.post_name', $slug);
            }
        })
        ->select('p.ID', 'p.post_title', 'p.post_name', 'p.post_content', 'p.post_excerpt', 'p.post_date')
        ->first();

    if (!$product) {
        return null;
    }

    return $localizeProductsCollectionByWpml([$product], $currentLocale, true)->first();
};

$renderAjaxTabHtml = function (Request $request, string $slug, string $tab, string $locale = 'ar') use ($resolveProductForAjaxSections, $localizeProductsCollectionByTranslatePress) {
    $currentLocale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    $wpBaseUrl = rtrim((string) (env('WP_PUBLIC_URL') ?: $request->getSchemeAndHttpHost()), '/');
    $product = $resolveProductForAjaxSections($slug, $currentLocale);

    if (!$product) {
        return response()->json(['success' => false, 'message' => 'Product not found'], 404);
    }

    $tab = strtolower(trim($tab));

    if ($tab === 'description') {
        $contentHtml = trim((string) ($product->post_content ?: $product->post_excerpt));

        if ($currentLocale === 'ar') {
            $translatedCollection = $localizeProductsCollectionByTranslatePress([
                (object) [
                    'post_title' => (string) ($product->post_title ?? ''),
                    'post_excerpt' => (string) ($product->post_excerpt ?? ''),
                    'post_content' => (string) ($product->post_content ?? ''),
                ],
            ], 'ar', true);

            $translatedRow = $translatedCollection->first();
            if ($translatedRow) {
                $translatedContent = trim((string) ($translatedRow->post_content ?? ''));
                $translatedExcerpt = trim((string) ($translatedRow->post_excerpt ?? ''));
                if ($translatedContent !== '') {
                    $contentHtml = $translatedContent;
                } elseif ($translatedExcerpt !== '') {
                    $contentHtml = $translatedExcerpt;
                }
            }
        }

        $contentHtml = str_replace(
            ['https://l.styliiiish.com', 'http://l.styliiiish.com', '//l.styliiiish.com'],
            [$wpBaseUrl, $wpBaseUrl, $wpBaseUrl],
            $contentHtml
        );

        if ($contentHtml === '') {
            $contentHtml = $currentLocale === 'en' ? '<p>No description available yet.</p>' : '<p>لا يوجد وصف متاح حالياً.</p>';
        }

        return response()->json([
            'success' => true,
            'tab' => 'description',
            'html' => $contentHtml,
        ]);
    }

    if ($tab === 'specifications') {
        $attributeRows = DB::table('wp_term_relationships as tr')
            ->join('wp_term_taxonomy as tt', 'tr.term_taxonomy_id', '=', 'tt.term_taxonomy_id')
            ->join('wp_terms as t', 'tt.term_id', '=', 't.term_id')
            ->where('tr.object_id', (int) $product->ID)
            ->where('tt.taxonomy', 'like', 'pa\_%')
            ->select('tt.taxonomy', 't.name')
            ->get();

        $grouped = [];
        foreach ($attributeRows as $row) {
            $taxonomy = (string) ($row->taxonomy ?? '');
            $value = trim((string) ($row->name ?? ''));
            if ($taxonomy === '' || $value === '') {
                continue;
            }
            if (!isset($grouped[$taxonomy])) {
                $grouped[$taxonomy] = [];
            }
            if (!in_array($value, $grouped[$taxonomy], true)) {
                $grouped[$taxonomy][] = $value;
            }
        }

        $labelMap = [
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

        $valueMap = [
            'ar' => [
                'pa_color' => [
                    'olive green' => 'زيتي',
                    'olive-green' => 'زيتي',
                    'olivegreen' => 'زيتي',
                    'olive' => 'زيتي',
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
                    'beige' => 'بيج',
                ],
                'pa_colour' => [
                    'olive green' => 'زيتي',
                    'olive-green' => 'زيتي',
                    'olivegreen' => 'زيتي',
                    'olive' => 'زيتي',
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
                'pa_fabric' => [
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
                    'new — styliiiish certified🔥' => 'جديد — معتمد من ستايلش 🔥',
                    'new-styliiiish-certified' => 'جديد — معتمد من ستايلش 🔥',
                    'used – very good — styliiiish certified ❤️' => 'مستعمل — جيد جدًا — معتمد من ستايلش ❤️',
                    'used - very good - styliiiish certified ❤️' => 'مستعمل — جيد جدًا — معتمد من ستايلش ❤️',
                ],
                'pa_condition' => [
                    'new' => 'جديد',
                    'new — styliiiish certified🔥' => 'جديد — معتمد من ستايلش 🔥',
                    'new-styliiiish-certified' => 'جديد — معتمد من ستايلش 🔥',
                    'used – very good — styliiiish certified ❤️' => 'مستعمل — جيد جدًا — معتمد من ستايلش ❤️',
                    'used - very good - styliiiish certified ❤️' => 'مستعمل — جيد جدًا — معتمد من ستايلش ❤️',
                ],
            ],
        ];

        $normalizeValueKey = function (string $value): string {
            return trim(mb_strtolower(str_replace(['_', '-', '–', '—'], ' ', $value)));
        };

        $translateSpecValue = function (string $taxonomy, string $value) use ($currentLocale, $valueMap, $normalizeValueKey): string {
            if ($currentLocale !== 'ar') {
                return $value;
            }

            $taxonomyMap = $valueMap['ar'][$taxonomy] ?? [];
            $key = $normalizeValueKey($value);
            if ($key !== '' && array_key_exists($key, $taxonomyMap)) {
                return (string) $taxonomyMap[$key];
            }

            $normalized = preg_replace('/styliiiish/i', 'ستايلش', $value) ?? $value;
            $normalized = preg_replace('/\bused\b/ui', 'مستعمل', $normalized) ?? $normalized;
            $normalized = preg_replace('/very\s+good/ui', 'جيد جدًا', $normalized) ?? $normalized;
            $normalized = preg_replace('/\bcertified\b/ui', 'معتمد', $normalized) ?? $normalized;
            $normalized = str_replace('ستايلش معتمد', 'معتمد من ستايلش', $normalized);
            return trim((string) $normalized);
        };

        $items = [];
        foreach ($grouped as $taxonomy => $values) {
            $label = $labelMap[$currentLocale][$taxonomy] ?? ucwords(str_replace(['pa_', '_', '-'], ['', ' ', ' '], $taxonomy));
            $translatedValues = array_values(array_unique(array_map(function ($value) use ($taxonomy, $translateSpecValue) {
                return $translateSpecValue((string) $taxonomy, (string) $value);
            }, $values)));
            $items[] = '<li><strong>' . e($label) . ':</strong> ' . e(implode(', ', $translatedValues)) . '</li>';
        }

        $html = empty($items)
            ? ($currentLocale === 'en' ? '<p>No specifications available yet.</p>' : '<p>لا توجد مواصفات متاحة حالياً.</p>')
            : '<ul class="detail-list" style="margin-top:0;">' . implode('', $items) . '</ul>';

        return response()->json([
            'success' => true,
            'tab' => 'specifications',
            'html' => $html,
        ]);
    }

    if ($tab === 'reviews') {
        $leaveReviewLabel = $currentLocale === 'en' ? 'Leave a review' : 'اترك تعليق';
        $rows = DB::table('wp_comments as c')
            ->leftJoin('wp_commentmeta as cm', function ($join) {
                $join->on('c.comment_ID', '=', 'cm.comment_id')
                    ->where('cm.meta_key', 'rating');
            })
            ->where('c.comment_post_ID', (int) $product->ID)
            ->where('c.comment_approved', '1')
            ->whereIn('c.comment_type', ['', 'review'])
            ->orderByDesc('c.comment_date_gmt')
            ->select('c.comment_author', 'c.comment_content', 'c.comment_date', 'cm.meta_value as rating')
            ->limit(20)
            ->get();

        if ($rows->isEmpty()) {
            $html = '<p>' . ($currentLocale === 'en'
                ? 'No reviews yet. Be the first to share your feedback.'
                : 'لا توجد مراجعات بعد. كوني أول من يشارك رأيه.') . '</p>'
                . '<button type="button" data-open-review-modal style="margin-top:10px;border:1px solid rgba(213,21,34,.22);background:#fff;border-radius:999px;min-height:36px;padding:0 14px;font-size:13px;font-weight:700;color:#D51522;cursor:pointer;">'
                . e($leaveReviewLabel)
                . '</button>';

            return response()->json(['success' => true, 'tab' => 'reviews', 'html' => $html]);
        }

        $cards = [];
        foreach ($rows as $row) {
            $author = trim((string) ($row->comment_author ?? ''));
            $author = $author !== '' ? $author : ($currentLocale === 'en' ? 'Customer' : 'عميل');
            $content = nl2br(e(trim((string) ($row->comment_content ?? ''))));
            $rating = max(0, min(5, (int) ($row->rating ?? 0)));
            $stars = $rating > 0 ? str_repeat('★', $rating) . str_repeat('☆', max(0, 5 - $rating)) : '';
            $date = trim((string) ($row->comment_date ?? ''));

            $cards[] = '<article style="border:1px solid rgba(189,189,189,.4);border-radius:12px;padding:12px;background:#fff;">'
                . '<div style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;">'
                . '<strong>' . e($author) . '</strong>'
                . '<span style="font-size:12px;color:#5a6678;">' . e($date) . '</span>'
                . '</div>'
                . ($stars !== '' ? '<div style="margin-top:6px;font-size:14px;color:#D4AF37;">' . e($stars) . '</div>' : '')
                . '<div style="margin-top:8px;color:#17273B;line-height:1.8;">' . $content . '</div>'
                . '</article>';
        }

        return response()->json([
            'success' => true,
            'tab' => 'reviews',
            'html' => '<div style="display:flex;justify-content:flex-end;margin-bottom:10px;">'
                . '<button type="button" data-open-review-modal style="border:1px solid rgba(213,21,34,.22);background:#fff;border-radius:999px;min-height:36px;padding:0 14px;font-size:13px;font-weight:700;color:#D51522;cursor:pointer;">'
                . e($leaveReviewLabel)
                . '</button>'
                . '</div>'
                . '<div style="display:grid;gap:10px;">' . implode('', $cards) . '</div>',
        ]);
    }

    if ($tab === 'policies') {
        $localePrefix = $currentLocale === 'en' ? '/en' : '/ar';
        $policySlugs = [
            'shipping-delivery-policy',
            'refund-return-policy',
            'privacy-policy',
            'terms-conditions',
        ];

        $basePolicyRows = DB::table('wp_posts')
            ->where('post_type', 'page')
            ->where('post_status', 'publish')
            ->whereIn('post_name', $policySlugs)
            ->select('ID', 'post_name', 'post_title', 'post_content')
            ->get()
            ->keyBy('post_name');

        if ($basePolicyRows->isEmpty()) {
            $html = $currentLocale === 'en'
                ? '<p>Policies are not available right now.</p>'
                : '<p>السياسات غير متاحة حالياً.</p>';
            return response()->json(['success' => true, 'tab' => 'policies', 'html' => $html]);
        }

        $policyRows = collect();
        foreach ($policySlugs as $policySlug) {
            $baseRow = $basePolicyRows->get($policySlug);
            if ($baseRow) {
                $baseRow->slug_key = (string) $policySlug;
                $policyRows->push($baseRow);
            }
        }

        if ($currentLocale === 'ar' && $policyRows->isNotEmpty() && Schema::hasTable('wp_icl_translations')) {
            $baseIds = $policyRows->pluck('ID')->map(fn ($id) => (int) $id)->filter(fn ($id) => $id > 0)->values();

            if ($baseIds->isNotEmpty()) {
                $baseTranslations = DB::table('wp_icl_translations')
                    ->where('element_type', 'post_page')
                    ->whereIn('element_id', $baseIds->all())
                    ->select('element_id', 'trid')
                    ->get();

                $tridByBaseId = [];
                foreach ($baseTranslations as $translationRow) {
                    $baseId = (int) ($translationRow->element_id ?? 0);
                    $trid = (int) ($translationRow->trid ?? 0);
                    if ($baseId > 0 && $trid > 0 && !array_key_exists($baseId, $tridByBaseId)) {
                        $tridByBaseId[$baseId] = $trid;
                    }
                }

                if (!empty($tridByBaseId)) {
                    $localizedTranslationRows = DB::table('wp_icl_translations')
                        ->where('element_type', 'post_page')
                        ->whereIn('trid', array_values($tridByBaseId))
                        ->where('language_code', 'like', 'ar%')
                        ->select('trid', 'element_id')
                        ->get();

                    $localizedIdsByTrid = [];
                    foreach ($localizedTranslationRows as $localizedTranslationRow) {
                        $trid = (int) ($localizedTranslationRow->trid ?? 0);
                        $elementId = (int) ($localizedTranslationRow->element_id ?? 0);
                        if ($trid > 0 && $elementId > 0 && !array_key_exists($trid, $localizedIdsByTrid)) {
                            $localizedIdsByTrid[$trid] = $elementId;
                        }
                    }

                    if (!empty($localizedIdsByTrid)) {
                        $localizedPosts = DB::table('wp_posts')
                            ->whereIn('ID', array_values($localizedIdsByTrid))
                            ->where('post_type', 'page')
                            ->where('post_status', 'publish')
                            ->select('ID', 'post_name', 'post_title', 'post_content')
                            ->get()
                            ->keyBy('ID');

                        $policyRows = $policyRows->map(function ($policyRow) use ($tridByBaseId, $localizedIdsByTrid, $localizedPosts) {
                            $baseId = (int) ($policyRow->ID ?? 0);
                            $trid = $tridByBaseId[$baseId] ?? null;
                            $localizedId = $trid ? ($localizedIdsByTrid[$trid] ?? null) : null;
                            if (!$localizedId) {
                                return $policyRow;
                            }

                            $localizedPost = $localizedPosts->get((int) $localizedId);
                            if (!$localizedPost) {
                                return $policyRow;
                            }

                            $policyRow->ID = (int) ($localizedPost->ID ?? $policyRow->ID ?? 0);
                            $policyRow->post_name = (string) ($localizedPost->post_name ?? $policyRow->post_name ?? '');
                            $policyRow->post_title = (string) ($localizedPost->post_title ?? $policyRow->post_title ?? '');
                            $policyRow->post_content = (string) ($localizedPost->post_content ?? $policyRow->post_content ?? '');
                            return $policyRow;
                        })->values();
                    }
                }
            }

            $policyRows = $localizeProductsCollectionByTranslatePress($policyRows, 'ar', true)->values();
        }

        $cards = [];
        foreach ($policyRows as $policyRow) {
            $title = html_entity_decode(trim((string) ($policyRow->post_title ?? '')), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $content = trim((string) ($policyRow->post_content ?? ''));
            $plain = trim(html_entity_decode(strip_tags($content), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
            $excerpt = mb_substr($plain, 0, 240) . (mb_strlen($plain) > 240 ? '…' : '');
            $slugKey = trim((string) ($policyRow->slug_key ?? $policyRow->post_name ?? ''));
            $url = $wpBaseUrl . $localePrefix . '/' . trim($slugKey, '/') . '/';

            if ($currentLocale === 'ar') {
                $hasArabicText = preg_match('/[\x{0600}-\x{06FF}]/u', $title . ' ' . $plain) === 1;
                if (!$hasArabicText) {
                    $arabicFallbackMap = [
                        'shipping-delivery-policy' => [
                            'title' => 'سياسة الشحن والتوصيل',
                            'excerpt' => 'نلتزم بتوصيل طلباتك بأسرع وقت وبأفضل تجربة ممكنة. توضّح هذه السياسة مناطق الشحن، أوقات التجهيز، المدد المتوقعة للتوصيل، وآلية المتابعة حتى الاستلام.',
                        ],
                        'refund-return-policy' => [
                            'title' => 'سياسة الاسترجاع والاستبدال',
                            'excerpt' => 'نحرص على رضاك الكامل عن الطلب. توضّح هذه السياسة شروط الاسترجاع والاستبدال، المدد المتاحة، الحالات المقبولة، وخطوات معالجة طلبات الاسترداد.',
                        ],
                        'privacy-policy' => [
                            'title' => 'سياسة الخصوصية',
                            'excerpt' => 'نحترم خصوصيتك ونلتزم بحماية بياناتك الشخصية. توضّح هذه السياسة البيانات التي نجمعها، سبب جمعها، وكيفية استخدامها، والخيارات المتاحة للتحكم بها.',
                        ],
                        'terms-conditions' => [
                            'title' => 'الشروط والأحكام',
                            'excerpt' => 'تنظّم هذه الشروط والأحكام استخدامك للموقع وإتمام عمليات الشراء. باستخدامك للموقع فإنك توافق على البنود المنظمة للطلبات، الدفع، الشحن، وسياسات المتجر.',
                        ],
                    ];

                    if (isset($arabicFallbackMap[$slugKey])) {
                        $title = (string) $arabicFallbackMap[$slugKey]['title'];
                        $excerpt = (string) $arabicFallbackMap[$slugKey]['excerpt'];
                    }
                }
            }

            $cards[] = '<article style="border:1px solid rgba(189,189,189,.4);border-radius:12px;padding:12px;background:#fff;">'
                . '<h4 style="margin:0 0 8px;font-size:15px;color:#17273B;">' . e($title) . '</h4>'
                . '<p style="margin:0;color:#5a6678;line-height:1.8;">' . e($excerpt) . '</p>'
                . '<a href="' . e($url) . '" target="_blank" rel="noopener" style="display:inline-flex;margin-top:8px;font-size:13px;font-weight:700;color:#D51522;">'
                . ($currentLocale === 'en' ? 'Read policy' : 'قراءة السياسة')
                . '</a>'
                . '</article>';
        }

        return response()->json([
            'success' => true,
            'tab' => 'policies',
            'html' => '<div style="display:grid;gap:10px;">' . implode('', $cards) . '</div>',
        ]);
    }

    return response()->json(['success' => false, 'message' => 'Unsupported tab'], 422);
};

$reportProductHandler = function (Request $request, string $slug, string $locale = 'ar') use ($resolveProductForAjaxSections) {
    $currentLocale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    $product = $resolveProductForAjaxSections($slug, $currentLocale);

    if (!$product) {
        return response()->json(['success' => false, 'message' => 'Product not found'], 404);
    }

    $validated = $request->validate([
        'name' => ['required', 'string', 'max:120'],
        'email' => ['nullable', 'email', 'max:190'],
        'reason' => ['required', 'string', 'min:8', 'max:2000'],
    ]);

    DB::table('wp_comments')->insert([
        'comment_post_ID' => (int) $product->ID,
        'comment_author' => (string) ($validated['name'] ?? ''),
        'comment_author_email' => (string) ($validated['email'] ?? ''),
        'comment_author_url' => '',
        'comment_author_IP' => (string) $request->ip(),
        'comment_date' => now()->format('Y-m-d H:i:s'),
        'comment_date_gmt' => now('UTC')->format('Y-m-d H:i:s'),
        'comment_content' => (string) ($validated['reason'] ?? ''),
        'comment_karma' => 0,
        'comment_approved' => '0',
        'comment_agent' => mb_substr((string) $request->userAgent(), 0, 250),
        'comment_type' => 'product_report',
        'comment_parent' => 0,
        'user_id' => 0,
    ]);

    return response()->json([
        'success' => true,
        'message' => $currentLocale === 'en'
            ? 'Your report has been received and will be reviewed shortly.'
            : 'تم استلام البلاغ بنجاح وسيتم مراجعته قريبًا.',
    ]);
};

$submitProductReviewHandler = function (Request $request, string $slug, string $locale = 'ar') use ($resolveProductForAjaxSections) {
    $currentLocale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    $product = $resolveProductForAjaxSections($slug, $currentLocale);

    if (!$product) {
        return response()->json(['success' => false, 'message' => 'Product not found'], 404);
    }

    $validated = $request->validate([
        'name' => ['required', 'string', 'max:120'],
        'email' => ['required', 'email', 'max:190'],
        'comment' => ['required', 'string', 'min:8', 'max:2000'],
        'rating' => ['required', 'integer', 'min:1', 'max:5'],
    ]);

    $requiresModeration = (string) DB::table('wp_options')
        ->where('option_name', 'comment_moderation')
        ->value('option_value') === '1';

    $approvedState = $requiresModeration ? '0' : '1';

    $commentId = DB::table('wp_comments')->insertGetId([
        'comment_post_ID' => (int) $product->ID,
        'comment_author' => (string) ($validated['name'] ?? ''),
        'comment_author_email' => (string) ($validated['email'] ?? ''),
        'comment_author_url' => '',
        'comment_author_IP' => (string) $request->ip(),
        'comment_date' => now()->format('Y-m-d H:i:s'),
        'comment_date_gmt' => now('UTC')->format('Y-m-d H:i:s'),
        'comment_content' => (string) ($validated['comment'] ?? ''),
        'comment_karma' => 0,
        'comment_approved' => $approvedState,
        'comment_agent' => mb_substr((string) $request->userAgent(), 0, 250),
        'comment_type' => 'review',
        'comment_parent' => 0,
        'user_id' => 0,
    ]);

    DB::table('wp_commentmeta')->insert([
        'comment_id' => (int) $commentId,
        'meta_key' => 'rating',
        'meta_value' => (string) ((int) ($validated['rating'] ?? 0)),
    ]);

    return response()->json([
        'success' => true,
        'message' => $approvedState === '1'
            ? ($currentLocale === 'en' ? 'Your review was published successfully.' : 'تم نشر مراجعتك بنجاح.')
            : ($currentLocale === 'en' ? 'Your review was submitted and is awaiting approval.' : 'تم إرسال مراجعتك وهي بانتظار الموافقة.'),
    ]);
};

$wishlistBridgeCall = function (Request $request, array $payload): array {
    $bridgeUrl = rtrim($request->getSchemeAndHttpHost(), '/') . '/wishlist-bridge.php';

    try {
        $bridgeResponse = Http::asForm()
            ->timeout(12)
            ->withHeaders([
                'Cookie' => (string) $request->header('Cookie', ''),
                'X-Forwarded-For' => (string) $request->ip(),
                'User-Agent' => (string) ($request->userAgent() ?: 'Styliiiish-Laravel-Bridge'),
            ])
            ->post($bridgeUrl, $payload);

        $json = $bridgeResponse->json();
        if (!is_array($json)) {
            $json = ['success' => false, 'message' => 'Invalid bridge response.'];
        }

        return [
            'ok' => $bridgeResponse->ok() && (bool) ($json['success'] ?? false),
            'status' => $bridgeResponse->status(),
            'json' => $json,
            'set_cookie' => $bridgeResponse->header('Set-Cookie'),
        ];
    } catch (\Throwable $exception) {
        logger()->error('Wishlist bridge call failed', [
            'url' => $bridgeUrl,
            'payload_action' => $payload['action'] ?? null,
            'error' => $exception->getMessage(),
        ]);

        return [
            'ok' => false,
            'status' => 500,
            'json' => ['success' => false, 'message' => 'Bridge call failed.'],
            'set_cookie' => null,
        ];
    }
};

$wishlistAddHandler = function (Request $request, string $slug, string $locale = 'ar') use ($resolveProductForAjaxSections, $wishlistBridgeCall) {
    $currentLocale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    $product = $resolveProductForAjaxSections($slug, $currentLocale);

    if (!$product) {
        return response()->json(['success' => false, 'message' => 'Product not found'], 404);
    }

    $bridge = $wishlistBridgeCall($request, [
        'action' => 'add',
        'pid' => (int) $product->ID,
    ]);

    if (!$bridge['ok']) {
        return response()->json([
            'success' => false,
            'message' => $currentLocale === 'en'
                ? 'Wishlist service is temporarily unavailable.'
                : 'خدمة المفضلة غير متاحة حالياً.',
        ], 500);
    }

    $count = max(0, (int) ($bridge['json']['count'] ?? 0));
    $response = response()->json([
        'success' => true,
        'count' => $count,
        'message' => $currentLocale === 'en'
            ? 'Product added to wishlist.'
            : 'تمت إضافة المنتج إلى المفضلة.',
    ]);

    if (!empty($bridge['set_cookie'])) {
        $response->headers->set('Set-Cookie', is_array($bridge['set_cookie']) ? implode(', ', $bridge['set_cookie']) : (string) $bridge['set_cookie']);
    }

    return $response;
};

$wishlistRemoveHandler = function (Request $request, int $id, string $locale = 'ar') use ($wishlistBridgeCall) {
    $currentLocale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    $productId = max(0, (int) $id);

    if ($productId <= 0) {
        return response()->json([
            'success' => false,
            'message' => $currentLocale === 'en' ? 'Invalid product id.' : 'معرّف المنتج غير صالح.',
        ], 422);
    }

    $bridge = $wishlistBridgeCall($request, [
        'action' => 'remove',
        'pid' => $productId,
    ]);

    if (!$bridge['ok']) {
        return response()->json([
            'success' => false,
            'message' => $currentLocale === 'en'
                ? 'Unable to remove wishlist item.'
                : 'تعذر حذف المنتج من المفضلة.',
        ], 500);
    }

    $response = response()->json([
        'success' => true,
        'count' => max(0, (int) ($bridge['json']['count'] ?? 0)),
        'message' => $currentLocale === 'en' ? 'Item removed from wishlist.' : 'تم حذف المنتج من المفضلة.',
    ]);

    if (!empty($bridge['set_cookie'])) {
        $response->headers->set('Set-Cookie', is_array($bridge['set_cookie']) ? implode(', ', $bridge['set_cookie']) : (string) $bridge['set_cookie']);
    }

    return $response;
};

$wishlistCountHandler = function (Request $request, string $locale = 'ar') use ($wishlistBridgeCall) {
    $currentLocale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    $bridge = $wishlistBridgeCall($request, [
        'action' => 'count',
    ]);

    if (!$bridge['ok']) {
        return response()->json([
            'success' => false,
            'message' => $currentLocale === 'en'
                ? 'Unable to load wishlist count.'
                : 'تعذر تحميل عدد المفضلة.',
        ], 500);
    }

    $response = response()->json([
        'success' => true,
        'count' => max(0, (int) ($bridge['json']['count'] ?? 0)),
    ]);

    if (!empty($bridge['set_cookie'])) {
        $response->headers->set('Set-Cookie', is_array($bridge['set_cookie']) ? implode(', ', $bridge['set_cookie']) : (string) $bridge['set_cookie']);
    }

    return $response;
};

$wishlistItemsHandler = function (Request $request, string $locale = 'ar') use ($wishlistBridgeCall, $mapLocaleToWpmlCode, $normalizeBrandByLocale, $resolveTranslatePressLanguageCodes) {
    $currentLocale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    $bridge = $wishlistBridgeCall($request, [
        'action' => 'list',
        'limit' => 8,
        'locale' => $currentLocale,
    ]);

    if (!$bridge['ok']) {
        return response()->json([
            'success' => false,
            'message' => $currentLocale === 'en'
                ? 'Unable to load wishlist items.'
                : 'تعذر تحميل عناصر المفضلة.',
        ], 500);
    }

    $rawItems = collect($bridge['json']['items'] ?? [])->filter(fn ($item) => is_array($item))->values();
    $localePrefix = '/' . $currentLocale;
    $wpmlCode = $mapLocaleToWpmlCode($currentLocale);

    $sourceProductIds = $rawItems
        ->map(fn ($item) => (int) ($item['id'] ?? 0))
        ->filter(fn ($id) => $id > 0)
        ->unique()
        ->values();

    $localizedIdBySourceId = [];
    $localizedPostsById = collect();

    if ($sourceProductIds->isNotEmpty() && Schema::hasTable('wp_icl_translations')) {
        $translationRows = DB::table('wp_icl_translations as base')
            ->join('wp_icl_translations as localized', function ($join) {
                $join->on('base.trid', '=', 'localized.trid')
                    ->where('localized.element_type', 'post_product');
            })
            ->where('base.element_type', 'post_product')
            ->whereIn('base.element_id', $sourceProductIds->all())
            ->where('localized.language_code', 'like', $wpmlCode . '%')
            ->select('base.element_id as source_id', 'localized.element_id as localized_id')
            ->get();

        foreach ($translationRows as $translationRow) {
            $sourceId = (int) ($translationRow->source_id ?? 0);
            $localizedId = (int) ($translationRow->localized_id ?? 0);
            if ($sourceId > 0 && $localizedId > 0 && !array_key_exists($sourceId, $localizedIdBySourceId)) {
                $localizedIdBySourceId[$sourceId] = $localizedId;
            }
        }

        $localizedPostIds = collect($sourceProductIds)
            ->map(fn ($sourceId) => (int) ($localizedIdBySourceId[(int) $sourceId] ?? (int) $sourceId))
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        if ($localizedPostIds->isNotEmpty()) {
            $localizedPostsById = DB::table('wp_posts')
                ->whereIn('ID', $localizedPostIds->all())
                ->where('post_type', 'product')
                ->where('post_status', 'publish')
                ->select('ID', 'post_title', 'post_name')
                ->get()
                ->keyBy('ID');
        }
    }

    $items = $rawItems->map(function (array $item) use ($localePrefix, $localizedIdBySourceId, $localizedPostsById, $normalizeBrandByLocale, $currentLocale) {
        $sourceId = (int) ($item['id'] ?? 0);
        $localizedId = (int) ($localizedIdBySourceId[$sourceId] ?? $sourceId);
        $localizedPost = $localizedPostsById->get($localizedId);

        $slug = trim((string) (($localizedPost->post_name ?? null) ?: ($item['slug'] ?? '')));
        $fallbackUrl = trim((string) ($item['url'] ?? ''));
        $productUrl = $slug !== '' ? $localePrefix . '/item/' . rawurlencode($slug) : $fallbackUrl;
        $bridgeName = trim((string) ($item['name'] ?? ''));
        $wpmlName = trim((string) ($localizedPost->post_title ?? ''));

        if ($currentLocale === 'ar') {
            $resolvedName = $bridgeName !== '' ? $bridgeName : $wpmlName;
        } else {
            $resolvedName = $wpmlName !== '' ? $wpmlName : $bridgeName;
        }

        $resolvedName = $normalizeBrandByLocale($resolvedName, $currentLocale);

        return [
            'id' => $localizedId > 0 ? $localizedId : $sourceId,
            'name' => $resolvedName,
            'image' => (string) ($item['image'] ?? ''),
            'url' => $productUrl,
        ];
    })->filter(fn ($item) => $item['id'] > 0 && $item['name'] !== '' && $item['url'] !== '')->values();

    if ($currentLocale === 'ar' && $items->isNotEmpty()) {
        $languageCodes = $resolveTranslatePressLanguageCodes('ar');
        $defaultLanguage = strtolower((string) ($languageCodes['default'] ?? ''));
        $targetLanguage = strtolower((string) ($languageCodes['target'] ?? ''));

        if ($defaultLanguage !== '' && $targetLanguage !== '') {
            $dictionaryTable = 'wp_trp_dictionary_' . $defaultLanguage . '_' . $targetLanguage;

            if (Schema::hasTable($dictionaryTable)) {
                $nameVariants = [];

                $normalizeNameVariant = function (string $value): string {
                    $decoded = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $decoded = str_replace(["\u{00A0}", "\r", "\n", "\t"], ' ', $decoded);
                    $decoded = preg_replace('/\s+/u', ' ', $decoded);
                    return trim((string) $decoded);
                };

                foreach ($items as $item) {
                    $name = trim((string) ($item['name'] ?? ''));
                    if ($name === '') {
                        continue;
                    }

                    $nameVariants[] = $name;

                    $normalized = $normalizeNameVariant($name);
                    if ($normalized !== '' && $normalized !== $name) {
                        $nameVariants[] = $normalized;
                    }
                }

                $nameVariants = array_values(array_unique(array_filter($nameVariants, fn ($value) => is_string($value) && trim($value) !== '')));

                if (!empty($nameVariants)) {
                    $dictionaryRows = DB::table($dictionaryTable)
                        ->whereIn('original', $nameVariants)
                        ->whereNotNull('translated')
                        ->where('translated', '!=', '')
                        ->select('original', 'translated')
                        ->get();

                    if ($dictionaryRows->isNotEmpty()) {
                        $translationMap = [];

                        foreach ($dictionaryRows as $dictionaryRow) {
                            $original = trim((string) ($dictionaryRow->original ?? ''));
                            $translated = trim((string) ($dictionaryRow->translated ?? ''));

                            if ($original === '' || $translated === '') {
                                continue;
                            }

                            if (!array_key_exists($original, $translationMap)) {
                                $translationMap[$original] = $translated;
                            }

                            $normalizedOriginal = $normalizeNameVariant($original);
                            if ($normalizedOriginal !== '' && !array_key_exists($normalizedOriginal, $translationMap)) {
                                $translationMap[$normalizedOriginal] = $translated;
                            }
                        }

                        $items = $items->map(function ($item) use ($translationMap, $normalizeNameVariant, $normalizeBrandByLocale) {
                            $currentName = trim((string) ($item['name'] ?? ''));
                            if ($currentName === '') {
                                return $item;
                            }

                            $normalizedName = $normalizeNameVariant($currentName);
                            $translatedName = $translationMap[$currentName] ?? $translationMap[$normalizedName] ?? null;

                            if (is_string($translatedName) && trim($translatedName) !== '') {
                                $item['name'] = $normalizeBrandByLocale(trim($translatedName), 'ar');
                            }

                            return $item;
                        })->values();
                    }
                }
            }
        }
    }

    $response = response()->json([
        'success' => true,
        'count' => max(0, (int) ($bridge['json']['count'] ?? $items->count())),
        'items' => $items,
    ]);

    if (!empty($bridge['set_cookie'])) {
        $response->headers->set('Set-Cookie', is_array($bridge['set_cookie']) ? implode(', ', $bridge['set_cookie']) : (string) $bridge['set_cookie']);
    }

    return $response;
};

$wishlistPageHandler = function (Request $request, string $locale = 'ar') {
    $currentLocale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    $localePrefix = $currentLocale === 'en' ? '/en' : '/ar';
    $isEnglish = $currentLocale === 'en';

    return view('wishlist', compact('currentLocale', 'localePrefix', 'isEnglish'));
};

$cartPageHandler = function (Request $request, string $locale = 'ar') {
    $currentLocale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    $localePrefix = $currentLocale === 'en' ? '/en' : '/ar';
    $isEnglish = $currentLocale === 'en';

    return view('cart', compact('currentLocale', 'localePrefix', 'isEnglish'));
};


Route::get('/item/{slug}/tabs/{tab}', fn (Request $request, string $slug, string $tab) => $renderAjaxTabHtml($request, $slug, $tab, 'ar'));
Route::get('/ar/item/{slug}/tabs/{tab}', fn (Request $request, string $slug, string $tab) => $renderAjaxTabHtml($request, $slug, $tab, 'ar'));
Route::get('/en/item/{slug}/tabs/{tab}', fn (Request $request, string $slug, string $tab) => $renderAjaxTabHtml($request, $slug, $tab, 'en'));

Route::post('/item/{slug}/report', fn (Request $request, string $slug) => $reportProductHandler($request, $slug, 'ar'));
Route::post('/ar/item/{slug}/report', fn (Request $request, string $slug) => $reportProductHandler($request, $slug, 'ar'));
Route::post('/en/item/{slug}/report', fn (Request $request, string $slug) => $reportProductHandler($request, $slug, 'en'));

Route::post('/item/{slug}/review', fn (Request $request, string $slug) => $submitProductReviewHandler($request, $slug, 'ar'));
Route::post('/ar/item/{slug}/review', fn (Request $request, string $slug) => $submitProductReviewHandler($request, $slug, 'ar'));
Route::post('/en/item/{slug}/review', fn (Request $request, string $slug) => $submitProductReviewHandler($request, $slug, 'en'));

Route::post('/item/{slug}/wishlist/add', fn (Request $request, string $slug) => $wishlistAddHandler($request, $slug, 'ar'));
Route::post('/ar/item/{slug}/wishlist/add', fn (Request $request, string $slug) => $wishlistAddHandler($request, $slug, 'ar'));
Route::post('/en/item/{slug}/wishlist/add', fn (Request $request, string $slug) => $wishlistAddHandler($request, $slug, 'en'));

Route::delete('/item/wishlist/{id}', fn (Request $request, int $id) => $wishlistRemoveHandler($request, $id, 'ar'));
Route::delete('/ar/item/wishlist/{id}', fn (Request $request, int $id) => $wishlistRemoveHandler($request, $id, 'ar'));
Route::delete('/en/item/wishlist/{id}', fn (Request $request, int $id) => $wishlistRemoveHandler($request, $id, 'en'));

Route::get('/item/wishlist/count', fn (Request $request) => $wishlistCountHandler($request, 'ar'));
Route::get('/ar/item/wishlist/count', fn (Request $request) => $wishlistCountHandler($request, 'ar'));
Route::get('/en/item/wishlist/count', fn (Request $request) => $wishlistCountHandler($request, 'en'));

Route::get('/item/wishlist/items', fn (Request $request) => $wishlistItemsHandler($request, 'ar'));
Route::get('/ar/item/wishlist/items', fn (Request $request) => $wishlistItemsHandler($request, 'ar'));
Route::get('/en/item/wishlist/items', fn (Request $request) => $wishlistItemsHandler($request, 'en'));

Route::get('/wishlist', fn (Request $request) => $wishlistPageHandler($request, 'ar'));
Route::get('/ar/wishlist', fn (Request $request) => $wishlistPageHandler($request, 'ar'));
Route::get('/en/wishlist', fn (Request $request) => $wishlistPageHandler($request, 'en'));

Route::get('/cart', fn (Request $request) => $cartPageHandler($request, 'ar'));
Route::get('/ar/cart', fn (Request $request) => $cartPageHandler($request, 'ar'));
Route::get('/en/cart', fn (Request $request) => $cartPageHandler($request, 'en'));

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

$categoriesHandler = function (Request $request, string $locale = 'ar') use ($normalizeBrandByLocale, $resolveTranslatePressLanguageCodes) {
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
        ->get()
        ->map(function ($category) use ($currentLocale, $normalizeBrandByLocale) {
            $category->name = $normalizeBrandByLocale((string) ($category->name ?? ''), $currentLocale);
            $category->description = $normalizeBrandByLocale((string) ($category->description ?? ''), $currentLocale);
            return $category;
        });

    $languageCodes = $resolveTranslatePressLanguageCodes($currentLocale);
    if ($languageCodes && $categories->isNotEmpty()) {
        $normalizeLookupText = function (string $value): string {
            $decoded = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $stripped = trim(strip_tags($decoded));
            $collapsed = preg_replace('/\s+/u', ' ', $stripped) ?? $stripped;
            return trim((string) $collapsed);
        };

        $defaultLanguage = (string) ($languageCodes['default'] ?? '');
        $targetLanguage = (string) ($languageCodes['target'] ?? '');

        if ($defaultLanguage !== '' && $targetLanguage !== '' && $defaultLanguage !== $targetLanguage) {
            $dictionaryTable = 'wp_trp_dictionary_' . $defaultLanguage . '_' . $targetLanguage;
            if (Schema::hasTable($dictionaryTable)) {
                $lookupStrings = $categories
                    ->flatMap(function ($category) use ($normalizeLookupText) {
                        $name = trim((string) ($category->name ?? ''));
                        $description = trim((string) ($category->description ?? ''));

                        return [
                            $name,
                            $normalizeLookupText($name),
                            $description,
                            $normalizeLookupText($description),
                        ];
                    })
                    ->filter(fn ($value) => $value !== '')
                    ->unique()
                    ->values();

                if ($lookupStrings->isNotEmpty()) {
                    $dictionaryRows = DB::table($dictionaryTable)
                        ->whereIn('original', $lookupStrings->all())
                        ->where('status', '!=', 0)
                        ->whereNotNull('translated')
                        ->where('translated', '!=', '')
                        ->select('original', 'translated')
                        ->get();

                    if ($dictionaryRows->isNotEmpty()) {
                        $translationMap = [];
                        foreach ($dictionaryRows as $row) {
                            $original = trim((string) ($row->original ?? ''));
                            $translated = trim((string) ($row->translated ?? ''));
                            if ($original !== '' && $translated !== '' && !array_key_exists($original, $translationMap)) {
                                $translationMap[$original] = $translated;
                            }

                            $normalizedOriginal = $normalizeLookupText($original);
                            if ($normalizedOriginal !== '' && $translated !== '' && !array_key_exists($normalizedOriginal, $translationMap)) {
                                $translationMap[$normalizedOriginal] = $translated;
                            }
                        }

                        if (!empty($translationMap)) {
                            $categories = $categories->map(function ($category) use ($translationMap, $normalizeBrandByLocale, $currentLocale, $normalizeLookupText) {
                                $originalName = trim((string) ($category->name ?? ''));
                                if ($originalName !== '' && isset($translationMap[$originalName])) {
                                    $category->name = $normalizeBrandByLocale((string) $translationMap[$originalName], $currentLocale);
                                } else {
                                    $normalizedName = $normalizeLookupText($originalName);
                                    if ($normalizedName !== '' && isset($translationMap[$normalizedName])) {
                                        $category->name = $normalizeBrandByLocale((string) $translationMap[$normalizedName], $currentLocale);
                                    }
                                }

                                $originalDescription = trim((string) ($category->description ?? ''));
                                if ($originalDescription !== '' && isset($translationMap[$originalDescription])) {
                                    $category->description = $normalizeBrandByLocale((string) $translationMap[$originalDescription], $currentLocale);
                                } else {
                                    $normalizedDescription = $normalizeLookupText($originalDescription);
                                    if ($normalizedDescription !== '' && isset($translationMap[$normalizedDescription])) {
                                        $category->description = $normalizeBrandByLocale((string) $translationMap[$normalizedDescription], $currentLocale);
                                    }
                                }

                                return $category;
                            });
                        }
                    }
                }
            }
        }
    }

    if ($currentLocale === 'ar' && $categories->isNotEmpty()) {
        $fallbackArabicDescriptions = [
            'discover beautiful plus-size modest dresses designed for the perfect fit' => 'اكتشفي فساتين محتشمة بمقاسات كبيرة بتصميمات أنيقة وقصّات مريحة تمنحكِ إطلالة متناسقة وثقة أكبر.',
            'final clearance modest dresses! shop our last chance collection for deep discounts on elegant' => 'تصفية نهائية على الفساتين المحتشمة — فرصة أخيرة للحصول على موديلات أنيقة بخصومات قوية لفترة محدودة.',
            'discover luxury modest evening dresses and formal gowns. shop sparkling sequins, elegant' => 'اكتشفي فساتين سواريه فاخرة وموديلات رسمية أنيقة بتفاصيل راقية ولمسات مميزة تناسب مناسباتك الخاصة.',
            'discover our stunning collection of used dresses in excellent condition' => 'اكتشفي مجموعة فساتين مستعملة بحالة ممتازة بعناية وجودة عالية وبأسعار مناسبة.',
            'discover elegant modest dresses for the mother of the bride/groom' => 'اكتشفي فساتين أنيقة ومحتشمة للأمهات في حفلات الزفاف والخطوبة بإطلالات راقية ومريحة.',
        ];

        $categories = $categories->map(function ($category) use ($fallbackArabicDescriptions) {
            $description = trim((string) ($category->description ?? ''));
            if ($description === '') {
                return $category;
            }

            $hasArabic = preg_match('/[\x{0600}-\x{06FF}]/u', $description) === 1;
            if ($hasArabic) {
                return $category;
            }

            $normalizedDescription = mb_strtolower($description);
            $normalizedDescription = html_entity_decode($normalizedDescription, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $normalizedDescription = preg_replace('/\s+/u', ' ', trim($normalizedDescription)) ?? trim($normalizedDescription);

            foreach ($fallbackArabicDescriptions as $needle => $arabicText) {
                if (str_contains($normalizedDescription, $needle)) {
                    $category->description = $arabicText;
                    return $category;
                }
            }

            $category->description = 'اكتشفي موديلات هذا القسم بتفاصيل مميزة وجودة عالية مع خيارات متنوعة تناسب مناسبتك.';
            return $category;
        });
    }

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