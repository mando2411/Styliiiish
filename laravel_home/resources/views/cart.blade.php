<!DOCTYPE html>
@php
    $currentLocale = $currentLocale ?? 'ar';
    $localePrefix = $localePrefix ?? '/ar';
    $isEnglish = $currentLocale === 'en';
    $wpBaseUrl = rtrim((string) (env('WP_PUBLIC_URL', request()->getSchemeAndHttpHost())), '/');
    $canonicalPath = $localePrefix . '/cart';

    $translations = [
        'ar' => [
            'page_title' => 'العربة | ستايلش',
            'meta_desc' => 'صفحة العربة في ستايلش لمراجعة المنتجات، تعديل الكميات، ومتابعة الدفع بسهولة وأمان.',
            'topbar' => 'المتجر الرسمي • شحن داخل مصر 2–10 أيام',
            'nav_home' => 'الرئيسية',
            'nav_shop' => 'المتجر',
            'nav_marketplace' => 'الماركت بليس',
            'nav_sell' => 'بيعي فستانك',
            'nav_blog' => 'المدونة',
            'slogan' => 'لأن كل امرأة تستحق أن تتألق',
            'title' => 'عربة التسوق',
            'subtitle' => 'راجعي طلبك قبل إتمام الشراء',
            'loading' => 'جاري تحميل العربة…',
            'empty_title' => 'العربة فارغة حالياً',
            'empty_desc' => 'أضيفي منتجاتك المفضلة من صفحة المنتج ثم عودي لإتمام طلبك.',
            'go_shop' => 'تصفح المنتجات',
            'product' => 'المنتج',
            'qty' => 'الكمية',
            'unit_price' => 'سعر القطعة',
            'line_total' => 'الإجمالي',
            'remove' => 'حذف',
            'removing' => 'جاري الحذف…',
            'updating' => 'جاري التحديث…',
            'cart_totals' => 'Cart totals',
            'subtotal' => 'Subtotal',
            'total' => 'Total',
            'proceed_checkout' => 'Proceed to checkout',
            'continue_shopping' => 'متابعة التسوق',
            'view_product' => 'عرض المنتج',
            'load_failed' => 'تعذر تحميل العربة حالياً.',
            'remove_failed' => 'تعذر حذف العنصر حالياً.',
            'update_failed' => 'تعذر تحديث الكمية حالياً.',
            'footer_brand_title' => 'ستيليش فاشون هاوس',
            'footer_brand_desc' => 'نعمل بشغف على تقديم أحدث تصاميم الفساتين لتناسب كل مناسبة خاصة بك.',
            'footer_brand_time' => 'مواعيد العمل: السبت إلى الجمعة من 11:00 صباحًا حتى 7:00 مساءً.',
            'footer_contact' => 'تواصلي معنا',
            'footer_quick' => 'روابط سريعة',
            'footer_official' => 'معلومات رسمية',
            'footer_policies' => 'سياسات وقوانين',
            'footer_about' => 'من نحن',
            'footer_privacy' => 'سياسة الخصوصية',
            'footer_terms' => 'الشروط والأحكام',
            'footer_refund' => 'سياسة الاسترجاع والاستبدال',
            'footer_faq' => 'الأسئلة الشائعة',
            'footer_shipping' => 'سياسة الشحن والتوصيل',
            'footer_cookie' => 'سياسة ملفات الارتباط',
            'footer_rights' => 'جميع الحقوق محفوظة',
        ],
        'en' => [
            'page_title' => 'Cart | Styliiiish',
            'meta_desc' => 'Your Styliiiish cart page to review products, adjust quantities, and proceed securely to checkout.',
            'topbar' => 'Official Store • Egypt shipping in 2–10 days',
            'nav_home' => 'Home',
            'nav_shop' => 'Shop',
            'nav_marketplace' => 'Marketplace',
            'nav_sell' => 'Sell Your Dress',
            'nav_blog' => 'Blog',
            'slogan' => 'Every woman deserves to shine',
            'title' => 'Shopping Cart',
            'subtitle' => 'Review your order before checkout',
            'loading' => 'Loading cart…',
            'empty_title' => 'Your cart is empty',
            'empty_desc' => 'Add your favorite products from the product page and come back to complete your order.',
            'go_shop' => 'Browse products',
            'product' => 'Product',
            'qty' => 'Qty',
            'unit_price' => 'Unit Price',
            'line_total' => 'Line Total',
            'remove' => 'Remove',
            'removing' => 'Removing…',
            'updating' => 'Updating…',
            'cart_totals' => 'Cart totals',
            'subtotal' => 'Subtotal',
            'total' => 'Total',
            'proceed_checkout' => 'Proceed to checkout',
            'continue_shopping' => 'Continue shopping',
            'view_product' => 'View product',
            'load_failed' => 'Unable to load cart right now.',
            'remove_failed' => 'Unable to remove this item right now.',
            'update_failed' => 'Unable to update quantity right now.',
            'footer_brand_title' => 'Styliiiish Fashion House',
            'footer_brand_desc' => 'We passionately curate the latest dress designs for your special occasions.',
            'footer_brand_time' => 'Working hours: Saturday to Friday, 11:00 AM to 7:00 PM.',
            'footer_contact' => 'Contact us',
            'footer_quick' => 'Quick links',
            'footer_official' => 'Official info',
            'footer_policies' => 'Policies',
            'footer_about' => 'About Us',
            'footer_privacy' => 'Privacy Policy',
            'footer_terms' => 'Terms & Conditions',
            'footer_refund' => 'Refund & Return Policy',
            'footer_faq' => 'FAQ',
            'footer_shipping' => 'Shipping Policy',
            'footer_cookie' => 'Cookie Policy',
            'footer_rights' => 'All rights reserved',
        ],
    ];

    $normalizeBrandText = fn (string $value) => $currentLocale === 'en'
        ? (preg_replace('/ستايلش/iu', 'Styliiiish', $value) ?? $value)
        : (preg_replace('/styliiiish/iu', 'ستايلش', $value) ?? $value);
    $t = fn (string $key) => $normalizeBrandText((string) ($translations[$currentLocale][$key] ?? $translations['ar'][$key] ?? $key));

    $wpLogo = 'https://styliiiish.com/wp-content/uploads/2025/11/ChatGPT-Image-Nov-2-2025-03_11_14-AM-e1762046066547.png';
    $wpIcon = 'https://styliiiish.com/wp-content/uploads/2025/11/cropped-ChatGPT-Image-Nov-2-2025-03_11_14-AM-e1762046066547.png';
    $seoUrl = $wpBaseUrl . $canonicalPath;
    $ogLocale = $isEnglish ? 'en_US' : 'ar_AR';
    $ogAltLocale = $isEnglish ? 'ar_AR' : 'en_US';
@endphp
<html lang="{{ $isEnglish ? 'en' : 'ar' }}" dir="{{ $isEnglish ? 'ltr' : 'rtl' }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="description" content="{{ $t('meta_desc') }}">
    <link rel="canonical" href="{{ $seoUrl }}">
    <link rel="alternate" hreflang="ar" href="{{ $wpBaseUrl }}/ar/cart">
    <link rel="alternate" hreflang="en" href="{{ $wpBaseUrl }}/en/cart">
    <link rel="alternate" hreflang="x-default" href="{{ $wpBaseUrl }}/ar/cart">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="{{ $ogLocale }}">
    <meta property="og:locale:alternate" content="{{ $ogAltLocale }}">
    <meta property="og:site_name" content="{{ $isEnglish ? 'Styliiiish' : 'ستايلش' }}">
    <meta property="og:title" content="{{ $t('page_title') }}">
    <meta property="og:description" content="{{ $t('meta_desc') }}">
    <meta property="og:url" content="{{ $seoUrl }}">
    <meta property="og:image" content="{{ $wpIcon }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $t('page_title') }}">
    <meta name="twitter:description" content="{{ $t('meta_desc') }}">
    <meta name="twitter:image" content="{{ $wpIcon }}">
    <meta name="theme-color" content="#d51522">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $wpIcon }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ $wpIcon }}">
    <link rel="apple-touch-icon" href="{{ $wpIcon }}">
    <title>{{ $t('page_title') }}</title>
    <style>
        :root { --wf-main-rgb: 213,21,34; --wf-main-color: rgb(var(--wf-main-rgb)); --wf-secondary-color:#17273B; --bg:#f6f7fb; --line:rgba(189,189,189,.4); --primary:var(--wf-main-color); --secondary:var(--wf-secondary-color); --danger:#dc2626; }
        *{box-sizing:border-box} body{margin:0;font-family:"Segoe UI",Tahoma,Arial,sans-serif;background:var(--bg);color:#17273B} a{text-decoration:none;color:inherit}
        .container{width:min(1180px,92%);margin:0 auto}
        .topbar{background:var(--secondary);color:#fff;font-size:13px}.topbar .container{min-height:40px;display:flex;align-items:center;justify-content:center}
        .header{background:#fff;border-bottom:1px solid var(--line);position:sticky;top:0;z-index:40}
        .header-inner{min-height:74px;display:grid;grid-template-columns:auto 1fr auto;align-items:center;gap:12px}
        .brand{display:flex;flex-direction:column;gap:2px}.brand-logo{height:38px;width:auto;max-width:min(210px,40vw);object-fit:contain}.brand-sub{font-size:11px;color:#5a6678}
        .nav{display:flex;justify-content:center;gap:8px;padding:5px;border:1px solid var(--line);border-radius:12px;background:#f9fbff}
        .nav a{padding:8px 12px;border-radius:8px;font-size:14px;font-weight:700;white-space:nowrap}.nav a.active,.nav a:hover{color:var(--primary);background:#fff4f5}
        .head-btn{border:1px solid var(--line);border-radius:10px;min-width:38px;min-height:38px;display:inline-flex;align-items:center;justify-content:center;background:#fff}.head-btn.active{color:var(--primary);border-color:rgba(213,21,34,.3);background:#fff4f5}
        .cart-head{padding:24px 0 14px}.cart-head h1{margin:0 0 6px;font-size:clamp(25px,4vw,35px)}.cart-head p{margin:0;color:#5a6678}
        .cart-layout{display:grid;grid-template-columns:1.5fr .9fr;gap:16px;margin-bottom:28px}
        .panel{background:#fff;border:1px solid var(--line);border-radius:16px;overflow:hidden}.panel-title{padding:16px 18px;border-bottom:1px solid var(--line);font-weight:800}
        .cart-list{padding:12px;display:grid;gap:10px}.cart-item{display:grid;grid-template-columns:92px 1fr auto;gap:10px;align-items:center;border:1px solid var(--line);border-radius:12px;padding:10px}
        .thumb{width:92px;height:92px;border-radius:10px;object-fit:cover;border:1px solid var(--line);background:#f2f2f5}
        .name{margin:0 0 6px;font-size:15px;line-height:1.45}
        .item-meta{display:flex;gap:14px;flex-wrap:wrap;color:#5a6678;font-size:12px}
        .qty-wrap{display:inline-flex;align-items:center;border:1px solid var(--line);border-radius:10px;overflow:hidden;background:#fff}
        .qty-btn{width:32px;height:32px;border:0;background:#fff;cursor:pointer;font-weight:900;color:#17273B}.qty-btn:disabled{opacity:.45;cursor:not-allowed}
        .qty-val{min-width:36px;text-align:center;font-weight:800}
        .item-actions{display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-end}
        .btn{border:1px solid var(--line);border-radius:10px;min-height:38px;padding:0 12px;display:inline-flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;background:#fff;cursor:pointer}
        .btn:hover{border-color:var(--primary);color:var(--primary)}.btn-danger{color:var(--danger);border-color:rgba(220,38,38,.28)}
        .totals{padding:14px;display:grid;gap:10px}.totals-row{display:flex;justify-content:space-between;gap:12px;color:#4b5563}.totals-row strong{color:#111827}
        .totals-row.total{padding-top:10px;border-top:1px dashed var(--line);font-size:16px}.checkout-btn{background:var(--primary);color:#fff;border-color:var(--primary);min-height:44px}
        .state{border:1px dashed var(--line);border-radius:14px;text-align:center;padding:30px 14px;color:#6b7280}.state h3{margin:0 0 8px;color:#111827;font-size:22px}
        .site-footer{margin-top:10px;background:#0f1a2a;color:#fff;border-top:4px solid var(--primary)}
        .footer-grid{padding:32px 0 20px;display:grid;grid-template-columns:1.5fr 1fr 1fr 1.1fr;gap:20px}.footer-brand-logo{width:156px;max-width:100%;object-fit:contain;margin-bottom:10px;display:block}
        .footer-brand h4{margin:0 0 8px;font-size:18px}.footer-brand p{margin:0 0 8px;color:#c8d3e2;font-size:14px;line-height:1.7}
        .footer-contact-row{display:flex;flex-wrap:wrap;gap:8px;margin-top:8px}.footer-contact-row a{color:#fff;border:1px solid rgba(255,255,255,.25);border-radius:999px;padding:6px 10px;font-size:12px}
        .footer-col h5{margin:0 0 10px;font-size:15px}.footer-links{list-style:none;margin:0;padding:0;display:grid;gap:8px}.footer-links a{color:#d7e0ed;font-size:14px}.footer-links a:hover{color:#fff}
        .footer-bottom{border-top:1px solid rgba(255,255,255,.18);padding:12px 0;display:flex;justify-content:space-between;gap:10px;color:#b8c5d8;font-size:13px;flex-wrap:wrap}.footer-bottom a{color:#fff}
        @media (max-width:980px){.header-inner{grid-template-columns:1fr;padding:10px 0}.brand,.nav{justify-content:center;text-align:center}.cart-layout{grid-template-columns:1fr}.footer-grid{grid-template-columns:1fr 1fr}}
        @media (max-width:720px){.cart-item{grid-template-columns:78px 1fr}.thumb{width:78px;height:78px}.item-actions{grid-column:1/-1;justify-content:stretch}.item-actions .btn{flex:1 1 100%}.nav{overflow-x:auto;justify-content:flex-start}}
        @media (max-width:390px){.footer-grid{grid-template-columns:1fr}}
    </style>
</head>
<body>
<div class="topbar"><div class="container">{{ $t('topbar') }}</div></div>
<header class="header"><div class="container header-inner">
    <a class="brand" href="{{ $localePrefix }}"><img class="brand-logo" src="{{ $wpLogo }}" alt="Styliiiish" onerror="this.onerror=null;this.src='/brand/logo.png';"><span class="brand-sub">{{ $t('slogan') }}</span></a>
    <nav class="nav" aria-label="Main Navigation">
        <a href="{{ $localePrefix }}">{{ $t('nav_home') }}</a><a href="{{ $localePrefix }}/shop">{{ $t('nav_shop') }}</a><a href="{{ $localePrefix }}/marketplace">{{ $t('nav_marketplace') }}</a><a href="https://styliiiish.com/my-dresses/" target="_blank" rel="noopener">{{ $t('nav_sell') }}</a><a href="https://styliiiish.com/blog/" target="_blank" rel="noopener">{{ $t('nav_blog') }}</a>
    </nav>
    <div style="display:flex;gap:8px;justify-content:center;"><a class="head-btn" href="https://styliiiish.com/my-account/" target="_blank" rel="noopener">👤</a><a class="head-btn" href="{{ $localePrefix }}/wishlist">❤</a><a class="head-btn active" href="{{ $localePrefix }}/cart">🛒</a></div>
</div></header>

<main class="container">
    <section class="cart-head"><h1>{{ $t('title') }}</h1><p>{{ $t('subtitle') }}</p></section>
    <section class="cart-layout">
        <div class="panel">
            <div class="panel-title">{{ $t('product') }}</div>
            <div id="cartList" class="cart-list"><div class="state">{{ $t('loading') }}</div></div>
        </div>
        <aside class="panel">
            <div class="panel-title">{{ $t('cart_totals') }}</div>
            <div class="totals">
                <div class="totals-row"><span>{{ $t('subtotal') }}</span><strong id="cartSubtotal">—</strong></div>
                <div class="totals-row total"><span>{{ $t('total') }}</span><strong id="cartTotal">—</strong></div>
                <a class="btn checkout-btn" id="proceedCheckoutBtn" href="{{ $wpBaseUrl }}/checkout/">{{ $t('proceed_checkout') }}</a>
                <a class="btn" href="{{ $localePrefix }}/shop">{{ $t('continue_shopping') }}</a>
            </div>
        </aside>
    </section>
</main>

<footer class="site-footer"><div class="container footer-grid"><div class="footer-brand"><img class="footer-brand-logo" src="{{ $wpLogo }}" alt="Styliiiish" onerror="this.onerror=null;this.src='/brand/logo.png';"><h4>{{ $t('footer_brand_title') }}</h4><p>{{ $t('footer_brand_desc') }}</p><p>{{ $t('footer_brand_time') }}</p><div class="footer-contact-row"><a href="{{ $localePrefix }}/contact-us">{{ $t('footer_contact') }}</a><a href="tel:+201050874255">+2 010-5087-4255</a></div></div>
<div class="footer-col"><h5>{{ $t('footer_quick') }}</h5><ul class="footer-links"><li><a href="{{ $localePrefix }}">{{ $t('nav_home') }}</a></li><li><a href="{{ $localePrefix }}/shop">{{ $t('nav_shop') }}</a></li><li><a href="{{ $localePrefix }}/blog">{{ $t('nav_blog') }}</a></li><li><a href="{{ $localePrefix }}/about-us">{{ $t('footer_about') }}</a></li><li><a href="{{ $localePrefix }}/contact-us">{{ $t('footer_contact') }}</a></li><li><a href="{{ $localePrefix }}/categories">{{ $isEnglish ? 'Categories' : 'الأقسام' }}</a></li></ul></div>
<div class="footer-col"><h5>{{ $t('footer_official') }}</h5><ul class="footer-links"><li><a href="https://maps.app.goo.gl/MCdcFEcFoR4tEjpT8" target="_blank" rel="noopener">1 Nabil Khalil St, Nasr City, Cairo, Egypt</a></li><li><a href="tel:+201050874255">+2 010-5087-4255</a></li><li><a href="mailto:email@styliiiish.com">email@styliiiish.com</a></li></ul></div>
<div class="footer-col"><h5>{{ $t('footer_policies') }}</h5><ul class="footer-links"><li><a href="{{ $localePrefix }}/about-us">{{ $t('footer_about') }}</a></li><li><a href="{{ $localePrefix }}/privacy-policy">{{ $t('footer_privacy') }}</a></li><li><a href="{{ $localePrefix }}/terms-conditions">{{ $t('footer_terms') }}</a></li><li><a href="{{ $localePrefix }}/refund-return-policy">{{ $t('footer_refund') }}</a></li><li><a href="{{ $localePrefix }}/faq">{{ $t('footer_faq') }}</a></li><li><a href="{{ $localePrefix }}/shipping-delivery-policy">{{ $t('footer_shipping') }}</a></li><li><a href="{{ $localePrefix }}/cookie-policy">{{ $t('footer_cookie') }}</a></li></ul></div></div>
<div class="container footer-bottom"><span>{{ $t('footer_rights') }} © {{ date('Y') }} Styliiiish | <a href="https://websiteflexi.com/" target="_blank" rel="noopener">Website Flexi</a></span><span><a href="https://styliiiish.com/" target="_blank" rel="noopener">styliiiish.com</a></span></div></footer>

<script>
(() => {
    const localePrefix = @json($localePrefix);
    const adminAjaxUrl = @json($wpBaseUrl . '/wp-admin/admin-ajax.php');
    const texts = {
        loading: @json($t('loading')), emptyTitle: @json($t('empty_title')), emptyDesc: @json($t('empty_desc')), goShop: @json($t('go_shop')),
        qty: @json($t('qty')), unitPrice: @json($t('unit_price')), lineTotal: @json($t('line_total')),
        remove: @json($t('remove')), removing: @json($t('removing')), updating: @json($t('updating')),
        removeFailed: @json($t('remove_failed')), loadFailed: @json($t('load_failed')), updateFailed: @json($t('update_failed')),
        viewProduct: @json($t('view_product'))
    };

    const cartList = document.getElementById('cartList');
    const cartSubtotal = document.getElementById('cartSubtotal');
    const cartTotal = document.getElementById('cartTotal');
    const proceedCheckoutBtn = document.getElementById('proceedCheckoutBtn');

    const escapeHtml = (value) => String(value || '').replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;').replaceAll("'",'&#039;');

    const renderEmpty = () => {
        cartList.innerHTML = `<div class="state"><h3>${escapeHtml(texts.emptyTitle)}</h3><p>${escapeHtml(texts.emptyDesc)}</p><a class="btn" href="${localePrefix}/shop" style="margin-top:12px;">${escapeHtml(texts.goShop)}</a></div>`;
        cartSubtotal.textContent = '—';
        cartTotal.textContent = '—';
    };

    const renderTotals = (payload) => {
        cartSubtotal.innerHTML = payload.subtotal_html || '—';
        cartTotal.innerHTML = payload.total_html || payload.subtotal_html || '—';
        if (proceedCheckoutBtn && payload.checkout_url) proceedCheckoutBtn.href = payload.checkout_url;
    };

    const renderCart = (payload) => {
        const items = Array.isArray(payload.items) ? payload.items : [];
        renderTotals(payload || {});

        if (items.length === 0) {
            renderEmpty();
            return;
        }

        cartList.innerHTML = items.map((item) => {
            const key = escapeHtml(item.key || '');
            const name = escapeHtml(item.name || '');
            const image = escapeHtml(item.image || '');
            const url = escapeHtml(item.url || '#');
            const qty = Math.max(1, Number(item.qty) || 1);
            const unitPrice = item.price_html || '—';
            const lineTotal = item.line_total_html || unitPrice;
            return `
                <article class="cart-item" data-cart-key="${key}">
                    <a href="${url}"><img class="thumb" src="${image}" alt="${name}" loading="lazy"></a>
                    <div>
                        <h3 class="name">${name}</h3>
                        <div class="item-meta"><span>${escapeHtml(texts.unitPrice)}: <strong>${unitPrice}</strong></span><span>${escapeHtml(texts.lineTotal)}: <strong>${lineTotal}</strong></span></div>
                        <div style="margin-top:8px;display:flex;justify-content:space-between;gap:10px;align-items:center;flex-wrap:wrap;">
                            <div class="qty-wrap" data-qty-wrap>
                                <button class="qty-btn" type="button" data-qty-change="-1">−</button>
                                <span class="qty-val">${qty}</span>
                                <button class="qty-btn" type="button" data-qty-change="1">+</button>
                            </div>
                            <div class="item-actions">
                                <a class="btn" href="${url}">${escapeHtml(texts.viewProduct)}</a>
                                <button class="btn btn-danger" type="button" data-remove-item>${escapeHtml(texts.remove)}</button>
                            </div>
                        </div>
                    </div>
                </article>
            `;
        }).join('');
    };

    const fetchSummary = async () => {
        cartList.innerHTML = `<div class="state">${escapeHtml(texts.loading)}</div>`;
        try {
            const response = await fetch(`${adminAjaxUrl}?action=styliiiish_cart_summary`, { method: 'GET', credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const result = await response.json();
            if (!response.ok || !result || !result.success) throw new Error('summary_failed');
            renderCart(result.data || {});
        } catch (error) {
            cartList.innerHTML = `<div class="state">${escapeHtml(texts.loadFailed)}</div>`;
            cartSubtotal.textContent = '—';
            cartTotal.textContent = '—';
        }
    };

    const removeItem = async (cartKey, button) => {
        if (!cartKey) return;
        const original = button?.textContent || texts.remove;
        if (button) { button.disabled = true; button.textContent = texts.removing; }
        try {
            const params = new URLSearchParams();
            params.set('action', 'styliiiish_remove_from_cart');
            params.set('cart_key', cartKey);
            const response = await fetch(adminAjaxUrl, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8', 'X-Requested-With': 'XMLHttpRequest' }, body: params.toString() });
            const result = await response.json();
            if (!response.ok || !result || !result.success) throw new Error('remove_failed');
            renderCart(result.data || {});
        } catch (error) {
            if (button) { button.disabled = false; button.textContent = original; }
            alert(texts.removeFailed);
        }
    };

    const updateQty = async (cartKey, nextQty, wrap) => {
        if (!cartKey) return;
        const qty = Math.max(1, Number(nextQty) || 1);
        const buttons = wrap ? Array.from(wrap.querySelectorAll('button')) : [];
        buttons.forEach((btn) => btn.disabled = true);
        try {
            const params = new URLSearchParams();
            params.set('action', 'styliiiish_update_cart_qty');
            params.set('cart_key', cartKey);
            params.set('qty', String(qty));
            const response = await fetch(adminAjaxUrl, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8', 'X-Requested-With': 'XMLHttpRequest' }, body: params.toString() });
            const result = await response.json();
            if (!response.ok || !result || !result.success) throw new Error('qty_failed');
            renderCart(result.data || {});
        } catch (error) {
            buttons.forEach((btn) => btn.disabled = false);
            alert(texts.updateFailed);
        }
    };

    cartList.addEventListener('click', (event) => {
        const row = event.target.closest('.cart-item');
        if (!row) return;
        const cartKey = row.getAttribute('data-cart-key') || '';

        const removeButton = event.target.closest('[data-remove-item]');
        if (removeButton) { removeItem(cartKey, removeButton); return; }

        const qtyChangeBtn = event.target.closest('[data-qty-change]');
        if (!qtyChangeBtn) return;
        const wrap = qtyChangeBtn.closest('[data-qty-wrap]');
        const qtyVal = wrap ? wrap.querySelector('.qty-val') : null;
        const currentQty = Math.max(1, Number((qtyVal && qtyVal.textContent) || 1));
        const delta = Number(qtyChangeBtn.getAttribute('data-qty-change') || 0);
        if (!delta) return;
        updateQty(cartKey, currentQty + delta, wrap);
    });

    fetchSummary();
})();
</script>
</body>
</html>
