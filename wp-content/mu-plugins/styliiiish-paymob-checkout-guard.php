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
        window.eval('if (typeof previousTotalBlock === "undefined") { var previousTotalBlock = null; }');
    } catch (error) {
    }

    // Block/slow Paymob polling intervals to prevent UI freeze.
    const nativeSetInterval = window.setInterval;
    window.setInterval = function (callback, delay) {
        try {
            const ms = Number(delay);
            const cbText = typeof callback === 'function'
                ? Function.prototype.toString.call(callback)
                : String(callback || '');

            const stack = String((new Error()).stack || '');
            const fromPaymob = stack.indexOf('paymob-pixel_block.js') !== -1;
            const isAggressivePaymobPoll = fromPaymob
                && ms > 0
                && ms <= 1000
                && (
                    cbText.indexOf('wc/store/cart') !== -1
                    || cbText.indexOf('cartTotals') !== -1
                    || cbText.indexOf('previousTotalBlock') !== -1
                    || cbText.indexOf('updateCheckoutData') !== -1
                    || cbText.indexOf('billingAddress') !== -1
                    || cbText.indexOf('ajaxCall(') !== -1
                );

            if (isAggressivePaymobPoll) {
                return nativeSetInterval.call(this, callback, 5000);
            }

            if (fromPaymob && !Number.isNaN(ms) && ms > 0 && ms <= 1000) {
                return nativeSetInterval.call(this, callback, 2500);
            }

            if (!Number.isNaN(ms) && ms > 0 && ms <= 250) {
                return nativeSetInterval.call(this, callback, 1200);
            }
        } catch (error) {
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
    const MIN_UPDATE_INTERVAL_MS = 4000;
    const DUPLICATE_WINDOW_MS = 1800;
    let lastUpdateCallAt = 0;
    let lastUpdateSignature = null;
    let lastUpdateSignatureAt = 0;
    let loaderTimer = null;
    let activeUpdateXhr = null;

    function isPaymobSelected() {
        const block = document.querySelector('input[name="radio-control-wc-payment-method-options"][value="paymob-pixel"]');
        const classic = document.getElementById('payment_method_paymob-pixel');
        return !!((block && block.checked) || (classic && classic.checked));
    }

    function getAjaxAction(rawData) {
        if (!rawData) {
            return '';
        }

        if (typeof rawData === 'object' && rawData !== null) {
            if (typeof rawData.action === 'string') {
                return rawData.action;
            }
            if (rawData instanceof FormData) {
                const action = rawData.get('action');
                return typeof action === 'string' ? action : '';
            }
            return '';
        }

        const text = String(rawData);
        const match = text.match(/(?:^|&)action=([^&]+)/);
        return match ? decodeURIComponent(match[1].replace(/\+/g, ' ')) : '';
    }

    function getPayloadSignature(rawData) {
        if (!rawData) {
            return '';
        }
        if (typeof rawData === 'string') {
            return rawData;
        }
        if (rawData instanceof FormData) {
            try {
                return JSON.stringify(Array.from(rawData.entries()));
            } catch (error) {
                return '';
            }
        }
        if (typeof rawData === 'object') {
            try {
                return JSON.stringify(rawData);
            } catch (error) {
                return '';
            }
        }
        return '';
    }

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
        const rawData = options?.data ?? originalOptions?.data ?? '';
        const action = getAjaxAction(rawData);
        const isPixelUpdate = url.indexOf('admin-ajax.php') !== -1 && action === 'update_pixel_data';
        const isOrderSession = url.indexOf('admin-ajax.php') !== -1 && action === 'get_order_id_from_session';
        const isPaymobCallback = url.indexOf('wc-api=paymob_callback') !== -1;

        if (!(isPixelUpdate || isOrderSession || isPaymobCallback)) {
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
            const signature = getPayloadSignature(rawData);

            if (signature && signature === lastUpdateSignature && (now - lastUpdateSignatureAt) < DUPLICATE_WINDOW_MS) {
                options.beforeSend = function () {
                    return false;
                };
                return;
            }

            if (activeUpdateXhr && activeUpdateXhr.readyState !== 4) {
                try {
                    activeUpdateXhr.abort('styliiiish_prevent_overlap');
                } catch (error) {
                }
            }

            if ((now - lastUpdateCallAt) < MIN_UPDATE_INTERVAL_MS) {
                options.beforeSend = function () {
                    return false;
                };
                return;
            }
            lastUpdateCallAt = now;
            lastUpdateSignature = signature;
            lastUpdateSignatureAt = now;
            armLoaderTimeout();
        }
    });

    $(document).ajaxSend(function (_event, jqXHR, settings) {
        const url = String(settings?.url || '');
        const action = getAjaxAction(settings?.data || '');
        const isPixelUpdate = url.indexOf('admin-ajax.php') !== -1 && action === 'update_pixel_data';
        if (!isPixelUpdate) {
            return;
        }

        activeUpdateXhr = jqXHR;
    });

    $(document).ajaxError(function (_event, jqXHR, settings, thrownError) {
        const url = String(settings?.url || '');
        const action = getAjaxAction(settings?.data || '');
        if (url.indexOf('admin-ajax.php') === -1 || action !== 'update_pixel_data') {
            return;
        }
        clearLoader();
        showNotice(jqXHR?.responseJSON?.data || thrownError || 'Payment service failed. Please retry.');
    });

    $(document).ajaxComplete(function (_event, _jqXHR, settings) {
        const url = String(settings?.url || '');
        const action = getAjaxAction(settings?.data || '');
        if (url.indexOf('admin-ajax.php') === -1 || action !== 'update_pixel_data') {
            return;
        }

        if (loaderTimer) {
            clearTimeout(loaderTimer);
            loaderTimer = null;
        }

        if (hasPaymobWidget()) {
            clearLoader();
        }

        if (activeUpdateXhr === _jqXHR) {
            activeUpdateXhr = null;
        }
    });

    const patchInterval = window.setInterval(function () {
        const hasUpdateCheckoutData = typeof window.updateCheckoutData === 'function';
        const hasAjaxCall = typeof window.ajaxCall === 'function';

        if (hasUpdateCheckoutData && !window.__styliiiishWrappedUpdateCheckoutData) {
            const originalUpdateCheckoutData = window.updateCheckoutData;
            window.updateCheckoutData = function () {
                const now = Date.now();
                if ((now - lastUpdateCallAt) < MIN_UPDATE_INTERVAL_MS) {
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
                const now = Date.now();
                if (!isPaymobSelected()) {
                    return;
                }
                if ((now - lastUpdateCallAt) < MIN_UPDATE_INTERVAL_MS) {
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
