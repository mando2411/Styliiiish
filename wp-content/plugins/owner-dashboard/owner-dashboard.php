<?php
/*
Plugin Name: WebsiteFlexi Owner Dashboard & Marketplace
Description: WebsiteFlexi Dashboard & Marketplace — Owner control, managers, dashboard access and customer dress marketplace.
Version: 3.4
Author: WebsiteFlexi
*/











if (!defined('ABSPATH')) {
    exit;
}



















/**
 * Constants
 */
define('WF_OWNER_DASHBOARD_PATH', plugin_dir_path(__FILE__));
define('WF_OWNER_DASHBOARD_URL', plugin_dir_url(__FILE__));


/**
 * Load includes
 */
require_once WF_OWNER_DASHBOARD_PATH . 'includes/helpers.php';
require_once WF_OWNER_DASHBOARD_PATH . 'includes/settings-handler.php';

/**
 * Admin Settings
 */
if (is_admin()) {
    require_once WF_OWNER_DASHBOARD_PATH . 'admin/system-settings.php';
}

/**
 * Frontend functional files
 */
require_once WF_OWNER_DASHBOARD_PATH . 'vendor-products.php';
require_once WF_OWNER_DASHBOARD_PATH . 'orders.php';
require_once WF_OWNER_DASHBOARD_PATH . 'stats.php';
require_once WF_OWNER_DASHBOARD_PATH . 'email.php';
require_once WF_OWNER_DASHBOARD_PATH . 'functions.php';

require_once WF_OWNER_DASHBOARD_PATH . 'modules/shared/ajax/manage-products-ajax.php';
require_once WF_OWNER_DASHBOARD_PATH . 'modules/shared/helpers-images.php';
require_once WF_OWNER_DASHBOARD_PATH . 'modules/shared/helpers-attributes.php';
require_once WF_OWNER_DASHBOARD_PATH . 'modules/manage-products/manage-products.php';


/* ===========================================
   Load Assets ONLY on owner-dashboard page
=========================================== */
add_action('wp_enqueue_scripts', function () {

    if (!is_page('owner-dashboard')) return;
    
    


    // CSS
    wp_enqueue_style('sty-owner-css', WF_OWNER_DASHBOARD_URL . 'assets/css/owner-style.css', [], time());
    wp_enqueue_style('sty-owner-mobile-css', WF_OWNER_DASHBOARD_URL . 'assets/css/mobile.css', ['sty-owner-css'], time());

    // Select2
    wp_enqueue_style('select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
    wp_enqueue_script('select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', ['jquery']);

    // SweetAlert
    wp_enqueue_script('sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', ['jquery']);

    // Owner Dashboard JS
    wp_enqueue_script('sty-owner-js', WF_OWNER_DASHBOARD_URL . 'assets/js/owner-dashboard-theme.js',
        ['jquery', 'sweetalert2', 'select2-js'], time(), true);

    wp_enqueue_media();

    wp_localize_script('sty-owner-js', 'ajax_object', [
        'ajax_url'    => admin_url('admin-ajax.php'),
        'nonce'       => wp_create_nonce('styliiiish_nonce'),
        'mode'        => 'owner',
        'old_add_url' => admin_url('admin-post.php?action=styliiiish_new_product'),
        'is_manager'  => current_user_can('manage_woocommerce'),
    ]);
});

/**
 * Theme CSS (Shopire + Ekart)
 */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('ekart-style', get_stylesheet_directory_uri() . '/../ekart/style.css');
});

/**
 * Add Settings link near Deactivate
 */
add_filter('plugin_action_links_' . plugin_basename(__FILE__), function ($links) {

    $settings_link = '<a href="' . admin_url('plugins.php?page=websiteflexi-system-settings') . '">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
});

/* ===========================================
   Shortcode: Owner Dashboard
=========================================== */
function styliiiish_owner_dashboard_shortcode(){
    if (!is_user_logged_in()) {
        return '<p>Please log in to access this page.</p>';
    }

    $user = wp_get_current_user();
    $user_id = $user->ID;

    $allowed_dashboard = wf_od_get_dashboard_ids();
    $is_admin = in_array('administrator', (array) $user->roles, true);
    $is_manager = (in_array($user_id, wf_od_get_manager_ids()) || $is_admin);

    if (!$is_manager && !in_array($user_id, $allowed_dashboard)) {
        return '<p>You do not have permission to access this page.</p>';
    }

    ob_start();
    ?>

    <div class="owner-dashboard-container" id="sty-page-wrapper">

        <h2 style="margin-bottom:20px;">
            🛍 Styliiiish Owner Dashboard
        </h2>

        <!-- CARDS -->
        <div class="owner-card" onclick="showSection('products')">
            <h3>🛍 Manage Products <span>→</span></h3>
        </div>

        <?php if ($is_manager): ?>
            <div class="owner-card" onclick="showSection('vendor_products')">
                <h3>👗 Customer Dresses Added <span>→</span></h3>
            </div>

            <div class="owner-card" onclick="showSection('orders')">
                <h3>📦 Orders <span>→</span></h3>
            </div>

            <div class="owner-card" onclick="showSection('stats')">
                <h3>📊 Statistics <span>→</span></h3>
            </div>

            <div class="owner-card" onclick="showSection('email')">
                <h3>✉️ Send Email <span>→</span></h3>
            </div>
        <?php endif; ?>

        <!-- SECTIONS -->
        <div id="section-products" class="owner-section" style="display:none;">
            <h3>🛍 Manage Products</h3>
            <?php styliiiish_render_manage_products($is_manager); ?>
        </div>

        <?php if ($is_manager): ?>
            <div id="section-vendor_products" class="owner-section" style="display:none;">
                <h3>👗 Customer Dresses Added</h3>
                <?php styliiiish_render_vendor_products(); ?>
            </div>

            <div id="section-orders" class="owner-section" style="display:none;">
                <h3>📦 Orders</h3>
                <?php styliiiish_render_orders(); ?>
            </div>

            <div id="section-stats" class="owner-section" style="display:none;">
                <h3>📊 Statistics</h3>
                <?php styliiiish_render_stats(); ?>
            </div>

            <div id="section-email" class="owner-section" style="display:none;">
                <h3>✉️ Send Email</h3>
                <?php styliiiish_render_email_sender(); ?>
            </div>
        <?php endif; ?>

    </div>

    <script>
        function showSection(section) {
            document.querySelectorAll('.owner-section').forEach(sec => sec.style.display = 'none');
            document.getElementById("section-" + section).style.display = 'block';
            window.scrollTo({top: 300, behavior: 'smooth'});
        }
    </script>

    <?php
    return ob_get_clean();
        }
        add_shortcode('owner_dashboard', 'styliiiish_owner_dashboard_shortcode');
        

        
        add_action('wp_ajax_styliiiish_inline_update_status', function () {
        
            $product_id = intval($_POST['product_id']);
            $status     = sanitize_text_field($_POST['status']);
        
            wp_update_post([
                'ID' => $product_id,
                'post_status' => $status
            ]);
        
            wp_die("OK");
        });
        
        
        
        
        /**
         * AJAX Inline Editing (Price / Stock)
         */
        add_action('wp_ajax_styliiiish_inline_update', function () {
        
            $product_id = intval($_POST['product_id']);
            $field      = sanitize_text_field($_POST['field']);
            $value      = sanitize_text_field($_POST['value']);
        
            if ($field === 'price') {
                update_post_meta($product_id, '_regular_price', $value);
                update_post_meta($product_id, '_price', $value);
            }
        
            if ($field === 'stock') {
                update_post_meta($product_id, '_stock', $value);
                update_post_meta($product_id, '_manage_stock', 'yes');
            }
        
            wp_die("OK");
        });







