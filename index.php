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
    '/shop',
    '/ads',
    '/google-reviews',
    '/brand',
    '/favicon.ico',
];

// Prefix routes (assets/subpaths) handled by Laravel
$laravel_prefix_routes = [
    '/shop/',
    '/ads/',
    '/google-reviews/',
    '/brand/',
    '/build/',
    '/storage/',
];

$send_to_laravel = in_array($path, $laravel_exact_routes, true);

if (!$send_to_laravel) {
    foreach ($laravel_prefix_routes as $prefix) {
        if (strpos($request_uri, $prefix) === 0) {
            $send_to_laravel = true;
            break;
        }
    }
}

if ($send_to_laravel) {
    $laravel_public = __DIR__ . '/laravel_home/public';
    $requested_file = realpath($laravel_public . $request_uri);

    if (
        $requested_file !== false &&
        strpos($requested_file, realpath($laravel_public)) === 0 &&
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

        header('Cache-Control: public, max-age=604800');
        readfile($requested_file);
        exit;
    }

    require __DIR__ . '/laravel_home/public/index.php';
    exit;
}

// ===== WordPress normal loading =====
define('WP_USE_THEMES', true);
require __DIR__ . '/wp-blog-header.php';