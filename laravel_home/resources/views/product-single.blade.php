<!DOCTYPE html>
@php
    $currentLocale = $currentLocale ?? 'ar';
    $localePrefix = $localePrefix ?? '/ar';
    $isEnglish = $currentLocale === 'en';
    $wpBaseUrl = rtrim((string) ($wpBaseUrl ?? env('WP_PUBLIC_URL', request()->getSchemeAndHttpHost())), '/');
    $canonicalPath = $localePrefix . '/item/' . rawurlencode((string) ($product->post_name ?? ''));

    $translations = [
        'ar' => [
            'page_title' => (($product->post_title ?? 'المنتج') . ' | ستايليش'),
            'topbar' => 'المتجر الرسمي • شحن داخل مصر 2–10 أيام',
            'brand_sub' => 'لأن كل امرأة تستحق أن تتألق',
            'home' => 'الرئيسية',
            'shop' => 'المتجر',
            'marketplace' => 'الماركت بليس',
            'sell_dress' => 'بيعي فستانك',
            'blog' => 'المدونة',
            'dress_details' => 'تفاصيل الفستان ✨',
            'material' => 'الخامة',
            'color' => 'اللون',
            'condition' => 'الحالة المتاحة',
            'sizes' => 'المقاسات المتاحة',
            'delivery_title' => 'وقت التوصيل 🚚',
            'delivery_note' => 'لجميع تفاصيل الشحن راجعي سياسة الشحن والتوصيل.',
            'shipping_policy' => 'سياسة الشحن والتوصيل',
            'size_guide' => '📏 دليل المقاسات',
            'size_guide_open' => 'عرض دليل المقاسات',
            'close' => 'إغلاق',
            'description' => 'وصف المنتج',
            'na' => 'غير محدد',
            'contact_for_price' => 'تواصل لمعرفة السعر',
            'currency' => 'ج.م',
            'qty' => 'الكمية',
            'select_option' => 'اختاري :label',
            'add_to_cart' => 'Add to Cart',
            'choose_options_first' => 'اختاري المقاس/اللون أولاً',
            'out_of_stock' => 'هذا الاختيار غير متاح حالياً',
            'related' => 'منتجات مشابهة',
            'quick_links' => 'روابط سريعة',
            'official_info' => 'معلومات رسمية',
            'policies' => 'سياسات وقوانين',
            'contact_us' => 'تواصلي معنا',
            'direct_call' => 'اتصال مباشر',
            'all_rights' => 'جميع الحقوق محفوظة © :year Styliiiish | تشغيل وتطوير',
            'view_product' => 'معاينة',
            'buy_now' => 'اطلبي الآن',
            'shop_desc' => 'اكتشفي أحدث فساتين السهرة والزفاف والخطوبة بأسعار تنافسية.',
        ],
        'en' => [
            'page_title' => (($product->post_title ?? 'Product') . ' | Styliiiish'),
            'topbar' => 'Official Store • Egypt shipping in 2–10 days',
            'brand_sub' => 'Because every woman deserves to shine',
            'home' => 'Home',
            'shop' => 'Shop',
            'marketplace' => 'Marketplace',
            'sell_dress' => 'Sell Your Dress',
            'blog' => 'Blog',
            'dress_details' => 'Dress Details ✨',
            'material' => 'Material',
            'color' => 'Color',
            'condition' => 'Available Conditions',
            'sizes' => 'Available Sizes',
            'delivery_title' => 'Delivery Time 🚚',
            'delivery_note' => 'For full shipping details, please review our Shipping & Delivery Policy.',
            'shipping_policy' => 'Shipping & Delivery Policy',
            'size_guide' => '📏 Size Guide',
            'size_guide_open' => 'Open Size Guide',
            'close' => 'Close',
            'description' => 'Product Description',
            'na' => 'N/A',
            'contact_for_price' => 'Contact for Price',
            'currency' => 'EGP',
            'qty' => 'Quantity',
            'select_option' => 'Select :label',
            'add_to_cart' => 'Add to Cart',
            'choose_options_first' => 'Please select options first',
            'out_of_stock' => 'This selection is out of stock',
            'related' => 'Related Products',
            'quick_links' => 'Quick Links',
            'official_info' => 'Official Info',
            'policies' => 'Policies',
            'contact_us' => 'Contact Us',
            'direct_call' => 'Direct Call',
            'all_rights' => 'All rights reserved © :year Styliiiish | Powered by',
            'view_product' => 'View',
            'buy_now' => 'Order Now',
            'shop_desc' => 'Discover latest evening, bridal, and engagement dresses at competitive prices.',
        ],
    ];

    $t = fn (string $key) => $translations[$currentLocale][$key] ?? $translations['ar'][$key] ?? $key;

    $price = (float) ($product->price ?? 0);
    $regular = (float) ($product->regular_price ?? 0);
    $isSale = $regular > 0 && $price > 0 && $regular > $price;

    $image = $product->image ?: ($wpBaseUrl . '/wp-content/uploads/woocommerce-placeholder.png');
    $contentHtml = trim((string) ($product->post_excerpt ?: $product->post_content));
    $variationRules = $variationRules ?? [];
    $productAttributesForSelection = $productAttributesForSelection ?? [];
    $relatedProducts = $relatedProducts ?? collect();

    $addToCartBase = $wpBaseUrl . '/cart/';
    $wpLogo = 'https://styliiiish.com/wp-content/uploads/2025/11/ChatGPT-Image-Nov-2-2025-03_11_14-AM-e1762046066547.png';
    $wpIcon = 'https://styliiiish.com/wp-content/uploads/2025/11/cropped-ChatGPT-Image-Nov-2-2025-03_11_14-AM-e1762046066547.png';
@endphp
<html lang="{{ $isEnglish ? 'en' : 'ar' }}" dir="{{ $isEnglish ? 'ltr' : 'rtl' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ strip_tags((string) ($product->post_excerpt ?: $product->post_title)) }}">
    <link rel="canonical" href="{{ $wpBaseUrl }}{{ $canonicalPath }}">
    <link rel="alternate" hreflang="ar" href="{{ $wpBaseUrl }}/ar/item/{{ rawurlencode((string) ($product->post_name ?? '')) }}">
    <link rel="alternate" hreflang="en" href="{{ $wpBaseUrl }}/en/item/{{ rawurlencode((string) ($product->post_name ?? '')) }}">
    <link rel="alternate" hreflang="x-default" href="{{ $wpBaseUrl }}/ar/item/{{ rawurlencode((string) ($product->post_name ?? '')) }}">
    <title>{{ $t('page_title') }}</title>
    <style>
        :root {
            --wf-main-rgb: 213, 21, 34;
            --wf-main-color: rgb(var(--wf-main-rgb));
            --wf-secondary-color: #17273B;
            --bg: #f6f7fb;
            --card: #ffffff;
            --text: #17273B;
            --muted: #5a6678;
            --line: rgba(189, 189, 189, 0.4);
            --primary: var(--wf-main-color);
            --secondary: var(--wf-secondary-color);
        }

        * { box-sizing: border-box; }
        body { margin: 0; font-family: "Segoe UI", Tahoma, Arial, sans-serif; background: var(--bg); color: var(--text); }
        a { text-decoration: none; color: inherit; }
        .container { width: min(1180px, 92%); margin: 0 auto; }

        .topbar { background: var(--secondary); color: #fff; font-size: 13px; }
        .topbar .container { min-height: 40px; display: flex; align-items: center; justify-content: center; }

        .header { background: #fff; border-bottom: 1px solid var(--line); position: sticky; top: 0; z-index: 40; }
        .header-inner { min-height: 74px; display: grid; grid-template-columns: auto 1fr auto; align-items: center; gap: 12px; }
        .brand { display: flex; flex-direction: column; gap: 2px; }
        .brand-logo { height: 38px; width: auto; max-width: min(210px, 40vw); object-fit: contain; }
        .brand-sub { font-size: 11px; color: var(--muted); }
        .nav { display: flex; justify-content: center; gap: 8px; padding: 5px; border: 1px solid var(--line); border-radius: 12px; background: #f9fbff; }
        .nav a { padding: 8px 12px; border-radius: 8px; font-size: 14px; font-weight: 700; white-space: nowrap; }
        .nav a.active, .nav a:hover { color: var(--primary); background: #fff4f5; }
        .head-btn { border: 1px solid var(--line); border-radius: 10px; min-width: 38px; min-height: 38px; display: inline-flex; align-items: center; justify-content: center; background: #fff; }

        .product-wrap { padding: 22px 0; }
        .product-grid { display: grid; grid-template-columns: 1.05fr 1fr; gap: 18px; }
        .panel { background: #fff; border: 1px solid var(--line); border-radius: 16px; overflow: hidden; }
        .media { padding: 12px; }
        .media img { width: 100%; aspect-ratio: 3/4; object-fit: cover; border-radius: 12px; background: #f2f2f5; }
        .details { padding: 14px; }
        .title { margin: 0 0 8px; font-size: clamp(24px, 4vw, 34px); line-height: 1.3; color: var(--secondary); }
        .prices { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 12px; }
        .price { color: var(--primary); font-weight: 900; font-size: 22px; }
        .old { color: #8b8b97; text-decoration: line-through; font-size: 14px; }

        .section-title { margin: 0 0 10px; font-size: 20px; color: var(--secondary); }
        .detail-list { margin: 0; padding: 0; list-style: none; display: grid; gap: 8px; }
        .detail-list li { background: #fff; border: 1px solid var(--line); border-radius: 10px; padding: 10px 12px; }

        .delivery { margin-top: 12px; background: #fff; border: 1px solid var(--line); border-radius: 12px; padding: 12px; }
        .delivery p { margin: 0 0 8px; color: var(--muted); }
        .delivery ul { margin: 0; padding-inline-start: 18px; color: var(--secondary); }

        .selectors { margin-top: 14px; display: grid; gap: 10px; }
        .selector label { display: block; font-size: 13px; font-weight: 700; color: var(--secondary); margin-bottom: 6px; }
        .selector select, .qty-input {
            width: 100%; min-height: 44px; border: 1px solid var(--line); border-radius: 10px; background: #fff;
            padding: 0 10px; font-size: 14px; color: var(--secondary);
        }

        .cart-row { margin-top: 12px; display: grid; grid-template-columns: 120px 1fr; gap: 8px; }
        .btn-main {
            border: 0; border-radius: 10px; min-height: 44px; background: var(--primary); color: #fff;
            font-size: 14px; font-weight: 800; cursor: pointer;
        }
        .btn-main:disabled { opacity: .55; cursor: not-allowed; }
        .help-text { margin-top: 8px; font-size: 13px; color: var(--muted); min-height: 18px; }

        .guide-row { margin-top: 12px; display: flex; gap: 8px; flex-wrap: wrap; }
        .btn-ghost { border: 1px solid var(--line); border-radius: 10px; background: #fff; color: var(--secondary); padding: 10px 14px; font-size: 14px; font-weight: 700; cursor: pointer; }

        .description { margin-top: 14px; color: var(--muted); line-height: 1.7; }

        .related { margin-top: 18px; }
        .related-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; }
        .r-card { background: #fff; border: 1px solid var(--line); border-radius: 14px; overflow: hidden; display: flex; flex-direction: column; }
        .r-thumb { width: 100%; aspect-ratio: 3/4; object-fit: cover; background: #f2f2f5; }
        .r-body { padding: 10px; display: flex; flex-direction: column; gap: 8px; height: 100%; }
        .r-title { margin: 0; font-size: 14px; line-height: 1.4; min-height: 40px; }
        .r-price { color: var(--primary); font-weight: 900; }
        .r-actions { margin-top: auto; display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        .r-actions a { min-height: 38px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; }
        .r-buy { background: var(--primary); color: #fff; }
        .r-view { border: 1px solid var(--line); color: var(--secondary); background: #fff; }

        .sg-modal { position: fixed; inset: 0; z-index: 120; display: none; }
        .sg-modal.is-open { display: block; }
        .sg-backdrop { position: absolute; inset: 0; background: rgba(15, 26, 42, 0.66); }
        .sg-dialog {
            position: relative; z-index: 1; width: min(980px, 94vw); height: min(86vh, 820px); margin: 6vh auto 0;
            background: #fff; border-radius: 14px; border: 1px solid var(--line); overflow: hidden;
            display: grid; grid-template-rows: auto 1fr;
        }
        .sg-head { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 10px 12px; border-bottom: 1px solid var(--line); }
        .sg-title { margin: 0; font-size: 16px; color: var(--secondary); }
        .sg-close { border: 1px solid var(--line); border-radius: 8px; background: #fff; color: var(--secondary); padding: 6px 10px; font-size: 13px; font-weight: 700; cursor: pointer; }
        .sg-body { width: 100%; height: 100%; }
        .sg-frame { width: 100%; height: 100%; border: 0; }

        .site-footer { margin-top: 16px; background: #0f1a2a; color: #fff; border-top: 4px solid var(--primary); }
        .footer-grid { padding: 32px 0 20px; display: grid; grid-template-columns: 1.5fr 1fr 1fr 1.1fr; gap: 20px; }
        .footer-brand-logo { width: 156px; max-width: 100%; object-fit: contain; margin-bottom: 10px; display: block; }
        .footer-brand h4 { margin: 0 0 8px; font-size: 18px; }
        .footer-brand p { margin: 0 0 8px; color: #c8d3e2; font-size: 14px; line-height: 1.7; }
        .footer-contact-row { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px; }
        .footer-contact-row a { color: #fff; border: 1px solid rgba(255,255,255,.25); border-radius: 999px; padding: 6px 10px; font-size: 12px; }
        .footer-col h5 { margin: 0 0 10px; font-size: 15px; }
        .footer-links { list-style: none; margin: 0; padding: 0; display: grid; gap: 8px; }
        .footer-links a { color: #d7e0ed; font-size: 14px; }
        .footer-links a:hover { color: #fff; }
        .footer-bottom { border-top: 1px solid rgba(255,255,255,.18); padding: 12px 0; display: flex; justify-content: space-between; gap: 10px; color: #b8c5d8; font-size: 13px; flex-wrap: wrap; }
        .footer-bottom a { color: #fff; }

        @media (max-width: 980px) {
            .header-inner { grid-template-columns: 1fr; padding: 10px 0; }
            .brand, .nav { justify-content: center; text-align: center; }
            .product-grid { grid-template-columns: 1fr; }
            .related-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .footer-grid { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 640px) {
            .nav { overflow-x: auto; justify-content: flex-start; }
            .cart-row { grid-template-columns: 1fr; }
            .related-grid { grid-template-columns: 1fr; gap: 10px; }
            .r-actions { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="topbar"><div class="container">{{ $t('topbar') }}</div></div>

    <header class="header">
        <div class="container header-inner">
            <a class="brand" href="{{ $localePrefix }}">
                <img class="brand-logo" src="{{ $wpLogo }}" alt="Styliiiish" onerror="this.onerror=null;this.src='/brand/logo.png';">
                <span class="brand-sub">{{ $t('brand_sub') }}</span>
            </a>

            <nav class="nav" aria-label="Main Navigation">
                <a href="{{ $localePrefix }}">{{ $t('home') }}</a>
                <a class="active" href="{{ $localePrefix }}/shop">{{ $t('shop') }}</a>
                <a href="{{ $localePrefix }}/marketplace">{{ $t('marketplace') }}</a>
                <a href="{{ $wpBaseUrl }}/my-dresses/" target="_blank" rel="noopener">{{ $t('sell_dress') }}</a>
                <a href="{{ $wpBaseUrl }}/blog/" target="_blank" rel="noopener">{{ $t('blog') }}</a>
            </nav>

            <div style="display:flex; gap:8px; justify-content:center;">
                <a class="head-btn" href="{{ $wpBaseUrl }}/my-account/" target="_blank" rel="noopener" title="Account" aria-label="Account">👤</a>
                <a class="head-btn" href="{{ $wpBaseUrl }}/wishlist/" target="_blank" rel="noopener" title="Wishlist" aria-label="Wishlist">❤</a>
                <a class="head-btn" href="{{ $wpBaseUrl }}/cart/" target="_blank" rel="noopener" title="Cart" aria-label="Cart">🛒</a>
            </div>
        </div>
    </header>

    <main class="container product-wrap">
        <section class="product-grid">
            <article class="panel media">
                <img src="{{ $image }}" alt="{{ $product->post_title }}" loading="eager">
            </article>

            <article class="panel details">
                <h1 class="title">{{ $product->post_title }}</h1>

                <div class="prices">
                    <span class="price">
                        @if($price > 0)
                            {{ number_format($price) }} {{ $t('currency') }}
                        @else
                            {{ $t('contact_for_price') }}
                        @endif
                    </span>
                    @if($isSale)
                        <span class="old">{{ number_format($regular) }} {{ $t('currency') }}</span>
                    @endif
                </div>

                <h2 class="section-title">{{ $t('dress_details') }}</h2>
                <ul class="detail-list">
                    <li><strong>{{ $t('material') }}:</strong> {{ $material ?: $t('na') }}</li>
                    <li><strong>{{ $t('color') }}:</strong> {{ $color ?: $t('na') }}</li>
                    <li><strong>{{ $t('condition') }}:</strong> {{ $condition ?: $t('na') }}</li>
                    <li><strong>{{ $t('sizes') }}:</strong> {{ !empty($sizeValues) ? implode(', ', $sizeValues) : $t('na') }}</li>
                </ul>

                <div class="delivery">
                    <h3 class="section-title" style="font-size: 18px; margin-bottom: 8px;">{{ $t('delivery_title') }}</h3>
                    <p>{{ $deliveryIntro }}</p>
                    <ul>
                        <li>{{ $readyDelivery }}</li>
                        <li>{{ $customDelivery }}</li>
                    </ul>
                    <p style="margin-top: 8px;">{{ $t('delivery_note') }} <a href="{{ $localePrefix }}/shipping-delivery-policy" style="color: var(--primary); font-weight: 700;">{{ $t('shipping_policy') }}</a></p>
                </div>

                <form id="addToCartForm" method="GET" action="{{ $addToCartBase }}">
                    <input type="hidden" name="add-to-cart" value="{{ (int) $product->ID }}">
                    <input type="hidden" name="product_id" value="{{ (int) $product->ID }}">
                    <input type="hidden" name="variation_id" id="variationIdInput" value="0">

                    @if(!empty($productAttributesForSelection))
                        <div class="selectors" id="attributeSelectors" data-has-variations="{{ !empty($hasVariations) ? '1' : '0' }}">
                            @foreach($productAttributesForSelection as $attribute)
                                <div class="selector">
                                    <label for="attr_{{ $attribute['taxonomy'] }}">{{ $attribute['label'] }}</label>
                                    <select id="attr_{{ $attribute['taxonomy'] }}" name="{{ $attribute['taxonomy'] }}" data-attribute-key="{{ $attribute['taxonomy'] }}">
                                        <option value="">{{ str_replace(':label', $attribute['label'], $t('select_option')) }}</option>
                                        @foreach($attribute['options'] as $option)
                                            <option value="{{ $option['slug'] }}">{{ $option['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="attribute_{{ $attribute['taxonomy'] }}" id="posted_{{ $attribute['taxonomy'] }}" value="">
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="cart-row">
                        <input class="qty-input" type="number" min="1" step="1" value="1" name="quantity" aria-label="{{ $t('qty') }}">
                        <button class="btn-main" id="addToCartBtn" type="submit">{{ $t('add_to_cart') }}</button>
                    </div>
                    <div class="help-text" id="cartHelpText"></div>
                </form>

                <div class="guide-row">
                    <button type="button" class="btn-ghost" id="open-size-guide" data-size-guide-url="{{ $sizeGuideUrl }}">{{ $t('size_guide') }}</button>
                </div>

                @if($contentHtml !== '')
                    <section class="description">
                        <h3 class="section-title" style="font-size:18px;">{{ $t('description') }}</h3>
                        {!! $contentHtml !!}
                    </section>
                @endif
            </article>
        </section>

        @if(($relatedProducts instanceof \Illuminate\Support\Collection && $relatedProducts->isNotEmpty()) || (is_array($relatedProducts) && !empty($relatedProducts)))
            <section class="related">
                <h2 class="section-title">{{ $t('related') }}</h2>
                <div class="related-grid">
                    @foreach($relatedProducts as $related)
                        @php
                            $relatedPrice = (float) ($related->price ?? 0);
                            $relatedImage = $related->image ?: ($wpBaseUrl . '/wp-content/uploads/woocommerce-placeholder.png');
                        @endphp
                        <article class="r-card">
                            <img class="r-thumb" src="{{ $relatedImage }}" alt="{{ $related->post_title }}" loading="lazy">
                            <div class="r-body">
                                <h3 class="r-title">{{ $related->post_title }}</h3>
                                <div class="r-price">
                                    {{ $relatedPrice > 0 ? number_format($relatedPrice) . ' ' . $t('currency') : $t('contact_for_price') }}
                                </div>
                                <div class="r-actions">
                                    <a class="r-buy" href="{{ $localePrefix }}/item/{{ $related->post_name }}">{{ $t('buy_now') }}</a>
                                    <a class="r-view" href="{{ $localePrefix }}/item/{{ $related->post_name }}">{{ $t('view_product') }}</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    </main>

    <footer class="site-footer">
        <div class="container footer-grid">
            <div class="footer-brand">
                <img class="footer-brand-logo" src="{{ $wpLogo }}" alt="Styliiiish" onerror="this.onerror=null;this.src='/brand/logo.png';">
                <h4>ستيليش فاشون هاوس</h4>
                <p>{{ $t('shop_desc') }}</p>
                <p>11:00 AM - 7:00 PM</p>
                <div class="footer-contact-row">
                    <a href="{{ $localePrefix }}/contact-us">{{ $t('contact_us') }}</a>
                    <a href="tel:+201050874255">{{ $t('direct_call') }}</a>
                </div>
            </div>

            <div class="footer-col">
                <h5>{{ $t('quick_links') }}</h5>
                <ul class="footer-links">
                    <li><a href="{{ $localePrefix }}">{{ $t('home') }}</a></li>
                    <li><a href="{{ $localePrefix }}/shop">{{ $t('shop') }}</a></li>
                    <li><a href="{{ $localePrefix }}/blog">{{ $t('blog') }}</a></li>
                    <li><a href="{{ $localePrefix }}/about-us">About</a></li>
                    <li><a href="{{ $localePrefix }}/contact-us">{{ $t('contact_us') }}</a></li>
                    <li><a href="{{ $localePrefix }}/categories">Categories</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h5>{{ $t('official_info') }}</h5>
                <ul class="footer-links">
                    <li><a href="https://maps.app.goo.gl/MCdcFEcFoR4tEjpT8" target="_blank" rel="noopener">1 Nabil Khalil St, Nasr City, Cairo, Egypt</a></li>
                    <li><a href="tel:+201050874255">+2 010-5087-4255</a></li>
                    <li><a href="mailto:email@styliiiish.com">email@styliiiish.com</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h5>{{ $t('policies') }}</h5>
                <ul class="footer-links">
                    <li><a href="{{ $localePrefix }}/about-us">About</a></li>
                    <li><a href="{{ $localePrefix }}/privacy-policy">Privacy</a></li>
                    <li><a href="{{ $localePrefix }}/terms-conditions">Terms</a></li>
                    <li><a href="{{ $localePrefix }}/refund-return-policy">Refund</a></li>
                    <li><a href="{{ $localePrefix }}/faq">FAQ</a></li>
                    <li><a href="{{ $localePrefix }}/shipping-delivery-policy">{{ $t('shipping_policy') }}</a></li>
                    <li><a href="{{ $localePrefix }}/cookie-policy">Cookies</a></li>
                </ul>
            </div>
        </div>

        <div class="container footer-bottom">
            <span>{{ str_replace(':year', (string) date('Y'), $t('all_rights')) }} <a href="https://websiteflexi.com/" target="_blank" rel="noopener">Website Flexi</a></span>
            <span><a href="https://styliiiish.com/" target="_blank" rel="noopener">styliiiish.com</a></span>
        </div>
    </footer>

    <div class="sg-modal" id="size-guide-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-label="{{ $t('size_guide_open') }}">
        <div class="sg-backdrop" data-close-size-guide></div>
        <div class="sg-dialog">
            <div class="sg-head">
                <h3 class="sg-title">{{ $t('size_guide') }}</h3>
                <button type="button" class="sg-close" data-close-size-guide>{{ $t('close') }}</button>
            </div>
            <div class="sg-body">
                <iframe class="sg-frame" id="size-guide-frame" src="about:blank" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const variationRules = @json($variationRules);
            const hasVariations = @json((bool) ($hasVariations ?? false));
            const selectorsWrap = document.getElementById('attributeSelectors');
            const addToCartBtn = document.getElementById('addToCartBtn');
            const variationIdInput = document.getElementById('variationIdInput');
            const helpText = document.getElementById('cartHelpText');
            const selectNodes = selectorsWrap ? Array.from(selectorsWrap.querySelectorAll('select[data-attribute-key]')) : [];

            const chooseOptionsText = @json($t('choose_options_first'));
            const outOfStockText = @json($t('out_of_stock'));

            const syncPostedAttributes = () => {
                selectNodes.forEach((selectNode) => {
                    const key = selectNode.getAttribute('data-attribute-key');
                    const hidden = document.getElementById('posted_' + key);
                    if (hidden) hidden.value = selectNode.value || '';
                });
            };

            const getSelected = () => {
                const values = {};
                selectNodes.forEach((selectNode) => {
                    const key = selectNode.getAttribute('data-attribute-key');
                    values[key] = (selectNode.value || '').trim();
                });
                return values;
            };

            const validateVariation = () => {
                syncPostedAttributes();

                if (!hasVariations) {
                    addToCartBtn.disabled = false;
                    variationIdInput.value = '0';
                    helpText.textContent = '';
                    return;
                }

                const selected = getSelected();
                const hasEmpty = Object.values(selected).some((value) => value === '');
                if (hasEmpty) {
                    addToCartBtn.disabled = true;
                    variationIdInput.value = '0';
                    helpText.textContent = chooseOptionsText;
                    return;
                }

                const matched = variationRules.find((rule) => {
                    const attrs = rule.attributes || {};
                    return Object.keys(selected).every((key) => (attrs[key] || '') === selected[key]);
                });

                if (!matched) {
                    addToCartBtn.disabled = true;
                    variationIdInput.value = '0';
                    helpText.textContent = chooseOptionsText;
                    return;
                }

                if ((matched.stock_status || 'instock') !== 'instock') {
                    addToCartBtn.disabled = true;
                    variationIdInput.value = String(matched.variation_id || 0);
                    helpText.textContent = outOfStockText;
                    return;
                }

                variationIdInput.value = String(matched.variation_id || 0);
                addToCartBtn.disabled = false;
                helpText.textContent = '';
            };

            if (selectNodes.length > 0) {
                selectNodes.forEach((node) => node.addEventListener('change', validateVariation));
                validateVariation();
            } else {
                addToCartBtn.disabled = false;
            }

            const trigger = document.getElementById('open-size-guide');
            const modal = document.getElementById('size-guide-modal');
            const frame = document.getElementById('size-guide-frame');
            if (!trigger || !modal || !frame) return;

            const guideUrl = (trigger.getAttribute('data-size-guide-url') || '').trim();
            const closeNodes = modal.querySelectorAll('[data-close-size-guide]');

            const openModal = () => {
                if (!guideUrl) return;
                frame.src = guideUrl;
                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            };

            const closeModal = () => {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
                frame.src = 'about:blank';
            };

            trigger.addEventListener('click', openModal);
            closeNodes.forEach((node) => node.addEventListener('click', closeModal));

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                    closeModal();
                }
            });
        })();
    </script>
</body>
</html>
