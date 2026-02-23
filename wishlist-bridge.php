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


$normalizeList = static function ($list): array {
    if (!is_array($list)) {
        return [];
    }

    return array_values(array_unique(array_map('intval', array_filter($list, static fn ($value) => (int) $value > 0))));
};

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
} elseif (!in_array($action, ['count', 'list'], true)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'Unknown action.',
    ]);
    exit;
}

$list = $normalizeList(fable_extra_woowishlist_get_list());

$count = count($list);

if ($action === 'list') {
    $limit = isset($_POST['limit']) ? (int) $_POST['limit'] : 8;
    $limit = max(1, min(20, $limit));

    $items = [];
    $ids = array_slice(array_reverse($list), 0, $limit);

    foreach ($ids as $productId) {
        $id = (int) $productId;
        if ($id <= 0) {
            continue;
        }

        $post = get_post($id);
        if (!$post || $post->post_type !== 'product') {
            continue;
        }

        $name = trim((string) get_the_title($id));
        $url = (string) get_permalink($id);
        $slug = trim((string) ($post->post_name ?? ''));
        $image = (string) get_the_post_thumbnail_url($id, 'woocommerce_thumbnail');

        if ($image === '') {
            $image = (string) wc_placeholder_img_src('woocommerce_thumbnail');
        }

        if ($name === '' || $url === '') {
            continue;
        }

        $items[] = [
            'id' => $id,
            'name' => $name,
            'url' => $url,
            'slug' => $slug,
            'image' => $image,
        ];
    }

    echo json_encode([
        'success' => true,
        'count' => max(0, $count),
        'items' => $items,
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'count' => max(0, $count),
]);
