<?php
/**
 * Functions: Functions
 * Location: includes/functions.php
 */
 
 
 // Hide admin bar for shop managers ONLY


 
 

 
 
add_filter('show_admin_bar', function($show) {

    if (current_user_can('shop_manager')) {
        return false; // hide admin bar
    }

    return $show; // keep it normally for admins
});
 
 
/**
 * USER Manage Products (User Mode)
 *
 * Shortcode: [styliiiish_user_manage_products]
 */
function styliiiish_user_manage_products_shortcode() {

    if (!is_user_logged_in()) {
        return '<p>You must be logged in to see your products.</p>';
    }

    ob_start();

    echo '<div class="styliiiish-user-manage-products">';
    echo '<h2 style="margin-bottom:20px;">My Dresses</h2>';

    // نفس نسخة المانجر لكن بمود "user"
    styliiiish_render_manage_products('user');

    echo '</div>';

    return ob_get_clean();
}
add_shortcode('styliiiish_user_manage_products', 'styliiiish_user_manage_products_shortcode');


add_action('wp_enqueue_scripts', function () {

    if ( ! (is_page('my-dresses') || is_page('my-dressess')) ) {
        return;
    }

   // wp_enqueue_media();

    // نفس CSS بتاعة الأونر
    wp_enqueue_style(
        'sty-owner-css',
        WF_OWNER_DASHBOARD_URL . 'assets/css/owner-style.css',
        [],
        time()
    );

    wp_enqueue_style(
        'sty-owner-mobile-css',
        WF_OWNER_DASHBOARD_URL . 'assets/css/mobile.css',
        ['sty-owner-css'],
        time()
    );

    // Select2
    wp_enqueue_style(
        'select2-css',
        'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
    );

    wp_enqueue_script(
        'select2-js',
        'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
        ['jquery'],
        null,
        true
    );

    // SweetAlert
    wp_enqueue_script(
        'sweetalert2',
        'https://cdn.jsdelivr.net/npm/sweetalert2@11',
        ['jquery'],
        null,
        true
    );

    // ✅ استخدم نفس سكربت الأونر للـ User Mode
    wp_enqueue_script(
        'sty-owner-js',
        WF_OWNER_DASHBOARD_URL . 'assets/js/owner-dashboard-theme.js',
        ['jquery', 'sweetalert2', 'select2-js'],
        time(),
        true
    );

    wp_localize_script('sty-owner-js', 'ajax_object', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('styliiiish_nonce'),
        // الـ mode هيتحدد من data-mode فى الـ HTML (user)
    ]);
});


add_filter( 'user_has_cap', function ( $caps, $cap, $args, $user ) {

    if ( in_array( 'customer', $user->roles, true ) ) {

        // Allow uploads
        $caps['upload_files'] = true;

        // Force allow async-upload from frontend
        $caps['edit_posts'] = true;

        // Prevent access to wp-admin even with edit_posts
        if ( is_admin() && ! wp_doing_ajax() ) {
            $caps['edit_posts'] = false;
        }
    }

    return $caps;

}, 10, 4 );


add_filter( 'upload_mimes', function( $mimes ) {
    $mimes['jpg']  = 'image/jpeg';
    $mimes['jpeg'] = 'image/jpeg';
    $mimes['png']  = 'image/png';
    $mimes['webp'] = 'image/webp';
    return $mimes;
});


add_filter( 'wp_handle_upload_prefilter', function( $file ) {

    if ( ! is_user_logged_in() ) {
        $file['error'] = 'Not logged in.';
        return $file;
    }

    return $file;
});


add_action('wp_enqueue_scripts', function () {
    if (is_page('owner-dashboard') || is_page('my-dresses')) {
        wp_enqueue_script(
            'lottie-player',
            'https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js',
            array(),
            null,
            true
        );
    }
});




add_action('init', function () {
    $role = get_role('customer');
    if ($role && !$role->has_cap('upload_files')) {
        $role->add_cap('upload_files');
    }
});











add_filter('body_class', 'styliiiish_add_user_dashboard_body_class');
function styliiiish_add_user_dashboard_body_class($classes) {

    // لو مش في صفحة الـ owner dashboard → خروج
    if (!is_page('owner-dashboard')) {
        return $classes;
    }

    $user_id = get_current_user_id();
    $is_manager = current_user_can('manage_woocommerce');
    
    // لو مش مدير → يبقى User Mode
    if (!$is_manager) {
        $classes[] = 'styliiiish-user-mode';
    }

    return $classes;
}








add_action('wp_enqueue_scripts', 'styliiiish_myaccount_styles');
function styliiiish_myaccount_styles() {

    // نسحب ملف CSS من داخل البلجن
    wp_enqueue_style(
        'styliiiish-myaccount-css',
        plugin_dir_url(__FILE__) . 'assets/css/myaccount-style.css',
        array(),
        time()
    );
}










add_action( 'woocommerce_account_dashboard', 'styliiiish_myaccount_welcome_box' );
function styliiiish_myaccount_welcome_box() {

    $site_url = site_url();
    $user     = wp_get_current_user();

    // هل هو مدير؟
    $is_manager = current_user_can('manage_woocommerce') || current_user_can('administrator');

    echo '<div class="styliiiish-welcome-box">';




    // محتوى المدير
    if ($is_manager) {

        echo '
            <h2 class="sty-title">Welcome back, Boss! 🔧✨</h2>
            <p class="sty-text">
                Here’s your quick access to manage Styliiiish smoothly and keep everything running perfectly.
            </p>

            <a href="' . $site_url . '/owner-dashboard/" class="sty-btn-admin-full">
                🔧 Manage Your Website
            </a>

        ';

    } 
    
    else {

        // محتوى اليوزر
        echo '
            <h2 class="sty-title">Hey lovely! 💖</h2>
            <p class="sty-text">
                Ready to earn money from your beautiful dress?  
                You can list it now in just a few smooth steps. We’re here to make it easy for you! ✨
            </p>

            <a href="' . $site_url . '/my-dresses/" class="sty-btn-user-full">
                👗 Sell Your Dress Now
            </a>

        ';
    }

    echo '</div>';
}




















add_action('wp_ajax_styliiiish_update_status', function () {

    $pid    = intval($_POST['product_id']);
    $status = sanitize_text_field($_POST['status']);

    if (!$pid || !$status) {
        wp_send_json_error(['message' => 'Invalid parameters']);
    }

    wp_update_post([
        'ID'          => $pid,
        'post_status' => $status
    ]);

    wp_send_json_success(['message' => 'Status updated']);
});













add_action('wp_print_scripts', function() {

    if ( is_page('owner-dashboard') || is_page('manage-products') || is_page('manage-customer-products') ) {

        $handles = [
            'theme', 'custom', 'ekart-custom', 'ekart-theme',
            'shopire-theme', 'shopire-custom', // بعض نسخ ekart بتستخدم دول
        ];

        foreach ($handles as $h) {
            wp_dequeue_script($h);
            wp_deregister_script($h);
        }
    }
}, 200);














/**
 * Disable Ekart JS files inside Owner Dashboard pages
 */
add_action('wp_enqueue_scripts', function() {

    if ( is_page('owner-dashboard') || is_page('manage-products') || is_page('manage-customer-products') ) {

        // dequeue ekart main theme js
        wp_dequeue_script('shopire/assets/js/custom-js');
        wp_dequeue_script('shopire/assets/js/theme-js');

        // force remove if registered with different handles
        wp_deregister_script('shopire/assets/js/custom-js');
        wp_deregister_script('shopire/assets/js/theme-js');
    }
}, 999);
