<?php


/**************** Manage-products-ajax.php ************************/



add_action('wp_enqueue_scripts', function () {

    wp_register_script('global-js', false);

    wp_enqueue_script('global-js');

    wp_localize_script('global-js', 'ajax_object', [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]);

});




add_action('pre_get_posts', function ($q) {

    if (is_admin()) return;
    if (!$q->is_main_query()) return;
    if (!is_user_logged_in()) return;

    if (is_page('vendor-dashboard')) {

        $q->set('author', get_current_user_id());
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
     check_ajax_referer('ajax_nonce','nonce');


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
        $template_id = 29323;  // OWNER TEMPLATE
    } else {
        $template_id = 29321;  // USER TEMPLATE
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
         'edit'   => true
    ]);
});

 
 
 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/
 
            /*             ############         AJAX: Get ALL product attributes + selected terms        ##################                              */

 /*_______________________________________________________________________________________________________________________________________________________*/
 /*_______________________________________________________________________________________________________________________________________________________*/ 
 
add_action('wp_ajax_styliiiish_get_attributes', function () {
    check_ajax_referer('ajax_nonce','nonce');


    $pid = intval($_POST['product_id'] ?? 0);
    $cat = intval($_POST['cat_id'] ?? 0);


    /* =================================
       MODE DETECTION (NEW - SAFE)
    ================================= */

    $is_add_mode  = (!$pid && $cat);   // Add Product
    $is_edit_mode = ($pid > 0);        // Edit Product


    /* =================================
       OLD LOGIC (UNCHANGED)
    ================================= */

    if ($is_edit_mode) {

        $product = wc_get_product($pid);

        if (!$product) {
            wp_send_json_error(['message' => 'Not found']);
        }
    }


    if (!$is_edit_mode && !$is_add_mode) {
        wp_send_json_error(['message' => 'Invalid product']);
    }


    /* ===========================
       Get Product Category
    =========================== */

    if ($is_edit_mode) {

        // OLD BEHAVIOR (KEEP)
        $cats = wp_get_post_terms($pid,'product_cat',['fields'=>'ids']);
        $main_cat = $cats[0] ?? 0;

    } else {

        // NEW (ADD MODE)
        $main_cat = $cat;

    }



    /* ===========================
       Get Allowed Attributes
    =========================== */

    $map = get_option('wf_category_attributes_map', []);

    $allowed = $map[$main_cat] ?? [];


    if (empty($allowed)) {
        wp_send_json_success([]); // مفيش Attributes مسموحة
    }


    /* ===========================
       Load Woo Attributes
    =========================== */

    $taxes = wc_get_attribute_taxonomies();

    $data = [];


    foreach ($taxes as $tax) {

        $taxonomy = 'pa_' . $tax->attribute_name;

        // ❗ فلترة حسب المسموح
        if (!in_array($taxonomy, $allowed, true)) {
            continue;
        }


        /* ===================
           Get Terms
        =================== */

        $terms = get_terms([
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
        ]);

        if(!$terms) continue;


        $options = [];

        foreach ($terms as $t) {

            $options[] = [
                'label' => $t->name,
                'value' => $t->slug
            ];
        }


        /* ===================
           Selected Value
        =================== */

        if ($is_edit_mode) {

            // OLD (KEEP)
            $selected = wc_get_product_terms(
                $pid,
                $taxonomy,
                ['fields' => 'slugs']
            );

        } else {

            // NEW (ADD MODE)
            $selected = [];

        }


        $data[] = [

            'taxonomy' => $taxonomy,

            'label'    => wc_attribute_label($taxonomy),

            'options'  => $options,

            'selected' => $selected[0] ?? '',

        ];
    }


    wp_send_json_success($data);

});


add_action('wp_ajax_styliiiish_save_attributes', function () {
    check_ajax_referer('ajax_nonce','nonce');



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
    check_ajax_referer('ajax_nonce','nonce');

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

    $thumb_id  = get_post_thumbnail_id($product_id);

    $thumb_url = $thumb_id
        ? wp_get_attachment_image_url($thumb_id, 'medium')
        : '';
    
    wp_send_json_success([
        'html'     => $html,
        'main'     => $main_html,
        'main_url' => $thumb_url
    ]);

});


 
 
/* ===============================
   AJAX: Get Images Modal Content
================================== */
add_action('wp_ajax_styliiiish_get_images', function () {
    check_ajax_referer('ajax_nonce','nonce');

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

    $thumb_id  = get_post_thumbnail_id($product_id);

    $thumb_url = $thumb_id
        ? wp_get_attachment_image_url($thumb_id, 'medium')
        : '';
    
    wp_send_json_success([
        'html'     => $html,
        'main'     => $main_html,
        'main_url' => $thumb_url
    ]);

});


/* ===============================
   AJAX: Add/Attach image to product (from Media Library)
================================== */

add_action('wp_ajax_styliiiish_add_image_to_product', function () {
    check_ajax_referer('ajax_nonce','nonce');

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

    $thumb_id  = get_post_thumbnail_id($product_id);

    $thumb_url = $thumb_id
        ? wp_get_attachment_image_url($thumb_id, 'medium')
        : '';
    
    wp_send_json_success([
        'html'     => $html,
        'main'     => $main_html,
        'main_url' => $thumb_url
    ]);

});



/* ===============================
   AJAX: Set Featured Image
================================== */
add_action('wp_ajax_styliiiish_set_featured_image', function () {
    check_ajax_referer('ajax_nonce','nonce');

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

    $thumb_id  = get_post_thumbnail_id($product_id);

        $thumb_url = $thumb_id
            ? wp_get_attachment_image_url($thumb_id, 'medium')
            : '';
        
        wp_send_json_success([
            'html'     => $html,
            'main'     => $main_html,
            'main_url' => $thumb_url
        ]);

});


/* ===============================
   AJAX: Remove image (featured or gallery)
================================== */
add_action('wp_ajax_styliiiish_remove_image', function () {
    check_ajax_referer('ajax_nonce','nonce');

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

    $thumb_id  = get_post_thumbnail_id($product_id);

    $thumb_url = $thumb_id
        ? wp_get_attachment_image_url($thumb_id, 'medium')
        : '';
    
    wp_send_json_success([
        'html'     => $html,
        'main'     => $main_html,
        'main_url' => $thumb_url
    ]);

});






















/* ===============================
   AJAX: Load Categories
================================== */
add_action('wp_ajax_styliiiish_get_cats', function () {
    check_ajax_referer('ajax_nonce','nonce');

    if( ! current_user_can('edit_products') ){
        wp_send_json_error('No permission');
    }

    $pid = intval($_POST['product_id']);

    if(!$pid){
        wp_send_json_error('Invalid product');
    }

    $current = wp_get_post_terms($pid,'product_cat',['fields'=>'ids']);


    /* ============================
       Vendor / Owner Detection
    ============================ */

    $is_vendor = ! current_user_can('manage_woocommerce');


    /* ============================
       Load Categories
    ============================ */

    if($is_vendor){

        // Vendor → فلترة
        $allowed = get_option('wf_allowed_vendor_categories', []);

        if(!empty($allowed)){

            $cats = get_terms([
                'taxonomy'   => 'product_cat',
                'hide_empty' => false,
                'include'    => $allowed
            ]);

        } else {

            $cats = []; // ممنوع كله
        }

    } else {

        // Owner / Admin → كله
        $cats = get_terms([
            'taxonomy'   => 'product_cat',
            'hide_empty' => false
        ]);
    }


    /* ============================
       Build Response
    ============================ */

    $data = [];

    foreach($cats as $c){

        $data[] = [
            'id'      => $c->term_id,
            'name'    => $c->name,
            'checked' => in_array($c->term_id,$current,true)
        ];
    }


    wp_send_json_success($data);

});

/* ===============================
   AJAX: Save Categories
================================== */
add_action('wp_ajax_styliiiish_save_cats', function () {
    check_ajax_referer('ajax_nonce','nonce');

    if( ! current_user_can('edit_products') ){
        wp_send_json_error('No permission');
    }

    $product_id = intval($_POST['product_id']);

    if(!$product_id){
        wp_send_json_error('Invalid product');
    }

    $cats = array_map('intval', $_POST['cats'] ?? []);

        $is_vendor = ! current_user_can('manage_woocommerce');
        
        if($is_vendor){
        
            $allowed = get_option('wf_allowed_vendor_categories', []);
        
            $cats = array_intersect($cats, $allowed);
        }


    wp_set_post_terms($product_id, $cats, 'product_cat');

    $names = wp_get_post_terms(
        $product_id,
        'product_cat',
        ['fields'=>'names']
    );

    wp_send_json_success([
        'names' => $names
    ]);

});

/* ===============================
   AJAX: Update Product Status
================================== */
add_action('wp_ajax_styliiiish_update_status', function () {
    check_ajax_referer('ajax_nonce','nonce');

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
    check_ajax_referer('ajax_nonce','nonce');

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
    check_ajax_referer('ajax_nonce','nonce');

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
    check_ajax_referer('ajax_nonce','nonce');

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
    check_ajax_referer('ajax_nonce','nonce');

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
                    continue 2;
                }
                $deleted = wp_delete_post($id, true);
                if ($deleted) {
                    $affected++;
                }
                break;

            case 'publish':
            case 'draft':
                if (!current_user_can('edit_post', $id)) {
                    continue 2;
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
function styliiiish_get_manage_products_data(
  $paged = 1,
  $search = '',
  $cat = 0,
  $status_filter = '',
  $mode = 'owner'
){

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

include plugin_dir_path(__FILE__) . '../../add-product/modal.php';
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
    return [

  'products' => $products,

  'stats' => is_array($stats) ? $stats : [],

  'pretty_stats' => $is_user ? [
    'active'      => $stat_active_value,
    'pending'     => $stat_pending_value,
    'uncomplete'  => $stat_uncomplete_value,
    'deactivated' => $stat_deactivated_value,
  ] : [],

  'pagination' => [
    'total'  => $total_products,
    'pages'  => $total_pages,
    'offset' => $offset,
    'page'   => $paged,
    'per'    => $per_page
  ],

  'mode'    => $mode,
  'is_user' => $is_user,

];
}




/* ===============================
   AJAX: Products List (Pagination + Filters)
================================== */
add_action('wp_ajax_styliiiish_manage_products_list', function () {
    check_ajax_referer('ajax_nonce','nonce');

    if (!is_user_logged_in()) {
        wp_die('No permission');
    }

    $page   = max(1, intval($_POST['page'] ?? 1));
    $search = sanitize_text_field($_POST['search'] ?? '');
    $cat    = intval($_POST['cat'] ?? 0);
    $status = sanitize_key($_POST['status'] ?? '');
    $mode   = sanitize_key($_POST['mode'] ?? 'owner');


    /* Get data */
    $data = styliiiish_get_manage_products_data(
        $page,
        $search,
        $cat,
        $status,
        $mode
    );

    if(!is_array($data)){
        wp_die('Invalid data');
    }


    /* Layout */
    $layout = get_option('wf_products_layout','table');

    $base = plugin_dir_path(__FILE__) . 'views/manage-products/';

    $base = realpath($base);

    if(!$base){
        wp_die('Views folder missing');
    }

    $file = $base . '/' . $layout . '.php';

    if(!file_exists($file)){
        $file = $base . '/table.php';
    }


    /* Vars */
    $products     = $data['products'] ?? [];
    $stats        = $data['stats'] ?? [];
    $pretty_stats = $data['pretty_stats'] ?? [];
    $pagination   = $data['pagination'] ?? [];
    $is_user      = $data['is_user'] ?? false;
    $mode         = $data['mode'] ?? 'owner';


    /* Render */
    if(file_exists($file)){
        include $file;
    }else{
        echo 'Layout file not found';
    }

    wp_die();
});












/* ===============================
   Helper: Stats for current filter
================================== */
function styliiiish_get_products_stats($base_args){
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
    check_ajax_referer('ajax_nonce','nonce');

    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

    if (!$product_id) {
        wp_send_json_error(['message' => 'Invalid ID']);
    }

    styliiiish_auto_pending_check($product_id);

    wp_send_json_success(['status' => 'checked']);
});



add_action('wp_ajax_styliiiish_force_pending_check', function () {
    check_ajax_referer('ajax_nonce','nonce');

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
    check_ajax_referer('ajax_nonce','nonce');

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
    check_ajax_referer('ajax_nonce','nonce');

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
























/*****************************************        المودل الى بيفتح عند اضافة المنتج ***************************************/

add_action('wp_ajax_styliiiish_add_product','sty_add_product');

function sty_add_product(){
    check_ajax_referer('ajax_nonce','nonce');

    if(!is_user_logged_in()){
        wp_send_json_error(['msg'=>'Login required']);
    }

    if(empty($_POST['title'])){
        wp_send_json_error(['msg'=>'No title']);
    }

    $pid   = intval($_POST['product_id']);
    $title = sanitize_text_field($_POST['title']);
    $desc  = wp_kses_post($_POST['desc'] ?? '');
    $price = floatval($_POST['regular_price'] ?? 0);
    $sale  = floatval($_POST['sale_price'] ?? 0);
    $cat   = intval($_POST['cats'] ?? 0);

    // صلاحيات
    if($pid && !current_user_can('edit_post',$pid)){
        wp_send_json_error(['msg'=>'No permission']);
    }

    /* ========== INSERT / UPDATE ========== */

    if($pid && get_post_type($pid)==='product'){

        wp_update_post([
            'ID'           => $pid,
            'post_title'   => $title,
            'post_content' => $desc,
            'post_status'  => 'pending'
        ]);

        $id = $pid;

    }else{

        $id = wp_insert_post([
            'post_type'    => 'product',
            'post_title'   => $title,
            'post_content' => $desc,
            'post_status'  => 'pending',
            'post_author'  => get_current_user_id()
        ]);

    }

    if(!$id || is_wp_error($id)){
        wp_send_json_error(['msg'=>'Insert failed']);
    }

    /* ========== PRICE ========== */

    update_post_meta($id,'_regular_price',$price);

    if($sale > 0 && $sale < $price){

        update_post_meta($id,'_sale_price',$sale);
        update_post_meta($id,'_price',$sale);

    }else{

        delete_post_meta($id,'_sale_price');
        update_post_meta($id,'_price',$price);
    }

    /* ========== CATEGORY ========== */

    if($cat){
        wp_set_object_terms($id,[$cat],'product_cat');
    }

    /* ========== ATTRIBUTES ========== */

if(!empty($_POST['attrs']) && is_array($_POST['attrs'])){

   $product_attributes = get_post_meta($id, '_product_attributes', true);

   if(!is_array($product_attributes)){
      $product_attributes = [];
   }

   foreach($_POST['attrs'] as $tax => $val){

      $tax = wc_sanitize_taxonomy_name($tax);
      $val = sanitize_text_field($val);

      // اربط الـ term
      wp_set_object_terms($id, $val, $tax, false);

      // خزّن Attribute بشكل صحيح
      $product_attributes[$tax] = [
         'name'         => $tax,
         'value'        => '',
         'position'     => count($product_attributes),
         'is_visible'   => 1,
         'is_variation' => 0,
         'is_taxonomy'  => 1
      ];
   }

   update_post_meta($id, '_product_attributes', $product_attributes);
}




    $thumb_id = get_post_thumbnail_id($id);

    $img = $thumb_id
        ? wp_get_attachment_image_url($thumb_id,'medium')
        : '';
    
    wp_send_json_success([
       'id'    => $id,
       'image' => $img
    ]);

}







add_action('wp_ajax_styliiiish_get_product_for_edit', function(){

    if(
        empty($_POST['nonce']) ||
        !wp_verify_nonce($_POST['nonce'],'ajax_nonce')
    ){
        wp_send_json_error('Bad nonce');
    }

    if(!is_user_logged_in()){
        wp_send_json_error('Login required');
    }

    $pid = intval($_POST['product_id']);

    if(!$pid || get_post_type($pid) !== 'product'){
        wp_send_json_error('Invalid product');
    }

    $post = get_post($pid);

    if(!$post){
        wp_send_json_error('Not found');
    }

    // السعر
    $price = get_post_meta($pid,'_regular_price',true);
    $sale = get_post_meta($pid,'_sale_price',true);


    // الكاتيجوري
    $cats = wp_get_post_terms($pid,'product_cat',['fields'=>'ids']);

    $thumb_id = get_post_thumbnail_id($pid);

        $img = $thumb_id
            ? wp_get_attachment_image_url($thumb_id,'medium')
            : '';

        /* ========== GET ATTRIBUTES (FROM PRODUCT) ========== */

$attrs = [];

$product = wc_get_product($pid);

if($product){

   $attributes = $product->get_attributes();

   foreach($attributes as $attr){

      // لو taxonomy attribute
      if($attr->is_taxonomy()){

         $tax = $attr->get_name(); // pa_color

         $terms = wp_get_post_terms($pid, $tax, ['fields'=>'slugs']);

         if($terms){
            $attrs[$tax] = $terms[0];
         }

      }
      // لو custom attribute
      else{

         $name = $attr->get_name();

         $vals = $attr->get_options();

         if(!empty($vals)){
            $attrs[$name] = $vals[0];
         }
      }
   }
}

        

    wp_send_json_success([
    
        'id'    => $pid,
        'title' => $post->post_title,
        'desc'  => $post->post_content,
        'price' => $price,
        'sale'  => $sale,
        'cats'  => $cats,
        'attrs' => $attrs,
        'image' => $img // ✅ الصورة
    
    ]);

});




