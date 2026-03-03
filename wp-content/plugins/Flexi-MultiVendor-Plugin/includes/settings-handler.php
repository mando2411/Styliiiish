<?php
if ( ! defined('ABSPATH') ) {
    exit;
}

/**
 * Option keys المستخدمة في السيستم
 */
function wf_od_option_key_marketplace_enabled() {
    return 'sty_mp_enable_marketplace';
}

function wf_od_option_key_add_product_mode() {
    return 'styliiiish_add_product_mode';
}

function wf_od_option_key_manager_ids() {
    return 'styliiiish_allowed_manager_ids';
}

function wf_od_option_key_dashboard_ids() {
    return 'styliiiish_dashboard_access_ids';
}

/**
 * هل الـ Marketplace مفعّل؟
 */
function websiteflexi_is_marketplace_enabled() {
    return (bool) get_option(wf_od_option_key_marketplace_enabled(), 0);
}

/**
 * وضع إضافة المنتج (ajax / old)
 */
function wf_od_get_add_product_mode() {
    return wf_od_get_option(wf_od_option_key_add_product_mode(), 'ajax');
}

/**
 * IDs للمديرين
 */
function wf_od_get_manager_ids() {
    $ids = get_option(wf_od_option_key_manager_ids(), array());
    return is_array($ids) ? $ids : array();
}

/**
 * IDs لمستخدمى الداشبورد
 */
function wf_od_get_dashboard_ids() {
    $ids = get_option(wf_od_option_key_dashboard_ids(), array());
    return is_array($ids) ? $ids : array();
}
