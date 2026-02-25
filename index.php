<?php

// ===== Laravel Routing =====
$request_uri = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');

// Normalize trailing slash except root
$path = rtrim($request_uri, '/');
$path = $path === '' ? '/' : $path;

// Exact routes handled by Laravel
$laravel_exact_routes = [
    '/',
    '/index.php',
    '/ar',
    '/en',
    '/shop',
    '/ar/shop',
    '/en/shop',
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
    '/ar/الدفع',
    '/ar/الدفع/',
    '/ara/الدفع',
    '/ara/الدفع/',
];

$wordpress_prefix_routes = [
    '/ar/wp-json/',
    '/en/wp-json/',
    '/ara/wp-json/',
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