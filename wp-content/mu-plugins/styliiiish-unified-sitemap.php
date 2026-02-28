<?php
/**
 * Plugin Name: Styliiiish Unified Sitemap
 * Description: Serves a unified sitemap index and Laravel-facing sitemap without requiring SEO plugins.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('styliiiish_sitemap_escape')) {
    function styliiiish_sitemap_escape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }
}

if (!function_exists('styliiiish_get_sitemap_request_path')) {
    function styliiiish_get_sitemap_request_path(): string
    {
        $requestUri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '/';
        $path = parse_url($requestUri, PHP_URL_PATH);
        $path = is_string($path) ? $path : '/';
        return ltrim(rawurldecode($path), '/');
    }
}

if (!function_exists('styliiiish_output_sitemap_index')) {
    function styliiiish_output_sitemap_index(): void
    {
        $lastmod = gmdate('c');
        $items = [
            home_url('/wp-sitemap.xml'),
            home_url('/laravel-sitemap.xml'),
        ];

        status_header(200);
        nocache_headers();
        header('Content-Type: application/xml; charset=UTF-8');

        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($items as $loc) {
            echo '<sitemap>';
            echo '<loc>' . styliiiish_sitemap_escape((string) $loc) . '</loc>';
            echo '<lastmod>' . styliiiish_sitemap_escape($lastmod) . '</lastmod>';
            echo '</sitemap>';
        }

        echo '</sitemapindex>';
        exit;
    }
}

if (!function_exists('styliiiish_output_laravel_sitemap')) {
    function styliiiish_output_laravel_sitemap(): void
    {
        global $wpdb;

        $base = rtrim(home_url('/'), '/');
        $now = gmdate('c');
        $urls = [];

        $addUrl = static function (string $url, string $lastmod = '') use (&$urls, $now): void {
            $normalized = trim($url);
            if ($normalized === '' || isset($urls[$normalized])) {
                return;
            }

            $urls[$normalized] = [
                'loc' => $normalized,
                'lastmod' => $lastmod !== '' ? $lastmod : $now,
            ];
        };

        $staticPaths = [
            '/',
            '/ar',
            '/en',
            '/shop',
            '/ar/shop',
            '/en/shop',
            '/ads',
            '/ar/ads',
            '/en/ads',
            '/blog',
            '/ar/blog',
            '/en/blog',
            '/contact-us',
            '/ar/contact-us',
            '/en/contact-us',
            '/about-us',
            '/ar/about-us',
            '/en/about-us',
            '/privacy-policy',
            '/ar/privacy-policy',
            '/en/privacy-policy',
            '/terms-conditions',
            '/ar/terms-conditions',
            '/en/terms-conditions',
            '/marketplace-policy',
            '/ar/marketplace-policy',
            '/en/marketplace-policy',
            '/refund-return-policy',
            '/ar/refund-return-policy',
            '/en/refund-return-policy',
            '/faq',
            '/ar/faq',
            '/en/faq',
            '/shipping-delivery-policy',
            '/ar/shipping-delivery-policy',
            '/en/shipping-delivery-policy',
            '/cookie-policy',
            '/ar/cookie-policy',
            '/en/cookie-policy',
            '/categories',
            '/ar/categories',
            '/en/categories',
            '/marketplace',
            '/ar/marketplace',
            '/en/marketplace',
            '/wishlist',
            '/ar/wishlist',
            '/en/wishlist',
            '/cart',
            '/ar/cart',
            '/en/cart',
            '/merchant-feed.xml',
            '/merchant-feed-en.xml',
        ];

        foreach ($staticPaths as $path) {
            $addUrl($base . $path);
        }

        $productRows = $wpdb->get_results(
            "SELECT ID, post_name, post_modified_gmt FROM {$wpdb->posts} WHERE post_type = 'product' AND post_status = 'publish'"
        );

        if (is_array($productRows)) {
            foreach ($productRows as $row) {
                $slug = isset($row->post_name) ? trim((string) $row->post_name) : '';
                if ($slug === '') {
                    continue;
                }

                $encodedSlug = rawurlencode($slug);
                $lastmod = !empty($row->post_modified_gmt) ? gmdate('c', strtotime((string) $row->post_modified_gmt . ' UTC')) : $now;

                $addUrl($base . '/product/' . $encodedSlug . '/', $lastmod);
                $addUrl($base . '/item/' . $encodedSlug, $lastmod);
                $addUrl($base . '/ar/item/' . $encodedSlug, $lastmod);
                $addUrl($base . '/en/item/' . $encodedSlug, $lastmod);
            }
        }

        $postRows = $wpdb->get_results(
            "SELECT ID, post_name, post_modified_gmt FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'publish'"
        );

        if (is_array($postRows)) {
            foreach ($postRows as $row) {
                $slug = isset($row->post_name) ? trim((string) $row->post_name) : '';
                if ($slug === '') {
                    continue;
                }

                $encodedSlug = rawurlencode($slug);
                $lastmod = !empty($row->post_modified_gmt) ? gmdate('c', strtotime((string) $row->post_modified_gmt . ' UTC')) : $now;

                $addUrl($base . '/blog/' . $encodedSlug, $lastmod);
                $addUrl($base . '/ar/blog/' . $encodedSlug, $lastmod);
                $addUrl($base . '/en/blog/' . $encodedSlug, $lastmod);
            }
        }

        $pageRows = $wpdb->get_results(
            "SELECT ID, post_modified_gmt FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status = 'publish'"
        );

        if (is_array($pageRows)) {
            foreach ($pageRows as $row) {
                $pageId = isset($row->ID) ? (int) $row->ID : 0;
                if ($pageId <= 0) {
                    continue;
                }

                $permalink = get_permalink($pageId);
                if (!is_string($permalink) || $permalink === '') {
                    continue;
                }

                $lastmod = !empty($row->post_modified_gmt) ? gmdate('c', strtotime((string) $row->post_modified_gmt . ' UTC')) : $now;
                $addUrl($permalink, $lastmod);
            }
        }

        status_header(200);
        nocache_headers();
        header('Content-Type: application/xml; charset=UTF-8');

        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($urls as $item) {
            echo '<url>';
            echo '<loc>' . styliiiish_sitemap_escape((string) $item['loc']) . '</loc>';
            echo '<lastmod>' . styliiiish_sitemap_escape((string) $item['lastmod']) . '</lastmod>';
            echo '</url>';
        }

        echo '</urlset>';
        exit;
    }
}

add_action('template_redirect', function (): void {
    if (is_admin()) {
        return;
    }

    $path = strtolower(styliiiish_get_sitemap_request_path());

    if ($path === 'sitemap_index.xml' || $path === 'en/sitemap_index.xml') {
        styliiiish_output_sitemap_index();
    }

    if ($path === 'laravel-sitemap.xml') {
        styliiiish_output_laravel_sitemap();
    }
}, 0);
