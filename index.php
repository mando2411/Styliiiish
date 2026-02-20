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
    require __DIR__ . '/laravel_home/public/index.php';
    exit;
}

// ===== WordPress normal loading =====
define('WP_USE_THEMES', true);
require __DIR__ . '/wp-blog-header.php';