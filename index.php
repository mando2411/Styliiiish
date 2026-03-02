<?php

// ===== Laravel Routing =====
$request_uri = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');

// Normalize trailing slash except root
$path = rtrim($request_uri, '/');
$path = $path === '' ? '/' : $path;

// Ensure Arabic my-account logout endpoint never 404s before WP boot.
if (in_array($path, ['/ar/حسابي/customer-logout', '/ara/حسابي/customer-logout', '/حسابي/customer-logout'], true)) {
    $target = '/my-account/customer-logout/';
    if (!empty($_SERVER['QUERY_STRING'])) {
        $target .= '?' . $_SERVER['QUERY_STRING'];
    }
    header('Location: ' . $target, true, 302);
    exit;
}

// Keep rental landing bilingual under WordPress + TranslatePress context.
if (in_array($path, ['/ar/dress-rental-in-cairo', '/ar/dress-rental-in-cairo/'], true)) {
    setcookie('trp_language', 'ar', [
        'expires' => time() + (30 * 24 * 60 * 60),
        'path' => '/',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => false,
        'samesite' => 'Lax',
    ]);
}

if (in_array($path, ['/dress-rental-in-cairo', '/dress-rental-in-cairo/'], true)) {
    setcookie('trp_language', 'en', [
        'expires' => time() + (30 * 24 * 60 * 60),
        'path' => '/',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => false,
        'samesite' => 'Lax',
    ]);
}

// Fix localized static asset URLs like /ar/wp-content/... -> /wp-content/...
if (preg_match('#^/(ar|en|ara)/wp-content/(.+)$#u', $request_uri, $matches)) {
    $normalized_asset_path = '/wp-content/' . $matches[2];
    $normalized_asset_file = realpath(__DIR__ . $normalized_asset_path);

    if ($normalized_asset_file !== false && strpos($normalized_asset_file, realpath(__DIR__ . '/wp-content')) === 0 && is_file($normalized_asset_file)) {
        $ext = strtolower(pathinfo($normalized_asset_file, PATHINFO_EXTENSION));
        $mime_types = [
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'webp' => 'image/webp',
            'css' => 'text/css; charset=UTF-8',
            'js' => 'application/javascript; charset=UTF-8',
            'json' => 'application/json; charset=UTF-8',
            'txt' => 'text/plain; charset=UTF-8',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'map' => 'application/json; charset=UTF-8',
        ];

        if (isset($mime_types[$ext])) {
            header('Content-Type: ' . $mime_types[$ext]);
        }

        header('Cache-Control: public, max-age=604800');
        readfile($normalized_asset_file);
        exit;
    }

    header('Location: ' . $normalized_asset_path, true, 302);
    exit;
}

// Exact routes handled by Laravel
$laravel_exact_routes = [
    '/',
    '/index.php',
    '/ar',
    '/en',
    '/shop',
    '/ar/shop',
    '/en/shop',
    '/item',
    '/ar/item',
    '/en/item',
    '/categories',
    '/ar/categories',
    '/en/categories',
    '/categories/',
    '/marketplace',
    '/ar/marketplace',
    '/en/marketplace',
    '/marketplace/',
    '/blog',
    '/ar/blog',
    '/en/blog',
    '/about-us',
    '/ar/about-us',
    '/en/about-us',
    '/privacy-policy',
    '/ar/privacy-policy',
    '/en/privacy-policy',
    '/ar/سياسة-الخصوصية',
    '/terms-conditions',
    '/ar/terms-conditions',
    '/en/terms-conditions',
    '/marketplace-policy',
    '/ar/marketplace-policy',
    '/en/marketplace-policy',
    '/Marketplace-Policy',
    '/Marketplace-Policy/',
    '/refund-return-policy',
    '/ar/refund-return-policy',
    '/en/refund-return-policy',
    '/Refund-Return-Policy',
    '/Refund-Return-Policy/',
    '/faq',
    '/ar/faq',
    '/en/faq',
    '/styliiiish-faq',
    '/styliiiish-faq/',
    '/shipping-delivery-policy',
    '/ar/shipping-delivery-policy',
    '/en/shipping-delivery-policy',
    '/shipping-delivery-policy/',
    '/cookie-policy',
    '/ar/cookie-policy',
    '/en/cookie-policy',
    '/🍪-cookie-policy',
    '/🍪-cookie-policy/',
    '/contact-us',
    '/ar/contact-us',
    '/en/contact-us',
    '/ads',
    '/ar/ads',
    '/en/ads',
    '/google-reviews',
    '/brand',
    '/favicon.ico',
];

// Prefix routes (assets/subpaths) handled by Laravel
$laravel_prefix_routes = [
    '/ar/',
    '/en/',
    '/shop/',
    '/item/',
    '/categories/',
    '/marketplace/',
    '/blog/',
    '/about-us/',
    '/privacy-policy/',
    '/ar/سياسة-الخصوصية/',
    '/terms-conditions/',
    '/marketplace-policy/',
    '/Marketplace-Policy/',
    '/refund-return-policy/',
    '/Refund-Return-Policy/',
    '/faq/',
    '/styliiiish-faq/',
    '/shipping-delivery-policy/',
    '/cookie-policy/',
    '/🍪-cookie-policy/',
    '/contact-us/',
    '/ads/',
    '/google-reviews/',
    '/brand/',
    '/build/',
    '/storage/',
];

// Routes that must stay on WordPress (e.g. translated plugin endpoints)
$wordpress_exact_routes = [
    '/dress-rental-in-cairo',
    '/dress-rental-in-cairo/',
    '/ar/dress-rental-in-cairo',
    '/ar/dress-rental-in-cairo/',
    '/en/dress-rental-in-cairo',
    '/en/dress-rental-in-cairo/',
    '/ar/الدفع',
    '/ar/الدفع/',
    '/ara/الدفع',
    '/ara/الدفع/',
    '/حسابي',
    '/حسابي/',
    '/ar/حسابي',
    '/ar/حسابي/',
    '/ara/حسابي',
    '/ara/حسابي/',
    '/my-account',
    '/my-account/',
    '/en/my-account',
    '/en/my-account/',
    '/فساتيني',
    '/فساتيني/',
    '/ar/فساتيني',
    '/ar/فساتيني/',
    '/ara/فساتيني',
    '/ara/فساتيني/',
    '/owner-dashboard',
    '/owner-dashboard/',
    '/ar/لوحة-معلومات-المالك',
    '/ar/لوحة-معلومات-المالك/',
    '/ara/لوحة-معلومات-المالك',
    '/ara/لوحة-معلومات-المالك/',
];

$wordpress_prefix_routes = [
    '/dress-rental-in-cairo/',
    '/ar/dress-rental-in-cairo/',
    '/en/dress-rental-in-cairo/',
    '/ar/wp-json/',
    '/en/wp-json/',
    '/ara/wp-json/',
    '/ar/حسابي/',
    '/ara/حسابي/',
    '/حسابي/',
    '/my-account/',
    '/en/my-account/',
    '/ar/فساتيني/',
    '/ara/فساتيني/',
    '/فساتيني/',
    '/owner-dashboard/',
    '/ar/لوحة-معلومات-المالك/',
    '/ara/لوحة-معلومات-المالك/',
];

$send_to_laravel = null;

if (isset($_GET['wc-ajax']) && (string) $_GET['wc-ajax'] !== '') {
    $send_to_laravel = false;
}

if (in_array($request_uri, $wordpress_exact_routes, true) || in_array($path, $wordpress_exact_routes, true)) {
    $send_to_laravel = false;
}

if ($send_to_laravel === null) {
    foreach ($wordpress_prefix_routes as $wp_prefix) {
        if (strpos($request_uri, $wp_prefix) === 0) {
            $send_to_laravel = false;
            break;
        }
    }
}

if ($send_to_laravel === null) {
    $send_to_laravel = in_array($path, $laravel_exact_routes, true);

    if (!$send_to_laravel) {
        foreach ($laravel_prefix_routes as $prefix) {
            if (strpos($request_uri, $prefix) === 0) {
                $send_to_laravel = true;
                break;
            }
        }
    }
}

// Default strategy: Laravel handles all non-explicit-WordPress routes.
if ($send_to_laravel === null) {
    $send_to_laravel = true;
}

if ($send_to_laravel) {
    $laravel_public = __DIR__ . '/laravel_home/public';
    $requested_file = realpath($laravel_public . $request_uri);

    if ($requested_file === false && $request_uri === '/favicon.ico') {
        $favicon_fallbacks = [
            realpath($laravel_public . '/favicon.ico'),
            realpath($laravel_public . '/brand/icons.png'),
            realpath($laravel_public . '/brand/logo.png'),
        ];

        foreach ($favicon_fallbacks as $fallback_file) {
            if ($fallback_file !== false && is_file($fallback_file)) {
                $requested_file = $fallback_file;
                break;
            }
        }
    }

    if ($requested_file === false && strpos($request_uri, '/google-reviews/') === 0) {
        $fallback_reviews_dir = realpath(__DIR__ . '/laravel_home/Google Reviews');
        $fallback_candidate = $fallback_reviews_dir
            ? realpath($fallback_reviews_dir . '/' . basename($request_uri))
            : false;

        if (
            $fallback_candidate !== false &&
            strpos($fallback_candidate, $fallback_reviews_dir) === 0 &&
            is_file($fallback_candidate)
        ) {
            $requested_file = $fallback_candidate;
        }
    }

    if (
        $requested_file !== false &&
        (
            strpos($requested_file, realpath($laravel_public)) === 0 ||
            strpos($requested_file, realpath(__DIR__ . '/laravel_home/Google Reviews')) === 0
        ) &&
        is_file($requested_file)
    ) {
        $ext = strtolower(pathinfo($requested_file, PATHINFO_EXTENSION));
        $mime_types = [
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'webp' => 'image/webp',
            'css' => 'text/css; charset=UTF-8',
            'js' => 'application/javascript; charset=UTF-8',
            'json' => 'application/json; charset=UTF-8',
            'txt' => 'text/plain; charset=UTF-8',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'map' => 'application/json; charset=UTF-8',
        ];

        if (isset($mime_types[$ext])) {
            header('Content-Type: ' . $mime_types[$ext]);
        }

        if (strpos($request_uri, '/google-reviews/') === 0) {
            header('Cache-Control: public, max-age=300, must-revalidate');
        } else {
            header('Cache-Control: public, max-age=604800');
        }
        readfile($requested_file);
        exit;
    }

    require __DIR__ . '/laravel_home/public/index.php';
    exit;
}

// ===== WordPress normal loading =====
define('WP_USE_THEMES', true);
require __DIR__ . '/wp-blog-header.php';