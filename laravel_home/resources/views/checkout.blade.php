<!DOCTYPE html>
@php
    $currentLocale = $currentLocale ?? 'ar';
    $localePrefix = $localePrefix ?? '/ar';
    $isEnglish = $currentLocale === 'en';
    $wpBaseUrl = rtrim((string) (env('WP_PUBLIC_URL', request()->getSchemeAndHttpHost())), '/');
    $canonicalPath = $localePrefix . '/checkout';

    $translations = [
        'ar' => [
            'page_title' => 'إتمام الطلب | ستايلش',
            'meta_desc' => 'صفحة إتمام الطلب في ستايلش مع تجربة سلسة وآمنة وتحويل مباشر لبوابة الدفع المعتمدة.',
            'topbar' => 'دفع آمن عبر Paymob • جميع المعاملات تتم على بوابة الدفع المعتمدة',
            'nav_home' => 'الرئيسية',
            'nav_shop' => 'المتجر',
            'nav_marketplace' => 'الماركت بليس',
            'nav_sell' => 'بيعي فستانك',
            'nav_blog' => 'المدونة',
            'slogan' => 'لأن كل امرأة تستحق أن تتألق',
            'title' => 'إتمام الطلب',
            'subtitle' => 'راجعي طلبك ثم انتقلي إلى بوابة الدفع الآمنة لإتمام العملية.',
            'step_review' => 'مراجعة الطلب',
            'step_address' => 'تأكيد العنوان',
            'step_payment' => 'الدفع عبر Paymob',
            'loading' => 'جاري تحميل تفاصيل الطلب…',
            'load_failed' => 'تعذر تحميل بيانات الطلب حاليًا.',
            'empty_title' => 'لا توجد عناصر في العربة',
            'empty_desc' => 'أضيفي منتجات أولًا ثم عودي لإتمام الطلب.',
            'go_cart' => 'العودة إلى العربة',
            'summary' => 'ملخص الطلب',
            'subtotal' => 'المجموع الفرعي',
            'shipping' => 'الشحن',
            'total' => 'الإجمالي',
            'item' => 'عنصر',
            'items' => 'عناصر',
            'secure_title' => 'الدفع الآمن',
            'secure_desc' => 'لحماية بياناتك وضمان التوافق الكامل مع بوابة Paymob، سيتم تحويلك الآن إلى صفحة الدفع الرسمية.',
            'secure_note' => 'لا يتم تخزين بيانات البطاقة داخل هذه الصفحة.',
            'pay_now' => 'الانتقال للدفع الآمن',
            'back_cart' => 'الرجوع للعربة',
            'policies' => 'السياسات',
            'footer_contact' => 'تواصلي معنا',
            'footer_privacy' => 'سياسة الخصوصية',
            'footer_terms' => 'الشروط والأحكام',
            'footer_refund' => 'سياسة الاسترجاع والاستبدال',
            'footer_shipping' => 'سياسة الشحن والتوصيل',
            'footer_rights' => 'جميع الحقوق محفوظة',
        ],
        'en' => [
            'page_title' => 'Checkout | Styliiiish',
            'meta_desc' => 'Smooth and secure Styliiiish checkout with direct handoff to the official payment gateway.',
            'topbar' => 'Secure payments via Paymob • All transactions are processed on the official gateway',
            'nav_home' => 'Home',
            'nav_shop' => 'Shop',
            'nav_marketplace' => 'Marketplace',
            'nav_sell' => 'Sell Your Dress',
            'nav_blog' => 'Blog',
            'slogan' => 'Every woman deserves to shine',
            'title' => 'Checkout',
            'subtitle' => 'Review your order, then continue to the secure payment gateway.',
            'step_review' => 'Review Order',
            'step_address' => 'Confirm Address',
            'step_payment' => 'Pay via Paymob',
            'loading' => 'Loading order details…',
            'load_failed' => 'Unable to load your order details right now.',
            'empty_title' => 'Your cart is empty',
            'empty_desc' => 'Add products first, then return to checkout.',
            'go_cart' => 'Back to cart',
            'summary' => 'Order Summary',
            'subtotal' => 'Subtotal',
            'shipping' => 'Shipping',
            'total' => 'Total',
            'item' => 'item',
            'items' => 'items',
            'secure_title' => 'Secure Payment',
            'secure_desc' => 'To keep full compatibility with Paymob and protect your data, you will continue on the official checkout page.',
            'secure_note' => 'Card data is not stored on this page.',
            'pay_now' => 'Continue to Secure Payment',
            'back_cart' => 'Back to cart',
            'policies' => 'Policies',
            'footer_contact' => 'Contact us',
            'footer_privacy' => 'Privacy Policy',
            'footer_terms' => 'Terms & Conditions',
            'footer_refund' => 'Refund & Return Policy',
            'footer_shipping' => 'Shipping Policy',
            'footer_rights' => 'All rights reserved',
        ],
    ];

    $t = fn (string $key) => (string) ($translations[$currentLocale][$key] ?? $translations['ar'][$key] ?? $key);
    $wpLogo = 'https://styliiiish.com/wp-content/uploads/2025/11/ChatGPT-Image-Nov-2-2025-03_11_14-AM-e1762046066547.png';
    $wpIcon = 'https://styliiiish.com/wp-content/uploads/2025/11/cropped-ChatGPT-Image-Nov-2-2025-03_11_14-AM-e1762046066547.png';
    $seoUrl = $wpBaseUrl . $canonicalPath;
@endphp
<html lang="{{ $isEnglish ? 'en' : 'ar' }}" dir="{{ $isEnglish ? 'ltr' : 'rtl' }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="{{ $t('meta_desc') }}">
    <link rel="canonical" href="{{ $seoUrl }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $t('page_title') }}">
    <meta property="og:description" content="{{ $t('meta_desc') }}">
    <meta property="og:url" content="{{ $seoUrl }}">
    <meta property="og:image" content="{{ $wpIcon }}">
    <title>{{ $t('page_title') }}</title>
    <style>
        :root{--wf-main-rgb:213,21,34;--wf-main-color:rgb(var(--wf-main-rgb));--wf-secondary-color:#17273B;--bg:#f6f7fb;--line:rgba(189,189,189,.4);--primary:var(--wf-main-color);--secondary:var(--wf-secondary-color)}
        *{box-sizing:border-box}body{margin:0;font-family:"Segoe UI",Tahoma,Arial,sans-serif;background:var(--bg);color:#17273B}a{text-decoration:none;color:inherit}
        .container{width:min(1180px,92%);margin:0 auto}
        .topbar{background:var(--secondary);color:#fff;font-size:13px}.topbar .container{min-height:40px;display:flex;align-items:center;justify-content:center}
        .header{background:#fff;border-bottom:1px solid var(--line)}.header-inner{min-height:74px;display:grid;grid-template-columns:auto 1fr auto;gap:12px;align-items:center}
        .brand{display:flex;flex-direction:column;gap:2px}.brand-logo{height:38px;width:auto;max-width:min(210px,40vw);object-fit:contain}.brand-sub{font-size:11px;color:#5a6678}
        .nav{display:flex;justify-content:center;gap:8px;padding:5px;border:1px solid var(--line);border-radius:12px;background:#f9fbff}
        .nav a{padding:8px 12px;border-radius:8px;font-size:14px;font-weight:700;white-space:nowrap}.nav a:hover{color:var(--primary);background:#fff4f5}
        .head-btn{border:1px solid var(--line);border-radius:10px;min-width:38px;min-height:38px;display:inline-flex;align-items:center;justify-content:center;background:#fff}
        .hero{padding:26px 0 12px}.hero h1{margin:0 0 8px;font-size:clamp(28px,4.1vw,40px)}.hero p{margin:0;color:#5a6678}
        .steps{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin:14px 0 20px}
        .step{background:#fff;border:1px solid var(--line);border-radius:12px;padding:10px 12px;font-weight:700;font-size:14px;display:flex;align-items:center;gap:8px;animation:slideUp .45s ease both}
        .step:nth-child(2){animation-delay:.08s}.step:nth-child(3){animation-delay:.16s}
        .dot{width:10px;height:10px;border-radius:999px;background:var(--primary)}
        .layout{display:grid;grid-template-columns:1.25fr .9fr;gap:18px;margin-bottom:26px}
        .card{background:#fff;border:1px solid var(--line);border-radius:16px;box-shadow:0 8px 24px rgba(23,39,59,.05);overflow:hidden;animation:fadeIn .45s ease both}
        .card h2{margin:0;padding:15px 16px;border-bottom:1px solid var(--line);font-size:18px;background:#fbfcff}
        .items{padding:12px;display:grid;gap:10px}
        .item-row{display:grid;grid-template-columns:68px 1fr auto;gap:10px;align-items:center;border:1px solid var(--line);border-radius:12px;padding:10px;transition:transform .18s ease,border-color .18s ease}
        .item-row:hover{transform:translateY(-1px);border-color:rgba(213,21,34,.25)}
        .item-row img{width:68px;height:68px;object-fit:cover;border-radius:10px;border:1px solid var(--line);background:#f1f3f8}
        .item-name{font-size:14px;font-weight:700;line-height:1.45}.item-meta{font-size:12px;color:#6b7280;margin-top:4px}
        .amount{font-size:13px;font-weight:800}
        .summary{padding:14px;display:grid;gap:10px}.row{display:flex;justify-content:space-between;gap:10px;color:#4b5563}.row strong{color:#111827}
        .row.total{padding-top:10px;border-top:1px dashed var(--line);font-size:17px}.row.total strong{font-size:22px}
        .secure-box{border:1px dashed var(--line);border-radius:12px;padding:12px;background:#fcfdff}
        .secure-title{font-size:15px;font-weight:800;margin:0 0 6px}.secure-text{margin:0;color:#4b5563;font-size:13px;line-height:1.7}
        .secure-note{margin-top:6px;color:#6b7280;font-size:12px}
        .actions{display:grid;gap:9px}.btn{border:1px solid var(--line);border-radius:11px;min-height:44px;padding:0 12px;display:inline-flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;background:#fff;cursor:pointer;transition:all .18s ease}
        .btn:hover{transform:translateY(-1px)}
        .btn-primary{background:var(--primary);border-color:var(--primary);color:#fff}.btn-primary:hover{background:var(--primary);border-color:var(--primary);color:#fff}
        .chips{display:flex;flex-wrap:wrap;gap:7px}.chip{border:1px solid var(--line);border-radius:999px;padding:7px 10px;background:#fff;font-size:12px;font-weight:700}
        .state{border:1px dashed var(--line);border-radius:13px;padding:24px 12px;text-align:center;color:#6b7280}
        .footer{margin-top:8px;background:#0f1a2a;color:#fff;border-top:4px solid var(--primary)}
        .footer-inner{padding:16px 0;display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;font-size:13px;color:#c8d3e2}
        @keyframes fadeIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}
        @keyframes slideUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
        @media (max-width:980px){.header-inner{grid-template-columns:1fr;padding:10px 0}.brand,.nav{justify-content:center;text-align:center}.layout{grid-template-columns:1fr}.steps{grid-template-columns:1fr}}
        @media (max-width:680px){.item-row{grid-template-columns:56px 1fr}.item-row img{width:56px;height:56px}.amount{grid-column:1/-1}}
    </style>
</head>
<body>
<div class="topbar"><div class="container">{{ $t('topbar') }}</div></div>

<header class="header"><div class="container header-inner">
    <a class="brand" href="{{ $localePrefix }}"><img class="brand-logo" src="{{ $wpLogo }}" alt="Styliiiish"><span class="brand-sub">{{ $t('slogan') }}</span></a>
    <nav class="nav" aria-label="Main Navigation">
        <a href="{{ $localePrefix }}">{{ $t('nav_home') }}</a>
        <a href="{{ $localePrefix }}/shop">{{ $t('nav_shop') }}</a>
        <a href="{{ $localePrefix }}/marketplace">{{ $t('nav_marketplace') }}</a>
        <a href="https://styliiiish.com/my-dresses/" target="_blank" rel="noopener">{{ $t('nav_sell') }}</a>
        <a href="https://styliiiish.com/blog/" target="_blank" rel="noopener">{{ $t('nav_blog') }}</a>
    </nav>
    <div style="display:flex;gap:8px;justify-content:center;"><a class="head-btn" href="{{ $localePrefix }}/wishlist">❤</a><a class="head-btn" href="{{ $localePrefix }}/cart">🛒</a></div>
</div></header>

<main class="container">
    <section class="hero">
        <h1>{{ $t('title') }}</h1>
        <p>{{ $t('subtitle') }}</p>
    </section>

    <section class="steps">
        <div class="step"><span class="dot"></span>{{ $t('step_review') }}</div>
        <div class="step"><span class="dot"></span>{{ $t('step_address') }}</div>
        <div class="step"><span class="dot"></span>{{ $t('step_payment') }}</div>
    </section>

    <section class="layout">
        <section class="card">
            <h2>{{ $t('summary') }}</h2>
            <div class="items" id="checkoutItems"><div class="state">{{ $t('loading') }}</div></div>
        </section>

        <aside class="card">
            <h2>{{ $t('secure_title') }}</h2>
            <div class="summary">
                <div class="row"><span>{{ $t('subtotal') }}</span><strong id="sumSubtotal">—</strong></div>
                <div class="row"><span>{{ $t('shipping') }}</span><strong id="sumShipping">—</strong></div>
                <div class="row total"><span>{{ $t('total') }}</span><strong id="sumTotal">—</strong></div>

                <div class="secure-box">
                    <p class="secure-title">{{ $t('secure_title') }}</p>
                    <p class="secure-text">{{ $t('secure_desc') }}</p>
                    <div class="secure-note">{{ $t('secure_note') }}</div>
                </div>

                <div class="actions">
                    <a id="securePayBtn" class="btn btn-primary" href="{{ $wpBaseUrl }}/checkout/">{{ $t('pay_now') }}</a>
                    <a class="btn" href="{{ $localePrefix }}/cart">{{ $t('back_cart') }}</a>
                </div>

                <div>
                    <div style="font-weight:800;font-size:13px;margin-bottom:7px;">{{ $t('policies') }}</div>
                    <div class="chips">
                        <a class="chip" href="{{ $localePrefix }}/privacy-policy">{{ $t('footer_privacy') }}</a>
                        <a class="chip" href="{{ $localePrefix }}/terms-conditions">{{ $t('footer_terms') }}</a>
                        <a class="chip" href="{{ $localePrefix }}/refund-return-policy">{{ $t('footer_refund') }}</a>
                        <a class="chip" href="{{ $localePrefix }}/shipping-delivery-policy">{{ $t('footer_shipping') }}</a>
                    </div>
                </div>
            </div>
        </aside>
    </section>
</main>

<footer class="footer"><div class="container footer-inner"><span>{{ $t('footer_rights') }} © {{ date('Y') }} Styliiiish</span><span><a href="{{ $localePrefix }}/contact-us">{{ $t('footer_contact') }}</a></span></div></footer>

<script>
(() => {
    const currentLocale = @json($currentLocale);
    const localePrefix = @json($localePrefix);
    const adminAjaxUrl = @json($wpBaseUrl . '/wp-admin/admin-ajax.php');
    const t = {
        loading: @json($t('loading')),
        loadFailed: @json($t('load_failed')),
        emptyTitle: @json($t('empty_title')),
        emptyDesc: @json($t('empty_desc')),
        goCart: @json($t('go_cart')),
        item: @json($t('item')),
        items: @json($t('items')),
    };

    const itemsWrap = document.getElementById('checkoutItems');
    const subtotalEl = document.getElementById('sumSubtotal');
    const shippingEl = document.getElementById('sumShipping');
    const totalEl = document.getElementById('sumTotal');
    const securePayBtn = document.getElementById('securePayBtn');

    const esc = (value) => String(value || '').replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;').replaceAll("'", '&#039;');

    const renderEmpty = () => {
        itemsWrap.innerHTML = `<div class="state"><h3 style="margin:0 0 8px;">${esc(t.emptyTitle)}</h3><p style="margin:0 0 12px;">${esc(t.emptyDesc)}</p><a class="btn" href="${localePrefix}/cart">${esc(t.goCart)}</a></div>`;
        subtotalEl.textContent = '—';
        shippingEl.textContent = '—';
        totalEl.textContent = '—';
    };

    const render = (payload) => {
        const items = Array.isArray(payload.items) ? payload.items : [];
        if (!items.length) { renderEmpty(); return; }

        const shippingLines = Array.isArray(payload.shipping_lines) ? payload.shipping_lines : [];
        const firstShipping = shippingLines.length ? shippingLines[0] : null;

        subtotalEl.innerHTML = payload.subtotal_html || '—';
        shippingEl.innerHTML = (firstShipping && firstShipping.cost_html) ? firstShipping.cost_html : (payload.shipping_total_html || '—');
        totalEl.innerHTML = payload.total_html || payload.subtotal_html || '—';
        if (securePayBtn && payload.checkout_url) securePayBtn.href = payload.checkout_url;

        itemsWrap.innerHTML = items.map((item) => {
            const qty = Math.max(1, Number(item.qty) || 1);
            const itemWord = qty > 1 ? t.items : t.item;
            return `
                <article class="item-row">
                    <a href="${esc(item.url || '#')}"><img src="${esc(item.image || '')}" alt="${esc(item.name || '')}"></a>
                    <div>
                        <div class="item-name">${esc(item.name || '')}</div>
                        <div class="item-meta">${qty} ${esc(itemWord)}</div>
                    </div>
                    <div class="amount">${item.line_total_html || item.price_html || '—'}</div>
                </article>
            `;
        }).join('');
    };

    const loadSummary = async () => {
        itemsWrap.innerHTML = `<div class="state">${esc(t.loading)}</div>`;
        try {
            const query = new URLSearchParams({ action: 'styliiiish_cart_summary', lang: currentLocale });
            const response = await fetch(`${adminAjaxUrl}?${query.toString()}`, {
                method: 'GET',
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const result = await response.json();
            if (!response.ok || !result || !result.success) throw new Error('summary_failed');
            render(result.data || {});
        } catch (error) {
            itemsWrap.innerHTML = `<div class="state">${esc(t.loadFailed)}</div>`;
            subtotalEl.textContent = '—';
            shippingEl.textContent = '—';
            totalEl.textContent = '—';
        }
    };

    loadSummary();
})();
</script>
</body>
</html>