<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed.',
    ]);
    exit;
}

$wpLoadPath = __DIR__ . '/wp-load.php';
if (!is_file($wpLoadPath)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'WordPress bootstrap not found.',
    ]);
    exit;
}

require_once $wpLoadPath;

if (!function_exists('fable_extra_woowishlist_get_list') || !function_exists('fable_extra_woowishlist_add')) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Wishlist plugin is not available.',
    ]);
    exit;
}

$action = isset($_POST['action']) ? (string) $_POST['action'] : '';

if ($action === 'add') {
    $pid = isset($_POST['pid']) ? (int) $_POST['pid'] : 0;

    if ($pid <= 0) {
        http_response_code(422);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid product id.',
        ]);
        exit;
    }

    fable_extra_woowishlist_add($pid);
} elseif ($action !== 'count') {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'Unknown action.',
    ]);
    exit;
}

$list = fable_extra_woowishlist_get_list();
if (!is_array($list)) {
    $list = [];
}

$count = count(array_unique(array_map('intval', $list)));

echo json_encode([
    'success' => true,
    'count' => max(0, $count),
]);
