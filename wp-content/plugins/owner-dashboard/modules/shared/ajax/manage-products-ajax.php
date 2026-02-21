<?php


/**************** Manage-products-ajax.php ************************/







add_action('pre_get_posts', function ($q) {

    if (is_admin()) return;
    if (!$q->is_main_query()) return;

    // صفحة لوحة تحكم إضافة الفساتين
    if (is_page('manage-customer-products')) {

        $user_id = get_current_user_id();
        $q->set('author', $user_id);
        $q->set('post_type', 'product');
        $q->set('post_status', ['publish', 'pending', 'draft']);
    }
});

 
 
 
 
 
 
 
 

 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
 
            /*             ############         Function Of Add New product          ##################                              */

 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
 
 add_action('wp_ajax_styliiiish_add_new_product', function () {

    $user_id = get_current_user_id();
    if (!$user_id) {
        wp_send_json_error(['message' => 'Not logged in']);
    }

    // =========================
    // Determine template based on user role
    // =========================
    // Owner template = 1905
    // User template  = 1954 (Your user/customer template)
    $manager_ids   = wf_od_get_manager_ids();     // managers
    $dashboard_ids = wf_od_get_dashboard_ids();   // owner-dashboard access

    if (in_array($user_id, $manager_ids) || in_array($user_id, $dashboard_ids)) {
        $template_id = 1905;  // OWNER TEMPLATE
    } else {
        $template_id = 1954;  // USER TEMPLATE
    }

    // Permission check
   $user_type = wf_od_get_user_type($user_id);

    // owner + manager + dashboard مسموحين
    if ($user_type === 'manager' || $user_type === 'dashboard') {
        // allowed
    }
    // user (marketplace) مسموح يضيف منتج لنفسه
    elseif ($user_type === 'marketplace') {
        // allowed
    }
    // غير كده block (مش هيحصل)
    else {
        wp_send_json_error(['message' => 'No permission']);
    }


    $post = get_post($template_id);
    if (!$post) {
        wp_send_json_error(['message' => 'Template not found']);
    }

    // =========================
    // Create new product (DRAFT)
    // =========================
    $new_id = wp_insert_post([
        'post_title'   => $post->post_title ?: 'New Product',
        'post_content' => $post->post_content,
        'post_excerpt' => $post->post_excerpt,
        'post_status'  => 'draft',
        'post_type'    => 'product',
        'post_author'  => $user_id, // Important
    ], true);

    if (is_wp_error($new_id)) {
        wp_send_json_error(['message' => $new_id->get_error_message()]);
    }

    // Copy meta
    $meta = get_post_meta($template_id);
    foreach ($meta as $key => $values) {
        if (in_array($key, ['_edit_lock','_edit_last'], true)) continue;
        foreach ($values as $value) {
            add_post_meta($new_id, $key, maybe_unserialize($value));
        }
    }

    // Copy taxonomy terms
    $taxonomies = get_object_taxonomies('product');
    foreach ($taxonomies as $taxonomy) {
        $terms = wp_get_object_terms($template_id, $taxonomy, ['fields' => 'ids']);
        if (!empty($terms)) {
            wp_set_object_terms($new_id, $terms, $taxonomy);
        }
    }

    // Copy featured image
    $thumb_id = get_post_thumbnail_id($template_id);
    if ($thumb_id) {
        set_post_thumbnail($new_id, $thumb_id);
    }

    // Copy gallery
    $gallery = get_post_meta($template_id, '_product_image_gallery', true);
    if (!empty($gallery)) {
        update_post_meta($new_id, '_product_image_gallery', $gallery);
    }

    wp_send_json_success([
        'new_id' => $new_id,
    ]);
});

 
 
 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
 
            /*             ############         AJAX: Get ALL product attributes + selected terms        ##################                              */

 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/ 
 
add_action('wp_ajax_styliiiish_get_attributes', function () {

    $pid = intval($_POST['product_id']);
    if (!$pid) wp_send_json_error(['message' => 'Invalid product']);

    $product = wc_get_product($pid);
    if (!$product) wp_send_json_error(['message' => 'Not found']);

    $taxes = wc_get_attribute_taxonomies();
    $data  = [];

    foreach ($taxes as $tax) {

        $taxonomy = 'pa_' . $tax->attribute_name;

        $terms = get_terms([
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
        ]);

        $options = [];
        foreach ($terms as $t){
            $options[] = [
                'label' => $t->name,
                'value' => $t->slug
            ];
        }

        $selected = wc_get_product_terms($pid, $taxonomy, ['fields'=>'slugs']);

        $data[] = [
            'taxonomy' => $taxonomy,
            'label'    => wc_attribute_label($taxonomy),
            'options'  => $options,
            'selected' => isset($selected[0]) ? $selected[0] : '',
        ];
    }

    wp_send_json_success($data);
});


add_action('wp_ajax_styliiiish_save_attributes', function () {

    $pid   = intval($_POST['product_id']);
    $items = $_POST['items'] ?? [];

    if (!$pid) {
        wp_send_json_error(['message' => 'Invalid product']);
    }

    $product = wc_get_product($pid);

    // اقرأ الـ attributes المفعلة حاليًا
    $existing_attrs = get_post_meta($pid, '_product_attributes', true);
    if (!is_array($existing_attrs)) {
        $existing_attrs = [];
    }

    foreach ($items as $taxonomy => $slug) {

        if (empty($slug)) continue;

        /* =====================================================
           1) VALIDATION — حماية من أي taxonomy أو slug مزيف
        ======================================================*/

        // لازم يكون attribute taxonomy pa_xxx
        if (strpos($taxonomy, 'pa_') !== 0) {
            continue;
        }

        // لازم تكون taxonomy حقيقية داخل WooCommerce
        if (!taxonomy_exists($taxonomy)) {
            continue;
        }

        // لازم يكون الـ term موجود فعلاً داخل الـ taxonomy
        $term_obj = get_term_by('slug', $slug, $taxonomy);
        if (!$term_obj) {
            continue;
        }

        /* =====================================================
           2) بعد التأكد — احفظ الـ term
        ======================================================*/
        wp_set_object_terms($pid, [$slug], $taxonomy);

        // مثال: pa_color → color
        $attr_name = str_replace('pa_', '', $taxonomy);

        // لو مش موجود: نضيفه
        if (!isset($existing_attrs[$taxonomy])) {

            $existing_attrs[$taxonomy] = [
                'name'         => $taxonomy,
                'value'        => $slug,
                'is_visible'   => 1,
                'is_variation' => 0,
                'is_taxonomy'  => 1
            ];

        } else {
            // موجود مسبقًا → حدّث القيمة
            $existing_attrs[$taxonomy]['value'] = $slug;
        }
        
        
    }

    // احفظ attributes في المنتج
    update_post_meta($pid, '_product_attributes', $existing_attrs);

    wp_send_json_success(['message' => 'Updated']);
});


 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
 
            /*             ############         AJAX: Get ALL product attributes + selected terms        ##################                              */

 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/ 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
/* ===============================
   Custom Image Upload (no media library UI)
================================== */
add_action('wp_ajax_styliiiish_upload_image_custom', function () {

    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Not logged in']);
    }

    $product_id = intval($_POST['product_id']);
    $user_id    = get_current_user_id();

    if (!$product_id || get_post_type($product_id) !== 'product') {
        wp_send_json_error(['message' => 'Invalid product']);
    }

    // السماح فقط لصاحب المنتج أو مدير
    $product_author = (int) get_post_field('post_author', $product_id);
    $is_manager     = current_user_can('manage_woocommerce');
    $is_owner       = ($user_id && $user_id === $product_author);

    if (!$is_manager && !$is_owner) {
        wp_send_json_error(['message' => 'No permission']);
    }

    if (empty($_FILES['file'])) {
        wp_send_json_error(['message' => 'No file received']);
    }

    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    $uploaded = wp_handle_upload($_FILES['file'], ['test_form' => false]);

    if (isset($uploaded['error'])) {
        wp_send_json_error(['message' => $uploaded['error']]);
    }

    // Create attachment
    $attachment = [
        'post_mime_type' => $uploaded['type'],
        'post_title'     => sanitize_file_name($_FILES['file']['name']),
        'post_content'   => '',
        'post_status'    => 'inherit'
    ];

    $attach_id = wp_insert_attachment($attachment, $uploaded['file']);
    $attach_data = wp_generate_attachment_metadata($attach_id, $uploaded['file']);
    wp_update_attachment_metadata($attach_id, $attach_data);

    // Add to gallery
    $gallery = get_post_meta($product_id, '_product_image_gallery', true);
    $gallery_ids = !empty($gallery)
        ? array_filter(array_map('intval', explode(',', $gallery)))
        : [];

    // أضف الصورة بدون حذف القديمة
    if (!in_array($attach_id, $gallery_ids, true)) {
        $gallery_ids[] = $attach_id;
    }

    // احفظ الجاليري
    update_post_meta($product_id, '_product_image_gallery', implode(',', $gallery_ids));

    // ==========
    // اجعل أول صورة فقط Featured
    // ==========
    $current_main = get_post_thumbnail_id($product_id);

    if (!$current_main) {
        // لو مفيش Main، خلي أول صورة هي Main
        set_post_thumbnail($product_id, $attach_id);
    }

    // إعادة تحميل الواجهة
    $html      = styliiiish_render_product_images_html($product_id);
    $product   = wc_get_product($product_id);
    $main_html = $product ? $product->get_image('thumbnail') : '';

            styliiiish_auto_pending_check($product_id);

    wp_send_json_success([
        'html' => $html,
        'main' => $main_html
    ]);
});


 
 
/* ===============================
   AJAX: Get Images Modal Content
================================== */
add_action('wp_ajax_styliiiish_get_images', function () {

    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $user_id    = get_current_user_id();

    if (!$product_id || get_post_type($product_id) !== 'product') {
        wp_send_json_error(['message' => 'Invalid product']);
    }

    // السماح فقط لصاحب المنتج أو مدير
    $product_author = (int) get_post_field('post_author', $product_id);
    $is_manager     = current_user_can('manage_woocommerce');
    $is_owner       = ($user_id && $user_id === $product_author);

    if (!$is_manager && !$is_owner) {
        wp_send_json_error(['message' => 'No permission']);
    }

    $html      = styliiiish_render_product_images_html($product_id);
    $product   = wc_get_product($product_id);
    $main_html = $product ? $product->get_image('thumbnail') : '';

    wp_send_json_success([
        'html' => $html,
        'main' => $main_html,
    ]);
});


/* ===============================
   AJAX: Add/Attach image to product (from Media Library)
================================== */

add_action('wp_ajax_styliiiish_add_image_to_product', function () {

    $product_id    = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $attachment_id = isset($_POST['attachment_id']) ? intval($_POST['attachment_id']) : 0;
    $user_id       = get_current_user_id();

    if (!$product_id || !$attachment_id || get_post_type($product_id) !== 'product') {
        wp_send_json_error(['message' => 'Invalid data']);
    }

    // السماح فقط لصاحب المنتج أو مدير
    $product_author = (int) get_post_field('post_author', $product_id);
    $is_manager     = current_user_can('manage_woocommerce');
    $is_owner       = ($user_id && $user_id === $product_author);

    if (!$is_manager && !$is_owner) {
        wp_send_json_error(['message' => 'No permission']);
    }

    // احصل على الجاليري القديم
    $gallery     = get_post_meta($product_id, '_product_image_gallery', true);
    $gallery_ids = !empty($gallery) ? array_filter(array_map('intval', explode(',', $gallery))) : [];

    // أضف الصورة الجديدة للجاليري فقط (من غير ما نمسح اللي قبلها)
    if (!in_array($attachment_id, $gallery_ids, true)) {
        $gallery_ids[] = $attachment_id;
        update_post_meta($product_id, '_product_image_gallery', implode(',', $gallery_ids));
    }

    // ⭐ لو المنتج ماعندوش Main حالياً خلي الصورة دي هى الـ Main
    $current_main = get_post_thumbnail_id($product_id);
    if (!$current_main) {
        set_post_thumbnail($product_id, $attachment_id);
    }

    $html      = styliiiish_render_product_images_html($product_id);
    $product   = wc_get_product($product_id);
    $main_html = $product ? $product->get_image('thumbnail') : '';

    wp_send_json_success([
        'html' => $html,
        'main' => $main_html,
    ]);
});



/* ===============================
   AJAX: Set Featured Image
================================== */
add_action('wp_ajax_styliiiish_set_featured_image', function () {

    $product_id    = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $attachment_id = isset($_POST['attachment_id']) ? intval($_POST['attachment_id']) : 0;
    $user_id       = get_current_user_id();

    if (!$product_id || !$attachment_id || get_post_type($product_id) !== 'product') {
        wp_send_json_error(['message' => 'Invalid data']);
    }

    // السماح فقط لصاحب المنتج أو مدير
    $product_author = (int) get_post_field('post_author', $product_id);
    $is_manager     = current_user_can('manage_woocommerce');
    $is_owner       = ($user_id && $user_id === $product_author);

    if (!$is_manager && !$is_owner) {
        wp_send_json_error(['message' => 'No permission']);
    }

    set_post_thumbnail($product_id, $attachment_id);

    $html      = styliiiish_render_product_images_html($product_id);
    $product   = wc_get_product($product_id);
    $main_html = $product ? $product->get_image('thumbnail') : '';

    wp_send_json_success([
        'html' => $html,
        'main' => $main_html,
    ]);
});


/* ===============================
   AJAX: Remove image (featured or gallery)
================================== */
add_action('wp_ajax_styliiiish_remove_image', function () {

    $product_id    = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $attachment_id = isset($_POST['attachment_id']) ? intval($_POST['attachment_id']) : 0;
    $user_id       = get_current_user_id();

    if (!$product_id || !$attachment_id || get_post_type($product_id) !== 'product') {
        wp_send_json_error(['message' => 'Invalid data']);
    }

    // السماح فقط لصاحب المنتج أو مدير
    $product_author = (int) get_post_field('post_author', $product_id);
    $is_manager     = current_user_can('manage_woocommerce');
    $is_owner       = ($user_id && $user_id === $product_author);

    if (!$is_manager && !$is_owner) {
        wp_send_json_error(['message' => 'No permission']);
    }

    $thumb_id    = get_post_thumbnail_id($product_id);
    $gallery     = get_post_meta($product_id, '_product_image_gallery', true);
    $gallery_ids = !empty($gallery) ? array_filter(array_map('intval', explode(',', $gallery))) : [];

    // Remove from gallery
    $gallery_ids = array_diff($gallery_ids, [$attachment_id]);
    update_post_meta($product_id, '_product_image_gallery', implode(',', $gallery_ids));

    // If this was featured image, remove it
    if ($thumb_id == $attachment_id) {
        delete_post_thumbnail($product_id);
        // Set new main if any gallery left
        if (!empty($gallery_ids)) {
            $new_main = reset($gallery_ids);
            set_post_thumbnail($product_id, $new_main);
        }
    }

    $html      = styliiiish_render_product_images_html($product_id);
    $product   = wc_get_product($product_id);
    $main_html = $product ? $product->get_image('thumbnail') : '';

    wp_send_json_success([
        'html' => $html,
        'main' => $main_html,
    ]);
});






















/* ===============================
   AJAX: Load Categories
================================== */
add_action('wp_ajax_styliiiish_get_cats', function () {

    if (!current_user_can('manage_woocommerce')) {
        wp_die('No permission');
    }

    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    if (!$product_id) {
        wp_die('Invalid product');
    }

    $current = wp_get_post_terms($product_id, 'product_cat', ['fields' => 'ids']);

    $cats = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
    ]);

    if (empty($cats) || is_wp_error($cats)) {
        echo '<p>No categories found.</p>';
        wp_die();
    }

    foreach ($cats as $cat) {
        $checked = in_array($cat->term_id, $current, true) ? 'checked' : '';
        echo '<label>
                <input type="checkbox" value="' . esc_attr($cat->term_id) . '" ' . $checked . ' />
                ' . esc_html($cat->name) . '
              </label>';
    }

    wp_die();
});

/* ===============================
   AJAX: Save Categories
================================== */
add_action('wp_ajax_styliiiish_save_cats', function () {

    if (!current_user_can('manage_woocommerce')) {
        wp_die('No permission');
    }

    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    if (!$product_id) {
        wp_die('Invalid product');
    }

    $cats = isset($_POST['cats']) ? array_map('intval', (array) $_POST['cats']) : [];

    wp_set_post_terms($product_id, $cats, 'product_cat');

    $names = wp_get_post_terms($product_id, 'product_cat', ['fields' => 'names']);

    echo !empty($names) ? esc_html(implode(', ', $names)) : '—';

    wp_die();
});

/* ===============================
   AJAX: Update Product Status
================================== */
add_action('wp_ajax_styliiiish_update_status', function () {

    if (!current_user_can('manage_woocommerce')) {
        wp_send_json_error(['message' => 'No permission']);
    }

    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $status     = isset($_POST['status']) ? sanitize_key($_POST['status']) : '';

    if (!$product_id || empty($status)) {
        wp_send_json_error(['message' => 'Invalid data']);
    }

    $allowed_statuses = ['publish', 'draft', 'pending', 'private'];
    if (!in_array($status, $allowed_statuses, true)) {
        wp_send_json_error(['message' => 'Invalid status']);
    }

    $update = wp_update_post([
        'ID'          => $product_id,
        'post_status' => $status,
    ], true);

    if (is_wp_error($update)) {
        wp_send_json_error(['message' => $update->get_error_message()]);
    }

    wp_send_json_success(['status' => $status]);
});

/* ===============================
   AJAX: Delete Product (NO RELOAD)
================================== */
add_action('wp_ajax_styliiiish_delete_product', function () {

    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

    if (!$product_id || get_post_type($product_id) !== 'product') {
        wp_send_json_error(['message' => 'Invalid product']);
    }

    $user_id        = get_current_user_id();
    $product_author = (int) get_post_field('post_author', $product_id);

    $is_manager = current_user_can('manage_woocommerce');
    $is_owner   = ($user_id && $user_id === $product_author);

    // 👇 إضافة من عندي:
    // السماح فقط: Manager أو صاحب المنتج
    if (!$is_manager && !$is_owner) {
        wp_send_json_error(['message' => 'No permission']);
    }

    $deleted = wp_delete_post($product_id, true);

    if (!$deleted) {
        wp_send_json_error(['message' => 'Failed to delete product']);
    }

    wp_send_json_success(['message' => 'Product deleted']);
});


/* ===============================
   AJAX: Duplicate Product
================================== */
add_action('wp_ajax_styliiiish_duplicate_product', function () {

    if (!current_user_can('edit_products')) {
        wp_send_json_error(['message' => 'No permission']);
    }

    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    if (!$product_id || get_post_type($product_id) !== 'product') {
        wp_send_json_error(['message' => 'Invalid product']);
    }

    $post = get_post($product_id);
    if (!$post) {
        wp_send_json_error(['message' => 'Product not found']);
    }

    $new_post = [
        'post_title'   => $post->post_title . ' (Copy)',
        'post_content' => $post->post_content,
        'post_excerpt' => $post->post_excerpt,
        'post_status'  => 'draft',
        'post_type'    => 'product',
        'post_author'  => get_current_user_id(),
    ];

    $new_id = wp_insert_post($new_post, true);
    if (is_wp_error($new_id)) {
        wp_send_json_error(['message' => $new_id->get_error_message()]);
    }

    // Copy meta
    $meta = get_post_meta($product_id);
    foreach ($meta as $key => $values) {
        if (in_array($key, ['_edit_lock','_edit_last'], true)) continue;
        foreach ($values as $value) {
            add_post_meta($new_id, $key, maybe_unserialize($value));
        }
    }

    // Copy terms
    $taxonomies = get_object_taxonomies('product');
    foreach ($taxonomies as $taxonomy) {
        $terms = wp_get_object_terms($product_id, $taxonomy, ['fields' => 'ids']);
        if (!empty($terms) && !is_wp_error($terms)) {
            wp_set_object_terms($new_id, $terms, $taxonomy);
        }
    }

    // Copy featured image
    $thumb_id = get_post_thumbnail_id($product_id);
    if ($thumb_id) {
        set_post_thumbnail($new_id, $thumb_id);
    }

    wp_send_json_success(['message' => 'Product duplicated', 'new_id' => $new_id]);
});

/* ===============================
   AJAX: Quick Inline Update (name/price/description)
================================== */
add_action('wp_ajax_styliiiish_quick_update_product', function () {

    $product_id = intval($_POST['product_id'] ?? 0);
    $field      = sanitize_key($_POST['field'] ?? '');
    $value      = wp_unslash($_POST['value'] ?? '');

    if (!$product_id || !$field) {
        wp_send_json_error(['message' => 'Invalid data']);
    }

    if (get_post_type($product_id) !== 'product') {
        wp_send_json_error(['message' => 'Invalid product type']);
    }

    $user_id        = get_current_user_id();
    $product_author = (int) get_post_field('post_author', $product_id);
    $is_manager     = current_user_can('manage_woocommerce');
    $is_owner       = ($user_id && $user_id === $product_author);

    // السماح فقط لصاحب المنتج أو مدير
    if (!$is_manager && !$is_owner) {
        wp_send_json_error(['message' => 'No permission']);
    }

    switch ($field) {

        /* =======================
           NAME
        ======================== */
        case 'name':
        case 'title':

            $update = wp_update_post([
                'ID'         => $product_id,
                'post_title' => wp_strip_all_tags($value),
            ], true);

            if (is_wp_error($update)) {
                wp_send_json_error(['message' => $update->get_error_message()]);
            }

            // 🔥 لازم قبل الـ send success
            styliiiish_auto_pending_check($product_id);

            wp_send_json_success([
                'value' => esc_html($value)
            ]);
            break;

        /* =======================
           DESCRIPTION
        ======================== */
        
        
        
        
        
        
        
        
        
        
        
        
        
        case 'post_content':
        case 'description':
        
            $value_clean = wp_kses_post($value);
        
            $update = wp_update_post([
                'ID'           => $product_id,
                'post_content' => $value_clean,
            ], true);
        
            if (is_wp_error($update)) {
                wp_send_json_error(['message' => $update->get_error_message()]);
            }
        
            // اعمل Preview بس للعرض، مش للحفظ
            $preview = wp_trim_words(wp_strip_all_tags($value_clean), 30);
        
            styliiiish_auto_pending_check($product_id);
        
            wp_send_json_success([
                'short' => esc_html($preview), // ده للعرض فقط
                'full'  => $value_clean        // ده الوصف الحقيقي
            ]);
            break;














        /* =======================
           PRICE
        ======================== */
        case 'price':

            $numeric = floatval(preg_replace('/[^0-9.,]/', '', $value));

            if ($numeric < 0) {
                wp_send_json_error(['message' => 'Invalid price']);
            }

            update_post_meta($product_id, '_regular_price', $numeric);
            update_post_meta($product_id, '_price', $numeric);

            // 🔥 لازم قبل الـ send success
            styliiiish_auto_pending_check($product_id);

            wp_send_json_success([
                'value' => esc_html($numeric) . ' EGP'
            ]);
            break;

        default:
            wp_send_json_error(['message' => 'Invalid field']);
    }
});



/* ===============================
   AJAX: Bulk Actions
================================== */
add_action('wp_ajax_styliiiish_bulk_action', function () {

    if (!current_user_can('edit_products')) {
        wp_send_json_error(['message' => 'No permission']);
    }

    $action_type = isset($_POST['bulk_action']) ? sanitize_key($_POST['bulk_action']) : '';
    $ids         = isset($_POST['ids']) ? (array) $_POST['ids'] : [];

    if (empty($action_type) || empty($ids)) {
        wp_send_json_error(['message' => 'No products selected']);
    }

    $ids = array_map('intval', $ids);

    $affected = 0;

    foreach ($ids as $id) {
        if (get_post_type($id) !== 'product') {
            continue;
        }

        switch ($action_type) {
            case 'delete':
                if (!current_user_can('delete_post', $id)) {
                    continue;
                }
                $deleted = wp_delete_post($id, true);
                if ($deleted) {
                    $affected++;
                }
                break;

            case 'publish':
            case 'draft':
                if (!current_user_can('edit_post', $id)) {
                    continue;
                }
                $res = wp_update_post([
                    'ID'          => $id,
                    'post_status' => $action_type,
                ], true);
                if (!is_wp_error($res)) {
                    $affected++;
                }
                break;
        }
    }

    if (!$affected) {
        wp_send_json_error(['message' => 'No products updated']);
    }

    wp_send_json_success(['message' => "$affected product(s) updated"]);
});


/* ===============================
   Helper: Build Table + Stats + Pagination
================================== */
function styliiiish_build_manage_products_content($paged = 1, $search = '', $cat = 0, $status_filter = '', $mode = 'owner') {

    $is_mobile = wp_is_mobile();
    $per_page  = $is_mobile ? 5 : 10;

    // detect mode
    if (isset($_POST['mode']) && in_array($_POST['mode'], ['owner','user'], true)) {
        $mode = sanitize_text_field($_POST['mode']);
    }

    $is_user = ($mode === 'user');
    
    
    
    
    
    
    
    
    
    $is_vendor_mode = ($mode === 'vendor');

// Logic for Vendor Mode (Customer Dresses)
if ($is_vendor_mode) {

    // 1) Vendor = all users EXCEPT managers/owners
    $manager_ids = get_option('styliiiish_allowed_manager_ids', []);
    if (empty($manager_ids)) {
        $manager_ids = [ get_current_user_id() ];
    }

    $base_args['author__not_in'] = $manager_ids;

    // 2) Product statuses allowed
    // customer dresses appear only when pending or draft
    if (!empty($status_filter)) {

        switch ($status_filter) {
            case 'pending':
                $base_args['post_status'] = ['pending'];
                break;

            case 'draft':
                $base_args['post_status'] = ['draft'];
                break;

            case 'uncomplete':
                $base_args['post_status'] = ['draft'];
                $base_args['meta_query'][] = [
                    'key'     => '_styliiiish_manual_deactivate',
                    'compare' => 'NOT EXISTS'
                ];
                break;

            case 'deactivated':
                $base_args['post_status'] = ['draft'];
                $base_args['meta_query'][] = [
                    'key'     => '_styliiiish_manual_deactivate',
                    'value'   => 'yes',
                    'compare' => '='
                ];
                break;
        }

    } else {
        // default vendor view (pending + draft)
        $base_args['post_status'] = ['pending', 'draft'];
    }
}

    
    
    
    
    
    
    
    
    
    
    
    
    

    /* ============================================
       Build base query args (OLD CLEAN LOGIC)
    ============================================ */
    // Determine allowed authors for OWNER MODE
        $allowed_ids = get_option('styliiiish_allowed_manager_ids', []);
        if (empty($allowed_ids)) {
             $allowed_ids = [ get_current_user_id() ];
}

    
        $base_args = [
            'post_type'      => 'product',
            'posts_per_page' => $per_page,
            'paged'          => $paged,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'post__not_in'   => [1905, 1954],
        ];
        
        $base_args['meta_query'] = [];

        
        // Mode: user → يشوف منتجاته فقط
        if ($is_user) {
            $base_args['author'] = get_current_user_id();
        }
        
        // Mode: owner → يشوف كل allowed managers/users
        else {
            $allowed_ids = get_option('styliiiish_allowed_manager_ids', []);
            if (empty($allowed_ids)) {
                $allowed_ids = [ get_current_user_id() ];
            }
            $base_args['author__in'] = $allowed_ids;
        }



    // Search
    if (!empty($search)) {
        $base_args['s'] = $search;
    }

    // Category
    if ($cat > 0) {
        $base_args['tax_query'] = [
            [
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $cat,
            ]
        ];
    }


/* ============================================
   Handle Status Filter (USER LOGIC)
============================================ */
if ($is_user) {

    if (!empty($status_filter)) {

        switch ($status_filter) {

            case 'active':
                $base_args['post_status'] = ['publish'];
                break;

            case 'pending':
                $base_args['post_status'] = ['pending'];
                break;

            case 'deactivated':
                $base_args['post_status'] = ['draft'];
               $base_args['meta_query'][] = [
                'key'     => '_styliiiish_manual_deactivate',
                'value'   => 'yes',
                'compare' => '=',
            ];

                break;

            case 'uncomplete':
                $base_args['post_status'] = ['draft'];
               $base_args['meta_query'][] = [
                'key'     => '_styliiiish_manual_deactivate',
                'compare' => 'NOT EXISTS'
            ];

                break;
        }

    } else {

        // الافتراضى: كل الحالات ماعدا deactivated
        $base_args['post_status'] = ['publish', 'pending', 'draft'];
    }

} else {

    // OWNER MODE كما هو
    if (!empty($status_filter)) {
        $base_args['post_status'] = [$status_filter];
    } else {
        $base_args['post_status'] = ['publish', 'draft'];
    }
}




    /* ============================================
       Stats (OLD STATS CALL)
    ============================================ */
    $stats = styliiiish_get_products_stats($base_args);


/* ============================================
   USER Pretty Stats (NEW UI ONLY)
============================================ */
if ($is_user) {

    $user_id = get_current_user_id();

    // لازم ناخد كل المنتجات بدون post_status أو meta_query أو tax_query
    $all_products = get_posts([
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'author'         => $user_id,
        'post_status'    => ['publish', 'pending', 'draft'], // مهم جداً
    ]);

    $active      = 0;
    $pending     = 0;
    $uncomplete  = 0;
    $deactivated = 0;

    foreach ($all_products as $pid) {

        $status = get_post_status($pid);
        $manual = get_post_meta($pid, '_styliiiish_manual_deactivate', true) === 'yes';

        // موقوف يدويًا
        if ($manual) {
            $deactivated++;
            continue;
        }

        if ($status === 'publish') {
            $active++;
        }
        elseif ($status === 'pending') {
            $pending++;
        }
        elseif ($status === 'draft') {
            $uncomplete++;
        }
    }

    $stat_active_value      = $active;
    $stat_pending_value     = $pending;
    $stat_uncomplete_value  = $uncomplete;
    $stat_deactivated_value = $deactivated;
}


    /* ============================================
       LIST QUERY
    ============================================ */
    $query = new WP_Query($base_args);
    
    $total_products = $query->found_posts;
    $total_pages    = $query->max_num_pages;
    $offset         = ($paged - 1) * $per_page;

        
    $products = [];
    if ($query->have_posts()) {
        foreach ($query->posts as $post) {
            $product = wc_get_product($post->ID);
            if ($product) $products[] = $product;
        }
    }


    /* ============================================
       OUTPUT START
    ============================================ */
    ob_start();
    ?>

<div class="styliiiish-stats-bar">

    <?php if ($is_user): ?>

        <div class="pretty-stats">

            <div class="pretty-stat-box stat-active-pretty">
                <div class="pretty-dot"></div>
                <div class="pretty-label">Active:</div>
                <div class="pretty-value"><?php echo esc_html($stat_active_value); ?></div>
            </div>

            <div class="pretty-stat-box stat-pending-pretty">
                <div class="pretty-dot"></div>
                <div class="pretty-label">Pending:</div>
                <div class="pretty-value"><?php echo esc_html($stat_pending_value); ?></div>
            </div>

            <div class="pretty-stat-box stat-uncomplete-pretty">
                <div class="pretty-dot"></div>
                <div class="pretty-label">Uncomplete:</div>
                <div class="pretty-value"><?php echo esc_html($stat_uncomplete_value); ?></div>
            </div>

            <div class="pretty-stat-box stat-deactivated-pretty">
                <div class="pretty-dot"></div>
                <div class="pretty-label">Deactivated:</div>
                <div class="pretty-value"><?php echo esc_html($stat_deactivated_value); ?></div>
            </div>

        </div>

    <?php else: ?>

        <div class="styliiiish-stat-box stat-published">
            <div class="stat-inner">
                Published: <?php echo esc_html($stats['publish']); ?>
            </div>
        </div>

        <div class="styliiiish-stats-row">
            <div class="styliiiish-stat-box stat-total">
                <div class="stat-inner">
                    Total: <?php echo esc_html($stats['total']); ?>
                </div>
            </div>

            <div class="styliiiish-stat-box stat-draft">
                <div class="stat-inner">
                    Draft: <?php echo esc_html($stats['draft']); ?>
                </div>
            </div>
        </div>

    <?php endif; ?>

</div>































                <div class="pagination-wrapper" style="margin-top: 10px;">
                <?php if ($total_products > 0) : ?>
                    <strong>
                        Showing <?php echo esc_html($offset + 1); ?> - 
                        <?php echo esc_html(min($offset + $per_page, $total_products)); ?>
                        of <?php echo esc_html($total_products); ?>
                    </strong>
                    <br><br>
            
                    <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                        <a href="#"
                           class="button styliiiish-page-link <?php echo $i == $paged ? 'button-primary styliiiish-current-page' : ''; ?>"
                           data-page="<?php echo esc_attr($i); ?>">
                            <?php echo esc_html($i); ?>
                        </a>
                    <?php endfor; ?>
                <?php endif; ?>
            </div>












    <table class="owner-products-table">
        <thead>
        <tr>
            <th><input type="checkbox" id="styliiiish-select-all"></th>
            <th>Image</th>
            <th>Name</th>
            <th>Description</th>
            <th>Attributes</th>
            <th style="width:70px">Price</th>
            <th>Categories</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>












        <tbody>
        <?php if (!empty($products)) : ?>
            <?php foreach ($products as $p) : ?>
                <?php
                $product_id   = $p->get_id();
                $terms        = wp_get_post_terms($product_id, 'product_cat', ['fields' => 'names']);
                $price        = $p->get_regular_price();

                // Description
                $desc_full  = wp_strip_all_tags( get_post_field('post_content', $product_id) );
                $desc_short = wp_trim_words( $desc_full, 30 );

                // Attributes text
                $attr_text = styliiiish_get_attributes_text( $product_id );
                if ( $attr_text === '' ) {
                    $attr_text = get_post_meta( $product_id, '_styliiiish_inline_attributes', true );
                }
                $attr_full  = wp_strip_all_tags($attr_text);
                $attr_short = $attr_full ? wp_trim_words($attr_full, 8) : '—';
                ?>
                
                
                
                
                
                
                
                <tr data-row-id="<?php echo esc_attr($product_id); ?>">
                    <td>
                        <input type="checkbox" class="styliiiish-row-check" value="<?php echo esc_attr($product_id); ?>">
                    </td>











                    <td class="styliiiish-image-cell" data-id="<?php echo esc_attr($product_id); ?>">
                        <div class="styliiiish-image-wrapper">
                            <?php echo $p->get_image('thumbnail'); ?>
                            <div class="styliiiish-image-overlay">Edit image</div>
                        </div>
                    </td>











                    <td data-label="name">
                        <span
                            class="inline-edit"
                            contenteditable="true"
                            data-id="<?php echo esc_attr($product_id); ?>"
                            data-field="title"><?php echo esc_html($p->get_name()); ?></span>
                    </td>











                    <td data-label="Description">
                        <span
                            class="inline-edit inline-description"
                            contenteditable="true"
                            data-id="<?php echo esc_attr($product_id); ?>"
                            data-field="post_content"
                            data-full="<?php echo esc_attr($desc_full); ?>">
                            <?php echo esc_html($desc_short); ?>
                        </span>
                    </td>









                    <td data-label="Attributes">
                        <button 
                            class="btn-edit-attrs" 
                            data-id="<?php echo esc_attr($product_id); ?>">
                            Edit
                        </button>
                    </td>












                    <td data-label="Price">
                        <span
                            class="inline-edit"
                            contenteditable="true"
                            data-id="<?php echo esc_attr($product_id); ?>"
                            data-field="price"><?php echo $price !== '' ? esc_html($price) . ' EGP' : '—'; ?></span>
                    </td>









                    <td class="cats-cell" data-label="Categories">
                        <div class="cats-wrap">
                            <div class="cats-text" id="cat-display-<?php echo esc_attr($product_id); ?>">
                                <?php echo !empty($terms) ? esc_html(implode(', ', $terms)) : '—'; ?>
                            </div>

                            <?php if (!$is_user): ?>
                                <button
                                    type="button"
                                    class="button button-small edit-cats-btn cats-edit-btn"
                                    data-product="<?php echo esc_attr($product_id); ?>">
                                    Edit
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>









                                                
                                                
                    
                                                    
                                                    




			<td data-label="Status">
			<?php if ($is_user): ?>

				<?php
				$is_deactivated = get_post_meta($product_id, '_styliiiish_manual_deactivate', true) === 'yes';
				$status         = $p->get_status();

				$reject_label = get_post_meta($product_id, '_styliiiish_reject_reason_label', true);
				$reject_note  = get_post_meta($product_id, '_styliiiish_reject_reason_note', true);
				?>

				<?php if ($is_deactivated): ?>
					<span class="sty-status status-deactivated">Deactivated ??</span>

				<?php elseif ($status === 'publish'): ?>
					<span class="sty-status status-active">Active ??</span>

				<?php elseif ($status === 'pending'): ?>
					<span class="sty-status status-pending">Pending</span>

				<?php else: // draft ?>

					<span class="sty-status status-uncomplete">
						<?php echo $reject_label ? 'Rejected ?' : 'Incomplete ??'; ?>
					</span>

					<?php if ($reject_label): ?>
						<div class="sty-reject-reason-box">
							<strong>Reason:</strong> <?php echo esc_html($reject_label); ?>

							<?php if ($reject_note): ?>
								<div class="sty-reject-note">
									<?php echo esc_html($reject_note); ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>

				<?php endif; ?>

			<?php else: ?>

				<!-- OWNER VIEW -->
				<select class="inline-status" data-id="<?php echo esc_attr($product_id); ?>">
					<option value="publish" <?php selected($p->get_status(), 'publish'); ?>>Published</option>
					<option value="draft" <?php selected($p->get_status(), 'draft'); ?>>Draft</option>
				</select>

			<?php endif; ?>
			</td>

























                   <td data-label="Actions">
                        <div class="owner-action-buttons">
                            <a class="owner-action-btn btn-edit"
                               href="<?php echo esc_url(admin_url('post.php?post=' . $product_id . '&action=edit')); ?>">Edit</a>
                    
                            <a class="owner-action-btn btn-view"
                               target="_blank"
                               href="<?php echo esc_url(get_permalink($product_id)); ?>">View</a>
                    
                            <?php if (!$is_user): ?>
                    
                                <a class="owner-action-btn btn-duplicate"
                                   href="#"
                                   data-id="<?php echo esc_attr($product_id); ?>">
                                    Duplicate
                                </a>
                    
                            <?php else: ?>
                                <?php
                                $is_deactivated = get_post_meta($product_id, '_styliiiish_manual_deactivate', true) === 'yes';
                                ?>
                    
                                <?php if ($is_deactivated): ?>
                                    <a href="#"
                                       class="owner-action-btn btn-activate-user"
                                       data-id="<?php echo esc_attr($product_id); ?>">
                                        ⚡ Activate
                                    </a>
                                <?php else: ?>
                                    <a href="#"
                                       class="owner-action-btn btn-deactivate-user"
                                       data-id="<?php echo esc_attr($product_id); ?>">
                                        ❌ Deactivate
                                    </a>
                                <?php endif; ?>
                    
                            <?php endif; ?>
                    
                            <a href="#"
                               class="owner-action-btn btn-delete"
                               data-id="<?php echo esc_attr($product_id); ?>">
                                Delete
                            </a>
                        </div>
                    </td>





                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="9">No products found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
    
    
    
    
    
    
    
    
    
    
    

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    

           <div class="pagination-wrapper" style="margin-top: 10px;">
            <?php if ($total_products > 0) : ?>
                <strong>
                    Showing <?php echo esc_html($offset + 1); ?> - 
                    <?php echo esc_html(min($offset + $per_page, $total_products)); ?>
                    of <?php echo esc_html($total_products); ?>
                </strong>
                <br><br>
        
                <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                    <a href="#"
                       class="button styliiiish-page-link <?php echo $i == $paged ? 'button-primary styliiiish-current-page' : ''; ?>"
                       data-page="<?php echo esc_attr($i); ?>">
                        <?php echo esc_html($i); ?>
                    </a>
                <?php endfor; ?>
            <?php endif; ?>
        </div>












                <?php if ($is_user): ?>
                <div class="styliiiish-user-tips-box">
                    <h3 style="margin-bottom:8px;">Hey beautiful! Welcome to Styliiiish Marketplace 👗✨</h3>
                    <p>You can upload your dress and start getting buyers in just <strong>5 smooth, simple steps</strong>:</p>
                
                    <ol>
                        <li><strong>Pick a cute and clear name</strong> for your dress.</li>
                        <li><strong>Describe it honestly & with love</strong>—mention condition, fit, and details.</li>
                        <li><strong>Add clear & pretty photos</strong> (event photos get more views!).</li>
                        <li><strong>Select the correct color, size, weight & condition</strong> from the Edit button.</li>
                        <li><strong>Set your price:</strong> write it in English numbers.</li>
                    </ol>
                
                    <p><strong>Notes:</strong></p>
                    <ul>
                        <li style="color:red;"><strong>Marketplace fee:</strong> Styliiiish applies a <strong>50%</strong> fee deducted from price you enter.</li>
                        <li>Write everything in English — our system auto-translates to Arabic ❤️</li>
                        <li>No need to press Save — everything saves instantly & automatically 🎉</li>
                        <li>Better photos + clear details = faster selling 💸✨</li>
                        
                    </ul>
                
                    <p>Styliiiish Marketplace is here to make your selling journey smooth & fun 💕</p>
                
                    <p style="margin-top:10px;">
                        For full details, visit:
                        <a href="https://l.styliiiish.com/%e2%ad%90-how-to-sell-your-dress-in-6-easy-steps-on-styliiiish-marketplace/"
                           target="_blank" rel="noopener noreferrer">
                            ⭐ How to Sell Your Dress in 6 Easy Steps on Styliiiish Marketplace
                        </a>
                    </p>
                </div>
                <?php endif; ?>

















        <div id="attrModal" class="attr-modal" style="display:none;">
            <div class="attr-modal-content">
                <h3>Select Attributes</h3>

                <div id="attrSelectorWrap"></div>

                <button id="saveAttrChanges" class="btn-save">Save</button>
                <button id="closeAttrModal" class="btn-close">Close</button>
            </div>
        </div>

    </div>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    <?php
    return ob_get_clean();
}



/* ===============================
   AJAX: Products List (Pagination + Filters)
================================== */
add_action('wp_ajax_styliiiish_manage_products_list', function () {

    if (!is_user_logged_in()) {
        wp_die('No permission');
    }

    $page   = isset($_POST['page']) ? max(1, intval($_POST['page'])) : 1;
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    $cat    = isset($_POST['cat']) ? intval($_POST['cat']) : 0;
    $status = isset($_POST['status']) ? sanitize_key($_POST['status']) : '';
    
    
    
    
    
    
    
    
    
    

    if (isset($_POST['mode'])) {
    if ($_POST['mode'] === 'user') {
        $mode = 'user';
    } elseif ($_POST['mode'] === 'vendor') {
        $mode = 'vendor';
    } else {
        $mode = 'owner';
    }
    } else {
        $mode = 'owner';
    }
















    echo styliiiish_build_manage_products_content($page, $search, $cat, $status, $mode);
    wp_die();
});









/* ===============================
   Helper: Stats for current filter
================================== */
function styliiiish_get_products_stats($base_args)
{
    $stats = [
        'total'    => 0,
        'publish'  => 0,
        'draft'    => 0,
        'pending'  => 0, // NEW
    ];

    // Total
    $args_total                = $base_args;
    $args_total['post_status'] = ['publish', 'draft'];
    $args_total['posts_per_page'] = 1;
    $args_total['paged']       = 1;
    $q_total                   = new WP_Query($args_total);
    $stats['total']            = (int) $q_total->found_posts;

    // Published
    $args_pub                = $base_args;
    $args_pub['post_status'] = ['publish'];
    $args_pub['posts_per_page'] = 1;
    $args_pub['paged']       = 1;
    $q_pub                   = new WP_Query($args_pub);
    $stats['publish']        = (int) $q_pub->found_posts;

    // Draft
    $args_draft                = $base_args;
    $args_draft['post_status'] = ['draft'];
    $args_draft['posts_per_page'] = 1;
    $args_draft['paged']       = 1;
    $q_draft                   = new WP_Query($args_draft);
    $stats['draft']            = (int) $q_draft->found_posts;
    
    // ⭐ Pending (NEW)
    $args_pending                = $base_args;
    $args_pending['post_status'] = ['pending'];
    $args_pending['posts_per_page'] = 1;
    $args_pending['paged']       = 1;
    $q_pending                   = new WP_Query($args_pending);
    $stats['pending']            = (int) $q_pending->found_posts;
    
    
    return $stats;
}









function styliiiish_auto_pending_check( $product_id ) {

    // لو المنتج متوقف يدويًا → متقربش منه نهائيًا
    $manual_off = get_post_meta($product_id, '_styliiiish_manual_deactivate', true);
    if ($manual_off === 'yes') {
        return;
    }

    $product = wc_get_product($product_id);
    if (!$product) return;

    $is_complete = true;

    // NAME
    $name = $product->get_name();
    if ( empty($name) || strlen($name) < 3 ) {
        $is_complete = false;
    }

    // FEATURED IMAGE
    $thumb = get_post_thumbnail_id($product_id);
    if (!$thumb) {
        $is_complete = false;
    }

    // DESCRIPTION
    $desc = get_post_field('post_content', $product_id);
    $desc_clean = trim( wp_strip_all_tags((string)$desc) );
    if ( empty($desc_clean) || strlen($desc_clean) < 20 ) {
        $is_complete = false;
    }

    // PRICE
    $price = $product->get_regular_price();
    if ( empty($price) || floatval($price) <= 0 ) {
        $is_complete = false;
    }

    // REQUIRED ATTRIBUTES
    $required_taxonomies = [
        'pa_color',
        'pa_product-condition',
        'pa_size',
        'pa_weight',
    ];

    foreach ($required_taxonomies as $tax) {
        $terms = wp_get_post_terms($product_id, $tax, ['fields' => 'ids']);
        if ( empty($terms) ) {
            $is_complete = false;
            break;
        }
    }

    $current_status = get_post_status($product_id);

    // ⭐ RULE: ACTIVE + COMPLETE → KEEP ACTIVE
    if ($current_status === 'publish' && $is_complete) {
        return;
    }

    // ⭐ RULE: ACTIVE + INCOMPLETE → MAKE DRAFT
    if ($current_status === 'publish' && !$is_complete) {
        wp_update_post([
            'ID'          => $product_id,
            'post_status' => 'draft'
        ]);
        return;
    }

    // ⭐ RULE: PENDING OR DRAFT behavior
    if ($is_complete) {
        if ($current_status !== 'pending') {
            wp_update_post([
                'ID'          => $product_id,
                'post_status' => 'pending'
            ]);
        }
    } else {
        if ($current_status !== 'draft') {
            wp_update_post([
                'ID'          => $product_id,
                'post_status' => 'draft'
            ]);
        }
    }
}





/* ===============================
   AJAX: Trigger Final Pending Check
================================== */
add_action('wp_ajax_styliiiish_trigger_pending_check', function () {

    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

    if (!$product_id) {
        wp_send_json_error(['message' => 'Invalid ID']);
    }

    styliiiish_auto_pending_check($product_id);

    wp_send_json_success(['status' => 'checked']);
});



add_action('wp_ajax_styliiiish_force_pending_check', function () {

    $product_id = intval($_POST['product_id']);
    if (!$product_id) {
        wp_send_json_error(['message' => 'Invalid ID']);
    }

    styliiiish_auto_pending_check($product_id);

    wp_send_json_success(['message' => 'Check applied']);
});








/* ===============================
   AJAX: User Deactivate Product
================================== */
add_action('wp_ajax_styliiiish_user_deactivate_product', function () {

    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

    if (!$product_id || get_post_type($product_id) !== 'product') {
        wp_send_json_error(['message' => 'Invalid product']);
    }

    $user_id        = get_current_user_id();
    $product_author = (int) get_post_field('post_author', $product_id);
    $is_manager     = current_user_can('manage_woocommerce');
    $is_owner       = ($user_id && $user_id === $product_author);

    // يسمح للـ owner + مدير
    if (!$is_manager && !$is_owner) {
        wp_send_json_error(['message' => 'No permission']);
    }

    // علّم إنه متوقف يدويًا
    update_post_meta($product_id, '_styliiiish_manual_deactivate', 'yes');

    // خليه draft
    $update = wp_update_post([
        'ID'          => $product_id,
        'post_status' => 'draft',
    ], true);

    if (is_wp_error($update)) {
        wp_send_json_error(['message' => $update->get_error_message()]);
    }

    $status_html = '<span class="sty-status status-deactivated">Deactivated 🚫</span>';

    wp_send_json_success([
        'status_html' => $status_html,
    ]);
});


/* ===============================
   AJAX: User Activate Product
================================== */
add_action('wp_ajax_styliiiish_user_activate_product', function () {

    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

    if (!$product_id || get_post_type($product_id) !== 'product') {
        wp_send_json_error(['message' => 'Invalid product']);
    }

    $user_id        = get_current_user_id();
    $product_author = (int) get_post_field('post_author', $product_id);
    $is_manager     = current_user_can('manage_woocommerce');
    $is_owner       = ($user_id && $user_id === $product_author);

    if (!$is_manager && !$is_owner) {
        wp_send_json_error(['message' => 'No permission']);
    }

    // إزالة deactivation flag
    delete_post_meta($product_id, '_styliiiish_manual_deactivate');

    // 🔥 تشغيل دالة التحقق بدل إجبار الحالة
    styliiiish_auto_pending_check($product_id);

    // جلب الحالة الجديدة بعد الفحص
    $new_status = get_post_status($product_id);

    // تجهيز الـ HTML بناءً على الحالة
    if ($new_status === 'pending') {
        $status_html = '<span class="sty-status status-pending">Pending </span>';
    } elseif ($new_status === 'draft') {
        $status_html = '<span class="sty-status status-uncomplete">Uncomplete 😕</span>';
    } else {
        $status_html = '<span class="sty-status status-active">Active ⚡</span>';
    }

    wp_send_json_success([
        'status'      => $new_status,
        'status_html' => $status_html,
    ]);
});


































