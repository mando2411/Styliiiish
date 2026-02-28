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

    wp_register_script('styliiiish-paymob-checkout-guard', false, ['jquery'], '1.0.0', true);
    wp_enqueue_script('styliiiish-paymob-checkout-guard');

    $inline_js = <<<'JS'
(function (window, document, $) {
    if (!$ || typeof $.ajax !== 'function') {
        return;
    }

    const LOADER_ID = 'paymob-loading-indicator';
    const NOTICE_ID = 'styliiiish-paymob-guard-notice';
    const LOADER_TIMEOUT_MS = 25000;
    const PIXEL_UPDATE_MIN_INTERVAL = 1200;
    const PIXEL_AJAX_TIMEOUT_MS = 20000;
    let loaderTimer = null;
    let lastPixelUpdateAt = 0;
    let lastAjaxCallAt = 0;
    let activeUpdateXhr = null;

    function getLoaderNodes() {
        return Array.from(document.querySelectorAll('#' + LOADER_ID));
    }

    function hasRenderedPaymobWidget() {
        const root = document.getElementById('paymob-elements');
        return !!(root && root.children && root.children.length > 0);
    }

    function clearLoaderTimer() {
        if (loaderTimer) {
            clearTimeout(loaderTimer);
            loaderTimer = null;
        }
    }

    function removeLoader() {
        const nodes = getLoaderNodes();
        if (!nodes.length) {
            return;
        }
        nodes.forEach((node) => node.remove());
    }

    function showErrorNotice(message) {
        const msg = String(message || 'Payment widget timed out. Please refresh and try again.');
        const existing = document.getElementById(NOTICE_ID);
        if (existing) {
            existing.textContent = msg;
            return;
        }

        const wrappers = [
            document.querySelector('.woocommerce-notices-wrapper'),
            document.querySelector('.wc-block-components-notices'),
            document.querySelector('.wc-block-checkout__actions'),
            document.querySelector('form.checkout'),
        ];

        const wrapper = wrappers.find(Boolean);
        if (!wrapper) {
            console.warn('[Paymob Guard]', msg);
            return;
        }

        const notice = document.createElement('div');
        notice.id = NOTICE_ID;
        notice.setAttribute('role', 'alert');
        notice.className = 'woocommerce-error';
        notice.style.margin = '12px 0';
        notice.textContent = msg;

        wrapper.prepend(notice);
    }

    function armLoaderTimeout() {
        clearLoaderTimer();
        loaderTimer = setTimeout(function () {
            if (!hasRenderedPaymobWidget()) {
                removeLoader();
                showErrorNotice('Unable to load Paymob checkout form. Please refresh and try again.');
            }
        }, LOADER_TIMEOUT_MS);
    }

    const observer = new MutationObserver(function () {
        const hasLoader = getLoaderNodes().length > 0;
        if (hasLoader) {
            armLoaderTimeout();
            return;
        }
        clearLoaderTimer();
    });

    observer.observe(document.body, { childList: true, subtree: true });

    $.ajaxPrefilter(function (options, originalOptions, jqXHR) {
        const url = String(options?.url || '');
        const data = String(options?.data || originalOptions?.data || '');
        const isAdminAjax = url.includes('admin-ajax.php');
        if (!isAdminAjax) {
            return;
        }

        const isPixelUpdate = data.includes('action=update_pixel_data');
        const isOrderSessionCall = data.includes('action=get_order_id_from_session');

        if (isPixelUpdate || isOrderSessionCall) {
            if (options.async === false) {
                options.async = true;
            }

            if (!options.timeout || options.timeout < PIXEL_AJAX_TIMEOUT_MS) {
                options.timeout = PIXEL_AJAX_TIMEOUT_MS;
            }
        }

        if (isPixelUpdate) {
            const now = Date.now();
            if (activeUpdateXhr && activeUpdateXhr.readyState !== 4) {
                try {
                    activeUpdateXhr.abort('styliiiish_throttled');
                } catch (error) {
                }
            }

            activeUpdateXhr = jqXHR;
            if ((now - lastPixelUpdateAt) < PIXEL_UPDATE_MIN_INTERVAL) {
                options.global = false;
            }
            lastPixelUpdateAt = now;
        }
    });

    $(document).ajaxError(function (_event, jqXHR, settings, thrownError) {
        const url = String(settings?.url || '');
        const data = String(settings?.data || '');
        const isPixelUpdate = url.includes('admin-ajax.php') && data.includes('action=update_pixel_data');

        if (!isPixelUpdate) {
            return;
        }

        removeLoader();
        const serverMessage = jqXHR?.responseJSON?.data || jqXHR?.responseJSON?.message;
        showErrorNotice(serverMessage || thrownError || 'Checkout request failed. Please retry.');
    });

    $(document).ajaxComplete(function (_event, jqXHR, settings) {
        const url = String(settings?.url || '');
        const data = String(settings?.data || '');
        const isPixelUpdate = url.includes('admin-ajax.php') && data.includes('action=update_pixel_data');

        if (!isPixelUpdate) {
            return;
        }

        const failed = !!(jqXHR && jqXHR.status >= 400);
        if (failed) {
            removeLoader();
            showErrorNotice('Checkout service returned an error. Please try again.');
            return;
        }

        if (hasRenderedPaymobWidget()) {
            removeLoader();
            clearLoaderTimer();
        }

        if (activeUpdateXhr === jqXHR) {
            activeUpdateXhr = null;
        }
    });

    $(document).ajaxSend(function (_event, _jqXHR, settings) {
        const url = String(settings?.url || '');
        const data = String(settings?.data || '');
        const isPixelUpdate = url.includes('admin-ajax.php') && data.includes('action=update_pixel_data');
        if (!isPixelUpdate) {
            return;
        }
        armLoaderTimeout();
    });

    // Safety net for old global functions from Paymob script.
    const interval = setInterval(function () {
        const hasShow = typeof window.showLoadingIndicator === 'function';
        const hasHide = typeof window.hideLoadingIndicator === 'function';
        const hasUpdateCheckoutData = typeof window.updateCheckoutData === 'function';
        const hasAjaxCall = typeof window.ajaxCall === 'function';

        if (!hasShow || !hasHide) {
            return;
        }

        const originalShow = window.showLoadingIndicator;
        const originalHide = window.hideLoadingIndicator;

        window.showLoadingIndicator = function () {
            originalShow.apply(this, arguments);
            armLoaderTimeout();
        };

        window.hideLoadingIndicator = function () {
            originalHide.apply(this, arguments);
            clearLoaderTimer();
        };

        if (hasUpdateCheckoutData && !window.__styliiiishUpdateCheckoutWrapped) {
            const originalUpdateCheckoutData = window.updateCheckoutData;
            window.updateCheckoutData = function () {
                const forceReload = !!arguments[0];
                const now = Date.now();
                if (!forceReload && (now - lastPixelUpdateAt) < PIXEL_UPDATE_MIN_INTERVAL) {
                    return;
                }
                lastPixelUpdateAt = now;
                return originalUpdateCheckoutData.apply(this, arguments);
            };
            window.__styliiiishUpdateCheckoutWrapped = true;
        }

        if (hasAjaxCall && !window.__styliiiishAjaxCallWrapped) {
            const originalAjaxCall = window.ajaxCall;
            window.ajaxCall = function () {
                const now = Date.now();
                const forceReload = !!arguments[2];
                if (!forceReload && (now - lastAjaxCallAt) < PIXEL_UPDATE_MIN_INTERVAL) {
                    return;
                }
                lastAjaxCallAt = now;
                return originalAjaxCall.apply(this, arguments);
            };
            window.__styliiiishAjaxCallWrapped = true;
        }

        clearInterval(interval);
    }, 300);

    // Stop polling eventually to avoid any long-lived interval.
    setTimeout(function () {
        clearInterval(interval);
    }, 15000);
})(window, document, window.jQuery);
JS;

    wp_add_inline_script('styliiiish-paymob-checkout-guard', $inline_js, 'after');
}, 999);
