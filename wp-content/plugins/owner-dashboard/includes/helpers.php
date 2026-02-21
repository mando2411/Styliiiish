<?php
if ( ! defined('ABSPATH') ) {
    exit;
}

/**
 * Helper: جلب قيمة option مع default
 */
function wf_od_get_option($key, $default = '') {
    $val = get_option($key, null);
    if ($val === null || $val === '') {
        return $default;
    }
    return $val;
}

/**
 * Helper: جلب لستة user IDs وتحويلهم لمستخدمين
 */
function wf_od_get_users_from_ids($ids = array()) {
    $users = array();
    if (!is_array($ids)) {
        $ids = array();
    }

    foreach ($ids as $uid) {
        $u = get_user_by('ID', intval($uid));
        if ($u) {
            $users[] = $u;
        }
    }
    return $users;
}


/**
 * Determine user type based on settings
 *
 * Returns:
 * - "manager"
 * - "dashboard"
 * - "marketplace"
 */
function wf_od_get_user_type( $user_id = 0 ) {

    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    if (!$user_id) {
        return 'marketplace';
    }

    // Lists from settings
    // ⚠️ هنا بنعتمد على الدوال اللى فى settings-handler.php
    if ( ! function_exists('wf_od_get_manager_ids') || ! function_exists('wf_od_get_dashboard_ids') ) {
        return 'marketplace';
    }

    $manager_ids   = wf_od_get_manager_ids();
    $dashboard_ids = wf_od_get_dashboard_ids();

    // 1) Manager
    if ( in_array($user_id, $manager_ids, true) ) {
        return 'manager';
    }

    // 2) Dashboard user (not manager)
    if ( in_array($user_id, $dashboard_ids, true) ) {
        return 'dashboard';
    }

    // 3) Default → Marketplace user
    return 'marketplace';
}
