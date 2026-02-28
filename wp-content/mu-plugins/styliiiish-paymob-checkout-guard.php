<?php
/**
 * Plugin Name: Styliiiish Paymob Checkout Guard
 * Description: Targeted Paymob Pixel freeze guard (without editing payment plugin files).
 */

if (!defined('ABSPATH')) {
    exit;
}

function styliiiish_paymob_guard_is_checkout_context(): bool {
    if (is_admin() || !function_exists('is_checkout') || !is_checkout()) {
        return false;
    }

    if (function_exists('is_order_received_page') && is_order_received_page()) {
        return false;
    }

    return true;
}

function styliiiish_paymob_guard_is_enabled(): bool {
    $pixel_settings = get_option('woocommerce_paymob-pixel_settings', []);
    return is_array($pixel_settings) && (($pixel_settings['enabled'] ?? 'no') === 'yes');
}

add_action('wp_enqueue_scripts', function () {
    if (!styliiiish_paymob_guard_is_checkout_context() || !styliiiish_paymob_guard_is_enabled()) {
        return;
    }

    $before_inline = <<<'JS'
(function (window) {
    if (window.__styliiiishPaymobBeforeGuardApplied) {
        return;
    }
    window.__styliiiishPaymobBeforeGuardApplied = true;

    // Fix missing global used by paymob-pixel_block.js logs/comparisons.
    if (typeof window.previousTotalBlock === 'undefined') {
        window.previousTotalBlock = null;
    }
    try {
        window.eval('var previousTotalBlock = window.previousTotalBlock;');
    } catch (error) {
    }

    // Slow down only very aggressive short polling intervals to prevent UI freeze.
    const nativeSetInterval = window.setInterval;
    window.setInterval = function (callback, delay) {
        const ms = Number(delay);
        if (!Number.isNaN(ms) && ms > 0 && ms <= 250) {
            return nativeSetInterval.call(this, callback, 1200);
        }
        return nativeSetInterval.apply(this, arguments);
    };
})(window);
JS;

    if (wp_script_is('paymob-pixel-checkout', 'enqueued')) {
        wp_add_inline_script('paymob-pixel-checkout', $before_inline, 'before');
    } else {
        wp_register_script('styliiiish-paymob-checkout-guard-before', false, [], '2.1.0', false);
        wp_enqueue_script('styliiiish-paymob-checkout-guard-before');
        wp_add_inline_script('styliiiish-paymob-checkout-guard-before', $before_inline, 'after');
    }

    wp_register_script('styliiiish-paymob-checkout-guard', false, ['jquery'], '2.0.0', true);
    wp_enqueue_script('styliiiish-paymob-checkout-guard');

    $inline_js = <<<'JS'
(function (window, document, $) {
    if (!$ || typeof $.ajax !== 'function') {
        return;
    }

    if (window.__styliiiishCheckoutIsolatedGuard) {
        return;
    }
    window.__styliiiishCheckoutIsolatedGuard = true;

    const LOADER_ID = 'paymob-loading-indicator';
    const NOTICE_ID = 'styliiiish-paymob-guard-notice';
    const AJAX_TIMEOUT_MS = 20000;
    const MIN_UPDATE_INTERVAL_MS = 1500;
    let lastUpdateCallAt = 0;
    let loaderTimer = null;

    function hasPaymobWidget() {
        const root = document.getElementById('paymob-elements');
        return !!(root && root.children && root.children.length > 0);
    }

    function clearLoader() {
        document.querySelectorAll('#' + LOADER_ID).forEach((node) => node.remove());
    }

    function showNotice(message) {
        const text = String(message || 'Checkout error. Please refresh and try again.');
        const existing = document.getElementById(NOTICE_ID);
        if (existing) {
            existing.textContent = text;
            return;
        }

        const host = document.querySelector('.woocommerce-notices-wrapper')
            || document.querySelector('.wc-block-components-notices')
            || document.querySelector('form.checkout')
            || document.querySelector('.wc-block-checkout__actions');

        if (!host) {
            return;
        }

        const node = document.createElement('div');
        node.id = NOTICE_ID;
        node.className = 'woocommerce-error';
        node.setAttribute('role', 'alert');
        node.style.margin = '12px 0';
        node.textContent = text;
        host.prepend(node);
    }

    function armLoaderTimeout() {
        if (loaderTimer) {
            clearTimeout(loaderTimer);
        }
        loaderTimer = setTimeout(function () {
            if (!hasPaymobWidget()) {
                clearLoader();
                showNotice('Paymob checkout is taking too long. Please refresh and try again.');
            }
        }, AJAX_TIMEOUT_MS);
    }

    $.ajaxPrefilter(function (options, originalOptions) {
        const url = String(options?.url || originalOptions?.url || '');
        const data = String(options?.data || originalOptions?.data || '');
        const isPixelUpdate = url.indexOf('admin-ajax.php') !== -1 && data.indexOf('action=update_pixel_data') !== -1;
        const isOrderSession = url.indexOf('admin-ajax.php') !== -1 && data.indexOf('action=get_order_id_from_session') !== -1;

        if (!(isPixelUpdate || isOrderSession)) {
            return;
        }

        if (options.async === false) {
            options.async = true;
        }

        if (!options.timeout || options.timeout < AJAX_TIMEOUT_MS) {
            options.timeout = AJAX_TIMEOUT_MS;
        }

        if (isPixelUpdate) {
            const now = Date.now();
            if ((now - lastUpdateCallAt) < MIN_UPDATE_INTERVAL_MS) {
                options.global = false;
            }
            lastUpdateCallAt = now;
            armLoaderTimeout();
        }
    });

    $(document).ajaxError(function (_event, jqXHR, settings, thrownError) {
        const url = String(settings?.url || '');
        const data = String(settings?.data || '');
        if (url.indexOf('admin-ajax.php') === -1 || data.indexOf('action=update_pixel_data') === -1) {
            return;
        }
        clearLoader();
        showNotice(jqXHR?.responseJSON?.data || thrownError || 'Payment service failed. Please retry.');
    });

    $(document).ajaxComplete(function (_event, _jqXHR, settings) {
        const url = String(settings?.url || '');
        const data = String(settings?.data || '');
        if (url.indexOf('admin-ajax.php') === -1 || data.indexOf('action=update_pixel_data') === -1) {
            return;
        }

        if (loaderTimer) {
            clearTimeout(loaderTimer);
            loaderTimer = null;
        }

        if (hasPaymobWidget()) {
            clearLoader();
        }
    });

    const patchInterval = window.setInterval(function () {
        const hasUpdateCheckoutData = typeof window.updateCheckoutData === 'function';
        const hasAjaxCall = typeof window.ajaxCall === 'function';

        if (hasUpdateCheckoutData && !window.__styliiiishWrappedUpdateCheckoutData) {
            const originalUpdateCheckoutData = window.updateCheckoutData;
            window.updateCheckoutData = function () {
                const forceReload = !!arguments[0];
                const now = Date.now();
                if (!forceReload && (now - lastUpdateCallAt) < MIN_UPDATE_INTERVAL_MS) {
                    return;
                }
                lastUpdateCallAt = now;
                return originalUpdateCheckoutData.apply(this, arguments);
            };
            window.__styliiiishWrappedUpdateCheckoutData = true;
        }

        if (hasAjaxCall && !window.__styliiiishWrappedAjaxCall) {
            const originalAjaxCall = window.ajaxCall;
            window.ajaxCall = function () {
                const forceReload = !!arguments[2];
                const now = Date.now();
                if (!forceReload && (now - lastUpdateCallAt) < MIN_UPDATE_INTERVAL_MS) {
                    return;
                }
                lastUpdateCallAt = now;
                return originalAjaxCall.apply(this, arguments);
            };
            window.__styliiiishWrappedAjaxCall = true;
        }

        if ((window.__styliiiishWrappedUpdateCheckoutData || !hasUpdateCheckoutData)
            && (window.__styliiiishWrappedAjaxCall || !hasAjaxCall)) {
            window.clearInterval(patchInterval);
        }
    }, 300);

    window.setTimeout(function () {
        window.clearInterval(patchInterval);
    }, 15000);
})(window, document, window.jQuery);
JS;

    wp_add_inline_script('styliiiish-paymob-checkout-guard', $inline_js, 'after');
}, 99999);
