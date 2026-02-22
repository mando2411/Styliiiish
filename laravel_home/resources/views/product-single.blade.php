<!DOCTYPE html>
@php
    $currentLocale = $currentLocale ?? 'ar';
    $localePrefix = $localePrefix ?? '/ar';
    $isEnglish = $currentLocale === 'en';
    $wpBaseUrl = rtrim((string) ($wpBaseUrl ?? env('WP_PUBLIC_URL', request()->getSchemeAndHttpHost())), '/');
    $canonicalPath = $localePrefix . '/product/' . rawurlencode((string) ($product->post_name ?? ''));

    $translations = [
        'ar' => [
            'page_title' => (($product->post_title ?? 'المنتج') . ' | ستايليش'),
            'back_shop' => 'العودة للمتجر',
            'buy_now' => 'إتمام الطلب',
            'view_on_wp' => 'عرض على ووردبريس',
            'dress_details' => 'تفاصيل الفستان ✨',
            'material' => 'الخامة',
            'color' => 'اللون',
            'condition' => 'الحالة المتاحة',
            'sizes' => 'المقاسات المتاحة',
            'delivery_title' => 'وقت التوصيل 🚚',
            'delivery_note' => 'لجميع تفاصيل الشحن راجعي سياسة الشحن والتوصيل.',
            'shipping_policy' => 'سياسة الشحن والتوصيل',
            'size_guide' => '📏 دليل المقاسات',
            'description' => 'وصف المنتج',
            'na' => 'غير محدد',
            'contact_for_price' => 'تواصل لمعرفة السعر',
            'currency' => 'ج.م',
        ],
        'en' => [
            'page_title' => (($product->post_title ?? 'Product') . ' | Styliiiish'),
            'back_shop' => 'Back to Shop',
            'buy_now' => 'Order Now',
            'view_on_wp' => 'View on WordPress',
            'dress_details' => 'Dress Details ✨',
            'material' => 'Material',
            'color' => 'Color',
            'condition' => 'Available Conditions',
            'sizes' => 'Available Sizes',
            'delivery_title' => 'Delivery Time 🚚',
            'delivery_note' => 'For full shipping details, please review our Shipping & Delivery Policy.',
            'shipping_policy' => 'Shipping & Delivery Policy',
            'size_guide' => '📏 Size Guide',
            'description' => 'Product Description',
            'na' => 'N/A',
            'contact_for_price' => 'Contact for Price',
            'currency' => 'EGP',
        ],
    ];

    $t = fn (string $key) => $translations[$currentLocale][$key] ?? $translations['ar'][$key] ?? $key;

    $price = (float) ($product->price ?? 0);
    $regular = (float) ($product->regular_price ?? 0);
    $isSale = $regular > 0 && $price > 0 && $regular > $price;

    $image = $product->image ?: ($wpBaseUrl . '/wp-content/uploads/woocommerce-placeholder.png');
    $shopUrl = $localePrefix . '/shop';
    $wpProductUrl = $wpBaseUrl . '/product/' . rawurlencode((string) ($product->post_name ?? '')) . '/';
    $contentHtml = trim((string) ($product->post_excerpt ?: $product->post_content));
@endphp
<html lang="{{ $isEnglish ? 'en' : 'ar' }}" dir="{{ $isEnglish ? 'ltr' : 'rtl' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ strip_tags((string) ($product->post_excerpt ?: $product->post_title)) }}">
    <link rel="canonical" href="{{ $wpBaseUrl }}{{ $canonicalPath }}">
    <link rel="alternate" hreflang="ar" href="{{ $wpBaseUrl }}/ar/product/{{ rawurlencode((string) ($product->post_name ?? '')) }}">
    <link rel="alternate" hreflang="en" href="{{ $wpBaseUrl }}/en/product/{{ rawurlencode((string) ($product->post_name ?? '')) }}">
    <link rel="alternate" hreflang="x-default" href="{{ $wpBaseUrl }}/ar/product/{{ rawurlencode((string) ($product->post_name ?? '')) }}">
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
        a { color: inherit; text-decoration: none; }
        .container { width: min(1120px, 92%); margin: 0 auto; }

        .top { padding: 18px 0; }
        .top a { font-size: 14px; font-weight: 700; color: var(--secondary); }

        .product-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; align-items: start; }
        .card { background: var(--card); border: 1px solid var(--line); border-radius: 16px; padding: 16px; }
        .media { width: 100%; border-radius: 14px; overflow: hidden; background: #fff; border: 1px solid var(--line); }
        .media img { width: 100%; height: auto; display: block; object-fit: cover; }

        .title { margin: 0 0 8px; font-size: 28px; line-height: 1.3; color: var(--secondary); }
        .prices { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 14px; }
        .price { font-size: 24px; font-weight: 900; color: var(--primary); }
        .old { text-decoration: line-through; color: #8b8b97; }

        .actions { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 16px; }
        .btn { border-radius: 10px; padding: 10px 14px; font-size: 14px; font-weight: 800; display: inline-flex; align-items: center; justify-content: center; }
        .btn-main { background: var(--primary); color: #fff; }
        .btn-ghost { border: 1px solid var(--line); background: #fff; color: var(--secondary); }

        .section-title { margin: 0 0 10px; font-size: 20px; color: var(--secondary); }
        .detail-list { margin: 0; padding: 0; list-style: none; display: grid; gap: 8px; }
        .detail-list li { background: #fff; border: 1px solid var(--line); border-radius: 10px; padding: 10px 12px; }
        .detail-list strong { color: var(--secondary); }

        .delivery { margin-top: 14px; background: #fff; border: 1px solid var(--line); border-radius: 12px; padding: 12px; }
        .delivery p { margin: 0 0 8px; color: var(--muted); }
        .delivery ul { margin: 0; padding-inline-start: 18px; color: var(--secondary); }

        .guide-row { margin-top: 12px; display: flex; gap: 10px; flex-wrap: wrap; }
        .description { margin-top: 14px; color: var(--muted); line-height: 1.7; }

        @media (max-width: 920px) {
            .product-layout { grid-template-columns: 1fr; }
            .title { font-size: 24px; }
        }
    </style>
</head>
<body>
    <div class="top">
        <div class="container">
            <a href="{{ $shopUrl }}">← {{ $t('back_shop') }}</a>
        </div>
    </div>

    <main class="container" style="padding-bottom: 24px;">
        <section class="product-layout">
            <article class="card">
                <div class="media">
                    <img src="{{ $image }}" alt="{{ $product->post_title }}" loading="eager">
                </div>
            </article>

            <article class="card">
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

                <div class="actions">
                    <a class="btn btn-main" href="{{ $wpProductUrl }}">{{ $t('buy_now') }}</a>
                    <a class="btn btn-ghost" href="{{ $wpProductUrl }}" target="_blank" rel="noopener">{{ $t('view_on_wp') }}</a>
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

                <div class="guide-row">
                    <a class="btn btn-ghost" href="{{ $sizeGuideUrl }}">{{ $t('size_guide') }}</a>
                </div>

                @if($contentHtml !== '')
                    <section class="description">
                        <h3 class="section-title" style="font-size:18px;">{{ $t('description') }}</h3>
                        {!! $contentHtml !!}
                    </section>
                @endif
            </article>
        </section>
    </main>
</body>
</html>
