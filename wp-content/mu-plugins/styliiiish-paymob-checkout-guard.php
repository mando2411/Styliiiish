<?php
/**
 * Plugin Name: Styliiiish Paymob Checkout Guard
 * Description: Non-invasive checkout guard for Paymob Pixel loading deadlocks.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_enqueue_scripts', function () {
    if (is_admin() || !function_exists('is_checkout') || !is_checkout()) {
        return;
    }

    $pixel_settings = get_option('woocommerce_paymob-pixel_settings', []);
    if (!is_array($pixel_settings) || (($pixel_settings['enabled'] ?? 'no') !== 'yes')) {
        return;
    }
    $before_inline = <<<'JS'
(function (window) {
    if (window.__styliiiishPaymobGuardBeforeLoaded) {
        return;
    }
    window.__styliiiishPaymobGuardBeforeLoaded = true;

    const nativeSetInterval = window.setInterval;
    window.setInterval = function (callback, delay) {
        try {
            const callbackText = typeof callback === 'function'
                ? Function.prototype.toString.call(callback)
                : String(callback || '');

            const stack = String((new Error()).stack || '');
            const fromPaymobBlock = stack.indexOf('paymob-pixel_block.js') !== -1;
            const aggressivePolling = Number(delay) > 0
                && Number(delay) <= 250
                && (
                    callbackText.indexOf('previousTotalBlock') !== -1
                    || callbackText.indexOf('wc/store/cart') !== -1
                    || callbackText.indexOf('cartTotals') !== -1
                    || callbackText.indexOf('updateCheckoutData') !== -1
                );

            if (fromPaymobBlock && aggressivePolling) {
                console.warn('[Styliiiish Paymob Guard] blocked aggressive interval', delay);
                return 0;
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
        wp_register_script('styliiiish-paymob-checkout-guard-before', false, [], '1.1.0', false);
        wp_enqueue_script('styliiiish-paymob-checkout-guard-before');
        wp_add_inline_script('styliiiish-paymob-checkout-guard-before', $before_inline, 'after');
    }

    wp_register_script('styliiiish-paymob-checkout-guard', false, ['jquery'], '1.1.0', true);
    wp_enqueue_script('styliiiish-paymob-checkout-guard');

    $after_inline = <<<'JS'
(function (window, document, $) {
    if (!$ || typeof $.ajax !== 'function') {
        return;
    }

    if (window.__styliiiishPaymobGuardAfterLoaded) {
        return;
    }
    window.__styliiiishPaymobGuardAfterLoaded = true;

    const LOADER_ID = 'paymob-loading-indicator';
    const NOTICE_ID = 'styliiiish-paymob-guard-notice';
    const LOADER_TIMEOUT_MS = 20000;
    const MIN_UPDATE_INTERVAL_MS = 1200;
    let lastUpdateCall = 0;
    let loaderTimer = null;

    function hasWidget() {
        const root = document.getElementById('paymob-elements');
        return !!(root && root.children && root.children.length > 0);
    }

    function removeLoader() {
        document.querySelectorAll('#' + LOADER_ID).forEach((node) => node.remove());
    }

    function clearLoaderTimer() {
        if (loaderTimer) {
            clearTimeout(loaderTimer);
            loaderTimer = null;
        }
    }

    function armLoaderTimeout() {
        clearLoaderTimer();
        loaderTimer = setTimeout(function () {
            if (!hasWidget()) {
                removeLoader();
                showNotice('Unable to load payment form. Please refresh and try again.');
            }
        }, LOADER_TIMEOUT_MS);
    }

    function showNotice(message) {
        const text = String(message || 'Checkout error, please retry.');
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

        if (!options.timeout || options.timeout < LOADER_TIMEOUT_MS) {
            options.timeout = LOADER_TIMEOUT_MS;
        }

        if (isPixelUpdate) {
            const now = Date.now();
            if ((now - lastUpdateCall) < MIN_UPDATE_INTERVAL_MS) {
                options.global = false;
            }
            lastUpdateCall = now;
        }
    });

    $(document).ajaxSend(function (_event, _jqXHR, settings) {
        const url = String(settings?.url || '');
        const data = String(settings?.data || '');
        if (url.indexOf('admin-ajax.php') !== -1 && data.indexOf('action=update_pixel_data') !== -1) {
            armLoaderTimeout();
        }
    });

    $(document).ajaxError(function (_event, jqXHR, settings, thrownError) {
        const url = String(settings?.url || '');
        const data = String(settings?.data || '');
        if (url.indexOf('admin-ajax.php') === -1 || data.indexOf('action=update_pixel_data') === -1) {
            return;
        }
        removeLoader();
        clearLoaderTimer();
        showNotice(jqXHR?.responseJSON?.data || thrownError || 'Payment service failed. Please retry.');
    });

    $(document).ajaxComplete(function (_event, _jqXHR, settings) {
        const url = String(settings?.url || '');
        const data = String(settings?.data || '');
        if (url.indexOf('admin-ajax.php') === -1 || data.indexOf('action=update_pixel_data') === -1) {
            return;
        }
        if (hasWidget()) {
            removeLoader();
            clearLoaderTimer();
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
                if (!forceReload && (now - lastUpdateCall) < MIN_UPDATE_INTERVAL_MS) {
                    return;
                }
                lastUpdateCall = now;
                return originalUpdateCheckoutData.apply(this, arguments);
            };
            window.__styliiiishWrappedUpdateCheckoutData = true;
        }

        if (hasAjaxCall && !window.__styliiiishWrappedAjaxCall) {
            const originalAjaxCall = window.ajaxCall;
            window.ajaxCall = function () {
                const forceReload = !!arguments[2];
                const now = Date.now();
                if (!forceReload && (now - lastUpdateCall) < MIN_UPDATE_INTERVAL_MS) {
                    return;
                }
                lastUpdateCall = now;
                return originalAjaxCall.apply(this, arguments);
            };
            window.__styliiiishWrappedAjaxCall = true;
        }

        if ((window.__styliiiishWrappedUpdateCheckoutData || !hasUpdateCheckoutData)
            && (window.__styliiiishWrappedAjaxCall || !hasAjaxCall)) {
            window.clearInterval(patchInterval);
        }
    }, 250);

    window.setTimeout(function () {
        window.clearInterval(patchInterval);
    }, 15000);
})(window, document, window.jQuery);
JS;

    wp_add_inline_script('styliiiish-paymob-checkout-guard', $after_inline, 'after');
}, 999);
