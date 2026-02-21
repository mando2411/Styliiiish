<?php
if ( ! defined('ABSPATH') ) {
    exit;
}

/**
 * إضافة صفحة WebsiteFlexi System Settings تحت Plugins
 */
add_action('admin_menu', function () {

    add_submenu_page(
        'plugins.php',
        'WebsiteFlexi Owner Dashboard & Marketplace',
        'WebsiteFlexi System Settings',
        'manage_options',
        'websiteflexi-system-settings',
        'websiteflexi_render_system_settings_page'
    );

});

/**
 * معالجة الـ POST + عرض صفحة الإعدادات
 */
function websiteflexi_render_system_settings_page() {

    if ( ! current_user_can('manage_options') ) {
        wp_die('You do not have permission to access this page.');
    }

    // =========================
    // معالجة الفورم: Marketplace
    // =========================
    if ( isset($_POST['wf_save_marketplace_settings']) && check_admin_referer('wf_save_marketplace_settings') ) {

        $enabled = isset($_POST['sty_mp_enable_marketplace']) ? 1 : 0;
        update_option(wf_od_option_key_marketplace_enabled(), $enabled);

        wp_redirect( add_query_arg(array(
            'page' => 'websiteflexi-system-settings',
            'wf_msg' => 'marketplace_saved',
            'tab' => 'marketplace',
        ), admin_url('plugins.php')) );
        exit;
    }

    // =========================
    // معالجة الفورم: Add Product Mode
    // =========================
    if ( isset($_POST['styliiiish_save_add_product_mode_btn'])
        && check_admin_referer('styliiiish_save_add_product_mode') ) {

        if ( isset($_POST['styliiiish_add_product_mode']) ) {
            update_option(
                wf_od_option_key_add_product_mode(),
                sanitize_text_field($_POST['styliiiish_add_product_mode'])
            );
        }

        wp_redirect( add_query_arg(array(
            'page'   => 'websiteflexi-system-settings',
            'wf_msg' => 'add_mode_saved',
            'tab'    => 'add_product',
        ), admin_url('plugins.php')) );
        exit;
    }

    // =========================
    // معالجة الفورم: إضافة Manager
    // =========================
    if ( isset($_POST['styliiiish_add_manager']) && check_admin_referer('styliiiish_add_manager_action') ) {

        $email    = strtolower(trim(sanitize_email($_POST['manager_email'])));
        $password = sanitize_text_field($_POST['manager_password']);

        if ( empty($email) ) {
            wp_die("Email is required.");
        }

        $allowed_ids = wf_od_get_manager_ids();
        $user = get_user_by('email', $email);

        if ( ! $user ) {
            if ( empty($password) ) {
                $password = wp_generate_password(12);
            }

            $user_id = wp_create_user($email, $password, $email);

            if ( is_wp_error($user_id) ) {
                wp_die("Error creating user: " . $user_id->get_error_message());
            }

            wp_update_user(array(
                'ID'   => $user_id,
                'role' => 'shop_manager'
            ));

            $user = get_user_by('ID', $user_id);

            wp_new_user_notification($user_id, null, 'user');
        }

        if ( ! in_array($user->ID, $allowed_ids, true) ) {
            $allowed_ids[] = $user->ID;
            update_option(wf_od_option_key_manager_ids(), array_unique($allowed_ids));
        }

        wp_redirect( add_query_arg(array(
            'page'   => 'websiteflexi-system-settings',
            'wf_msg' => 'manager_added',
            'tab'    => 'managers',
        ), admin_url('plugins.php')) );
        exit;
    }

    // =========================
    // إزالة Manager
    // =========================
    if ( isset($_GET['wf_remove_manager']) ) {

        $remove_id   = intval($_GET['wf_remove_manager']);
        $allowed_ids = wf_od_get_manager_ids();

        $allowed_ids = array_diff($allowed_ids, array($remove_id));
        update_option(wf_od_option_key_manager_ids(), $allowed_ids);

        wp_redirect( add_query_arg(array(
            'page'   => 'websiteflexi-system-settings',
            'wf_msg' => 'manager_removed',
            'tab'    => 'managers',
        ), admin_url('plugins.php')) );
        exit;
    }

    // =========================
    // إضافة Dashboard User
    // =========================
    if ( isset($_POST['styliiiish_add_dashboard_user']) && check_admin_referer('styliiiish_add_dashboard_action') ) {

        $email    = strtolower(trim(sanitize_email($_POST['dashboard_email'])));
        $password = sanitize_text_field($_POST['dashboard_password']);

        if ( empty($email) ) {
            wp_die("Email is required.");
        }

        $access_ids = wf_od_get_dashboard_ids();
        $user = get_user_by('email', $email);

        if ( ! $user ) {
            if ( empty($password) ) {
                $password = wp_generate_password(12);
            }

            $user_id = wp_create_user($email, $password, $email);

            if ( is_wp_error($user_id) ) {
                wp_die("Error creating user: " . $user_id->get_error_message());
            }

            wp_update_user(array(
                'ID'   => $user_id,
                'role' => 'shop_manager'
            ));

            $user = get_user_by('ID', $user_id);

            wp_new_user_notification($user_id, null, 'user');
        }

        if ( ! in_array($user->ID, $access_ids, true) ) {
            $access_ids[] = $user->ID;
            update_option(wf_od_option_key_dashboard_ids(), array_unique($access_ids));
        }

        wp_redirect( add_query_arg(array(
            'page'   => 'websiteflexi-system-settings',
            'wf_msg' => 'dashboard_user_added',
            'tab'    => 'dashboard_access',
        ), admin_url('plugins.php')) );
        exit;
    }

    // =========================
    // إزالة Dashboard User
    // =========================
    if ( isset($_GET['wf_remove_dashboard_user']) ) {

        $remove_id  = intval($_GET['wf_remove_dashboard_user']);
        $access_ids = wf_od_get_dashboard_ids();

        $access_ids = array_diff($access_ids, array($remove_id));
        update_option(wf_od_option_key_dashboard_ids(), $access_ids);

        wp_redirect( add_query_arg(array(
            'page'   => 'websiteflexi-system-settings',
            'wf_msg' => 'dashboard_user_removed',
            'tab'    => 'dashboard_access',
        ), admin_url('plugins.php')) );
        exit;
    }

    // =========================
    // إعداد البيانات للعرض
    // =========================

    $active_tab           = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'marketplace';
    $marketplace_enabled  = websiteflexi_is_marketplace_enabled();
    $add_product_mode     = wf_od_get_add_product_mode();

    $manager_ids    = wf_od_get_manager_ids();
    $dashboard_ids  = wf_od_get_dashboard_ids();

    $manager_users   = wf_od_get_users_from_ids($manager_ids);
    $dashboard_users = wf_od_get_users_from_ids($dashboard_ids);

    $message_key = isset($_GET['wf_msg']) ? sanitize_key($_GET['wf_msg']) : '';

    // تمرير المتغيرات لملف الـ View
    include WF_OWNER_DASHBOARD_PATH . 'admin/system-settings-view.php';
}
