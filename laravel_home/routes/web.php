<?php

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

$homeHandler = function (string $locale = 'ar') {
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

    $products = Cache::remember('home_products', 300, function () {

        return DB::table('wp_posts as p')
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

$shopDataHandler = function (Request $request) {
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

    return [$products, $search, $sort];
};

$shopHandler = function (Request $request, string $locale = 'ar') use ($shopDataHandler) {
    $currentLocale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    $localePrefix = $currentLocale === 'en' ? '/en' : '/ar';

    [$products, $search, $sort] = $shopDataHandler($request);

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

$adsHandler = function (string $locale = 'ar') {
    $currentLocale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    $localePrefix = $currentLocale === 'en' ? '/en' : '/ar';

    $products = Cache::remember('ads_products', 300, function () {
        return DB::table('wp_posts as p')
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

Route::get('/favicon.ico', function (Request $request) {
    $wpBaseUrl = rtrim((string) (env('WP_PUBLIC_URL') ?: $request->getSchemeAndHttpHost()), '/');
    return redirect()->away($wpBaseUrl . '/wp-content/uploads/2025/11/cropped-ChatGPT-Image-Nov-2-2025-03_11_14-AM-e1762046066547.png');
});