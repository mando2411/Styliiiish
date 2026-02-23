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
            'add_to_cart' => 'أضيفي إلى العربة',
            'choose_options_first' => 'اختاري المقاس/اللون أولاً',
            'out_of_stock' => 'هذا الاختيار غير متاح حالياً',
            'related' => 'منتجات مشابهة',
            'quick_links' => 'روابط سريعة',
            'official_info' => 'معلومات رسمية',
            'policies' => 'سياسات وقوانين',
            'contact_us' => 'تواصلي معنا',
            'direct_call' => 'اتصال مباشر',
            'account' => 'حسابي',
            'wishlist' => 'المفضلة',
            'cart' => 'العربة',
            'about' => 'من نحن',
            'categories' => 'الأقسام',
            'privacy' => 'الخصوصية',
            'terms' => 'الشروط والأحكام',
            'refund' => 'الاسترجاع',
            'faq' => 'الأسئلة الشائعة',
            'cookies' => 'الكوكيز',
            'subtotal' => 'الإجمالي الفرعي',
            'qty_short' => 'الكمية',
            'loading_cart' => 'جاري تحميل العربة…',
            'size_col' => 'المقاس',
            'weight_col' => 'وزن الجسم (كجم)',
            'bust_col' => 'الصدر (سم)',
            'waist_col' => 'الخصر (سم)',
            'hips_col' => 'الأرداف (سم)',
            'size_guide_note' => 'المقاسات مبنية على قياسات الجسم. إذا كنتِ بين مقاسين، اختاري المقاس الأكبر.',
            'image_with_number' => 'صورة :number',
            'gallery_thumbs_label' => 'صور المنتج',
            'all_rights' => 'جميع الحقوق محفوظة © :year Styliiiish | تشغيل وتطوير',
            'view_product' => 'معاينة',
            'buy_now' => 'اطلبي الآن',
            'shop_desc' => 'اكتشفي أحدث فساتين السهرة والزفاف والخطوبة بأسعار تنافسية.',
            'cart_title' => 'سلة التسوق',
            'cart_empty' => 'العربة فارغة حاليًا.',
            'view_cart' => 'عرض العربة',
            'checkout' => 'إتمام الشراء',
            'remove' => 'حذف',
            'added_to_cart' => 'تمت إضافة المنتج للعربة',
            'add_to_cart_failed' => 'تعذر إضافة المنتج للعربة',
            'size_guide_missing' => 'دليل المقاسات غير متاح لهذا المنتج حالياً.',
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
            'account' => 'Account',
            'wishlist' => 'Wishlist',
            'cart' => 'Cart',
            'about' => 'About',
            'categories' => 'Categories',
            'privacy' => 'Privacy',
            'terms' => 'Terms',
            'refund' => 'Refund',
            'faq' => 'FAQ',
            'cookies' => 'Cookies',
            'subtotal' => 'Subtotal',
            'qty_short' => 'Qty',
            'loading_cart' => 'Loading cart…',
            'size_col' => 'Size',
            'weight_col' => 'Body Weight (kg)',
            'bust_col' => 'Bust (cm)',
            'waist_col' => 'Waist (cm)',
            'hips_col' => 'Hips (cm)',
            'size_guide_note' => 'Sizes are based on body measurements. If you are between two sizes, choose the larger size.',
            'image_with_number' => 'Image :number',
            'gallery_thumbs_label' => 'Product gallery thumbnails',
            'all_rights' => 'All rights reserved © :year Styliiiish | Powered by',
            'view_product' => 'View',
            'buy_now' => 'Order Now',
            'shop_desc' => 'Discover latest evening, bridal, and engagement dresses at competitive prices.',
            'cart_title' => 'Shopping Cart',
            'cart_empty' => 'Your cart is currently empty.',
            'view_cart' => 'View Cart',
            'checkout' => 'Checkout',
            'remove' => 'Remove',
            'added_to_cart' => 'Product added to cart',
            'add_to_cart_failed' => 'Unable to add product to cart',
            'size_guide_missing' => 'Size guide is not available for this product yet.',
        ],
    ];

    $t = fn (string $key) => $translations[$currentLocale][$key] ?? $translations['ar'][$key] ?? $key;

    $price = (float) ($product->price ?? 0);
    $regular = (float) ($product->regular_price ?? 0);
    $isSale = $regular > 0 && $price > 0 && $regular > $price;

    $placeholderImage = $wpBaseUrl . '/wp-content/uploads/woocommerce-placeholder.png';
    $image = $product->image ?: $placeholderImage;
    if (str_ends_with(strtolower((string) $image), '.heic')) {
        $image = $placeholderImage;
    }

    $galleryImages = collect($galleryImages ?? [])
        ->map(function ($url) use ($wpBaseUrl, $placeholderImage) {
            $value = trim((string) $url);
            if ($value === '') {
                return null;
            }

            $normalized = str_replace(
                ['https://l.styliiiish.com', 'http://l.styliiiish.com', '//l.styliiiish.com'],
                [$wpBaseUrl, $wpBaseUrl, $wpBaseUrl],
                $value
            );

            if (str_ends_with(strtolower($normalized), '.heic')) {
                $normalized = $placeholderImage;
            }

            return $normalized;
        })
        ->filter(fn ($url) => is_string($url) && $url !== '')
        ->prepend($image)
        ->unique()
        ->values();

    if ($galleryImages->isEmpty()) {
        $galleryImages = collect([$image]);
    }

    $mainImage = (string) $galleryImages->first();

    $contentHtml = trim((string) ($product->post_excerpt ?: $product->post_content));
    if ($contentHtml !== '') {
        $contentHtml = str_replace(
            ['https://l.styliiiish.com', 'http://l.styliiiish.com', '//l.styliiiish.com'],
            [$wpBaseUrl, $wpBaseUrl, $wpBaseUrl],
            $contentHtml
        );
    }
    $variationRules = $variationRules ?? [];
    $productAttributesForSelection = $productAttributesForSelection ?? [];
    $relatedProducts = $relatedProducts ?? collect();

    $normalizeColorKey = function (string $value): string {
        return trim(mb_strtolower(str_replace(['_', '-'], ' ', $value)));
    };

    $isColorAttribute = function (array $attribute) use ($normalizeColorKey): bool {
        $taxonomy = $normalizeColorKey((string) ($attribute['taxonomy'] ?? ''));
        $label = $normalizeColorKey((string) ($attribute['label'] ?? ''));

        return str_contains($taxonomy, 'color')
            || str_contains($taxonomy, 'colour')
            || str_contains($label, 'color')
            || str_contains($label, 'colour')
            || str_contains($label, 'لون');
    };

    $colorSwatchMap = [
        'black' => '#17273B', 'أسود' => '#17273B',
        'white' => '#FFFFFF', 'أبيض' => '#FFFFFF',
        'red' => '#D51522', 'أحمر' => '#D51522',
        'blue' => '#2563EB', 'أزرق' => '#2563EB',
        'green' => '#16A34A', 'أخضر' => '#16A34A',
        'olive' => '#6B7A3A', 'olive green' => '#6B7A3A', 'زيتي' => '#6B7A3A',
        'pink' => '#EC4899', 'وردي' => '#EC4899',
        'gold' => '#D4AF37', 'ذهبي' => '#D4AF37',
        'silver' => '#9CA3AF', 'فضي' => '#9CA3AF',
        'ivory' => '#F8F4E8', 'عاجي' => '#F8F4E8',
        'nude' => '#C8A98E', 'نيود' => '#C8A98E',
        'cream' => '#F5E6C8', 'كريمي' => '#F5E6C8',
        'purple' => '#7C3AED', 'بنفسجي' => '#7C3AED',
        'gray' => '#6B7280', 'grey' => '#6B7280', 'رمادي' => '#6B7280',
        'brown' => '#8B5E3C', 'بني' => '#8B5E3C',
        'beige' => '#D6C3A5', 'بيج' => '#D6C3A5',
    ];

    $resolveColorSwatch = function (string $slug, string $name) use ($normalizeColorKey, $colorSwatchMap): string {
        $slugKey = $normalizeColorKey($slug);
        $nameKey = $normalizeColorKey($name);

        if ($slugKey !== '' && isset($colorSwatchMap[$slugKey])) {
            return (string) $colorSwatchMap[$slugKey];
        }

        if ($nameKey !== '' && isset($colorSwatchMap[$nameKey])) {
            return (string) $colorSwatchMap[$nameKey];
        }

        return '#D9DCE3';
    };

    $addToCartBase = $wpBaseUrl . '/cart/';
    $buildMarker = 'PRODUCT_SINGLE_BUILD_2026-02-23_01';
    $wpLogo = 'https://styliiiish.com/wp-content/uploads/2025/11/ChatGPT-Image-Nov-2-2025-03_11_14-AM-e1762046066547.png';
    $wpIcon = 'https://styliiiish.com/wp-content/uploads/2025/11/cropped-ChatGPT-Image-Nov-2-2025-03_11_14-AM-e1762046066547.png';
@endphp
<html lang="{{ $isEnglish ? 'en' : 'ar' }}" dir="{{ $isEnglish ? 'ltr' : 'rtl' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
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
        .cart-trigger-wrap { position: relative; }
        .cart-trigger { position: relative; }
        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            min-width: 18px;
            height: 18px;
            border-radius: 999px;
            background: var(--primary);
            color: #fff;
            font-size: 11px;
            line-height: 18px;
            text-align: center;
            font-weight: 800;
            padding: 0 4px;
        }
        .cart-plus-one {
            position: absolute;
            top: -24px;
            right: -4px;
            font-size: 12px;
            font-weight: 900;
            color: var(--primary);
            opacity: 0;
            transform: translateY(0);
            pointer-events: none;
        }
        .cart-plus-one.show {
            animation: cartPlusOne .8s ease;
        }
        @keyframes cartPlusOne {
            0% { opacity: 0; transform: translateY(8px); }
            20% { opacity: 1; transform: translateY(0); }
            100% { opacity: 0; transform: translateY(-12px); }
        }

        .product-wrap { padding: 22px 0; }
        .product-grid { display: grid; grid-template-columns: 1.05fr 1fr; gap: 18px; }
        .panel { background: #fff; border: 1px solid var(--line); border-radius: 16px; overflow: hidden; }
        .media { padding: 12px; }
        .media-main {
            width: 100%;
            aspect-ratio: 3/4;
            object-fit: cover;
            border-radius: 12px;
            background: #f2f2f5;
            display: block;
        }
        .media-thumbs {
            margin-top: 10px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(72px, 1fr));
            gap: 8px;
        }
        .media-thumb {
            width: 100%;
            aspect-ratio: 3/4;
            border-radius: 10px;
            border: 2px solid transparent;
            background: #fff;
            padding: 0;
            cursor: pointer;
            overflow: hidden;
        }
        .media-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .media-thumb.is-active {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(var(--wf-main-rgb), 0.15);
        }
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

        .selectors { margin-top: 14px; display: grid; gap: 12px; }
        .selector {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 10px;
        }
        .selector label { display: block; font-size: 13px; font-weight: 800; color: var(--secondary); margin-bottom: 8px; }
        .selector-single {
            min-height: 42px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: var(--bg);
            display: inline-flex;
            align-items: center;
            padding: 0 12px;
            font-size: 13px;
            font-weight: 700;
            color: var(--secondary);
        }
        .attr-options { display: flex; flex-wrap: wrap; gap: 8px; }
        .attr-option-btn {
            border: 1px solid var(--line);
            border-radius: 999px;
            background: #fff;
            color: var(--secondary);
            min-height: 38px;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: .16s ease;
        }
        .attr-option-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }
        .attr-option-btn.is-active {
            border-color: var(--primary);
            background: rgba(var(--wf-main-rgb), 0.08);
            color: var(--primary);
        }
        .attr-options.colors { gap: 10px; }
        .attr-option-btn.color {
            border-radius: 10px;
            min-height: 44px;
            padding: 6px 10px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .swatch-dot {
            width: 22px;
            height: 22px;
            border-radius: 999px;
            border: 1px solid rgba(23, 39, 59, 0.18);
            background: var(--swatch-color, #D9DCE3);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.45);
        }
        .attr-option-btn.color.is-active .swatch-dot {
            box-shadow:
                0 0 0 2px #fff,
                0 0 0 4px rgba(var(--wf-main-rgb), 0.35);
        }
        .qty-input {
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

        .sg-modal { position: fixed; inset: 0; z-index: 120; display: none; align-items: center; justify-content: center; padding: 20px; }
        .sg-modal.is-open { display: flex; }
        .sg-backdrop { position: absolute; inset: 0; background: rgba(15, 26, 42, 0.66); }
        .sg-dialog {
            position: relative; z-index: 1; width: min(980px, 94vw); margin: 0;
            background: #fff; border-radius: 14px; border: 1px solid var(--line); overflow: hidden;
            display: flex; flex-direction: column;
            max-height: 86vh;
        }
        .sg-head { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 10px 12px; border-bottom: 1px solid var(--line); }
        .sg-title { margin: 0; font-size: 16px; color: var(--secondary); }
        .sg-close { border: 1px solid var(--line); border-radius: 8px; background: #fff; color: var(--secondary); padding: 6px 10px; font-size: 13px; font-weight: 700; cursor: pointer; }
        .sg-body { width: 100%; height: auto; }
        .sg-table-wrap { overflow: auto; padding: 12px; max-height: calc(86vh - 64px); }
        .sg-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 10px;
            overflow: hidden;
        }
        .sg-table th,
        .sg-table td {
            padding: 10px 12px;
            border-bottom: 1px solid var(--line);
            font-size: 14px;
            text-align: center;
            white-space: nowrap;
        }
        .sg-table th {
            background: #f8fafc;
            color: var(--secondary);
            font-weight: 800;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        .sg-table tr:last-child td { border-bottom: 0; }
        .sg-table tr.sg-active-size td {
            background: #fff4f5;
            color: var(--secondary);
            font-weight: 800;
        }
        .sg-note {
            margin: 10px 0 0;
            color: var(--muted);
            font-size: 13px;
            text-align: center;
        }

        .mini-cart {
            position: fixed;
            inset: 0;
            z-index: 130;
            opacity: 0;
            pointer-events: none;
            transition: opacity .22s ease;
        }
        .mini-cart.is-open {
            opacity: 1;
            pointer-events: auto;
        }
        .mini-cart-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(15,26,42,.55);
            opacity: 0;
            transition: opacity .22s ease;
        }
        .mini-cart.is-open .mini-cart-backdrop { opacity: 1; }
        .mini-cart-panel {
            position: absolute;
            top: 0;
            right: 0;
            width: min(420px, 92vw);
            height: 100%;
            background: #fff;
            border-inline-start: 1px solid var(--line);
            box-shadow: 0 12px 30px rgba(0,0,0,.2);
            display: grid;
            grid-template-rows: auto 1fr auto;
            transform: translateX(100%);
            transition: transform .26s ease;
        }
        .mini-cart.is-open .mini-cart-panel { transform: translateX(0); }
        [dir="rtl"] .mini-cart-panel { right: auto; left: 0; border-inline-start: 0; border-inline-end: 1px solid var(--line); }
        [dir="rtl"] .mini-cart-panel { transform: translateX(-100%); }
        [dir="rtl"] .mini-cart.is-open .mini-cart-panel { transform: translateX(0); }
        .mini-cart-head { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 12px; border-bottom: 1px solid var(--line); }
        .mini-cart-head h3 { margin: 0; font-size: 17px; color: var(--secondary); }
        .mini-cart-close { border: 1px solid var(--line); border-radius: 8px; background: #fff; color: var(--secondary); padding: 6px 10px; cursor: pointer; }
        .mini-cart-list { overflow: auto; padding: 12px; display: grid; gap: 10px; align-content: start; grid-auto-rows: max-content; }
        .mini-cart-item {
            display: grid;
            grid-template-columns: 70px 1fr auto;
            gap: 10px;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 8px;
            background: #fff;
            box-shadow: 0 8px 18px rgba(23,39,59,.05);
        }
        .mini-cart-item img { width: 70px; height: 92px; object-fit: cover; border-radius: 9px; background: #f2f2f5; }
        .mini-cart-info { min-width: 0; }
        .mini-cart-item h4 {
            margin: 0 0 4px;
            font-size: 13px;
            line-height: 1.45;
            color: var(--secondary);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .mini-cart-meta { font-size: 12px; color: var(--muted); display: flex; gap: 6px; align-items: center; }
        .mini-cart-price { font-size: 12px; color: var(--primary); font-weight: 800; margin-top: 4px; }
        .mini-cart-remove {
            border: 1px solid rgba(213,21,34,.24);
            background: #fff7f8;
            color: var(--primary);
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
            padding: 6px 8px;
            border-radius: 8px;
            align-self: start;
        }
        .mini-cart-remove:hover { background: #ffeff1; }
        .mini-cart-empty { color: var(--muted); font-size: 14px; padding: 8px 0; text-align: center; }
        .mini-cart-loading {
            color: var(--muted);
            font-size: 13px;
            border: 1px dashed var(--line);
            border-radius: 12px;
            padding: 12px;
            text-align: center;
            background: #fbfcff;
        }
        .mini-cart-foot { border-top: 1px solid var(--line); padding: 12px; display: grid; gap: 8px; }
        .mini-cart-subtotal { font-size: 13px; color: var(--secondary); display: flex; justify-content: space-between; }
        .mini-cart-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        .mini-cart-actions a { min-height: 40px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 800; }
        .mini-cart-view { border: 1px solid var(--line); background: #fff; color: var(--secondary); }
        .mini-cart-checkout { background: var(--primary); color: #fff; }

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
            .sg-table th,
            .sg-table td { font-size: 12px; padding: 8px; }
            .media-thumbs { grid-template-columns: repeat(5, minmax(0, 1fr)); }
        }
    </style>
</head>
<body>
    <!-- {{ $buildMarker }} -->
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
                <a class="head-btn" href="{{ $wpBaseUrl }}/my-account/" target="_blank" rel="noopener" title="{{ $t('account') }}" aria-label="{{ $t('account') }}">👤</a>
                <a class="head-btn" href="{{ $wpBaseUrl }}/wishlist/" target="_blank" rel="noopener" title="{{ $t('wishlist') }}" aria-label="{{ $t('wishlist') }}">❤</a>
                <span class="cart-trigger-wrap">
                    <button class="head-btn cart-trigger" type="button" id="miniCartTrigger" title="{{ $t('cart') }}" aria-label="{{ $t('cart') }}">🛒
                        <span class="cart-count" id="cartCountBadge">0</span>
                    </button>
                    <span class="cart-plus-one" id="cartPlusOne">+1</span>
                </span>
            </div>
        </div>
    </header>

    <main class="container product-wrap">
        <section class="product-grid">
            <article class="panel media">
                <img id="mainProductImage" class="media-main" src="{{ $mainImage }}" alt="{{ $product->post_title }}" loading="eager" onerror="this.onerror=null;this.src='{{ $placeholderImage }}';">
                @if($galleryImages->count() > 1)
                    <div class="media-thumbs" id="productMediaThumbs" aria-label="{{ $t('gallery_thumbs_label') }}">
                        @foreach($galleryImages as $galleryIndex => $galleryImage)
                            <button type="button" class="media-thumb{{ $galleryIndex === 0 ? ' is-active' : '' }}" data-gallery-src="{{ $galleryImage }}" aria-label="{{ str_replace(':number', (string) ($galleryIndex + 1), $t('image_with_number')) }}">
                                <img src="{{ $galleryImage }}" alt="{{ $product->post_title }} - {{ $galleryIndex + 1 }}" loading="lazy" onerror="this.onerror=null;this.src='{{ $placeholderImage }}';">
                            </button>
                        @endforeach
                    </div>
                @endif
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
                                @php
                                    $options = collect($attribute['options'] ?? [])->values();
                                    $optionCount = $options->count();
                                    $isColorGroup = $isColorAttribute($attribute);
                                    $taxonomyKey = (string) ($attribute['taxonomy'] ?? '');
                                    $singleOption = $optionCount === 1 ? $options->first() : null;
                                @endphp
                                <div class="selector">
                                    <label>{{ $attribute['label'] }}</label>

                                    @if($optionCount <= 1)
                                        <div class="selector-single">
                                            {{ (string) ($singleOption['name'] ?? $t('na')) }}
                                        </div>
                                        <input type="hidden" data-attribute-key="{{ $taxonomyKey }}" value="{{ (string) ($singleOption['slug'] ?? '') }}">
                                        <input type="hidden" name="attribute_{{ $taxonomyKey }}" id="posted_{{ $taxonomyKey }}" value="{{ (string) ($singleOption['slug'] ?? '') }}">
                                    @else
                                        <div class="attr-options {{ $isColorGroup ? 'colors' : '' }}" role="group" aria-label="{{ $attribute['label'] }}">
                                            @foreach($options as $option)
                                                @php
                                                    $optionSlug = (string) ($option['slug'] ?? '');
                                                    $optionName = (string) ($option['name'] ?? '');
                                                    $swatchColor = $resolveColorSwatch($optionSlug, $optionName);
                                                @endphp
                                                <button
                                                    type="button"
                                                    class="attr-option-btn {{ $isColorGroup ? 'color' : '' }}"
                                                    data-attr-option
                                                    data-attribute-key="{{ $taxonomyKey }}"
                                                    data-option-value="{{ $optionSlug }}"
                                                    aria-pressed="false"
                                                >
                                                    @if($isColorGroup)
                                                        <span class="swatch-dot" style="--swatch-color: {{ $swatchColor }}"></span>
                                                    @endif
                                                    <span>{{ $optionName }}</span>
                                                </button>
                                            @endforeach
                                        </div>
                                        <input type="hidden" data-attribute-key="{{ $taxonomyKey }}" id="attr_{{ $taxonomyKey }}" value="">
                                        <input type="hidden" name="attribute_{{ $taxonomyKey }}" id="posted_{{ $taxonomyKey }}" value="">
                                    @endif
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
                    <button type="button" class="btn-ghost" id="open-size-guide">{{ $t('size_guide') }}</button>
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
                    <li><a href="{{ $localePrefix }}/about-us">{{ $t('about') }}</a></li>
                    <li><a href="{{ $localePrefix }}/contact-us">{{ $t('contact_us') }}</a></li>
                    <li><a href="{{ $localePrefix }}/categories">{{ $t('categories') }}</a></li>
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
                    <li><a href="{{ $localePrefix }}/about-us">{{ $t('about') }}</a></li>
                    <li><a href="{{ $localePrefix }}/privacy-policy">{{ $t('privacy') }}</a></li>
                    <li><a href="{{ $localePrefix }}/terms-conditions">{{ $t('terms') }}</a></li>
                    <li><a href="{{ $localePrefix }}/refund-return-policy">{{ $t('refund') }}</a></li>
                    <li><a href="{{ $localePrefix }}/faq">{{ $t('faq') }}</a></li>
                    <li><a href="{{ $localePrefix }}/shipping-delivery-policy">{{ $t('shipping_policy') }}</a></li>
                    <li><a href="{{ $localePrefix }}/cookie-policy">{{ $t('cookies') }}</a></li>
                </ul>
            </div>
        </div>

        <div class="container footer-bottom">
            <span>{{ str_replace(':year', (string) date('Y'), $t('all_rights')) }} <a href="https://websiteflexi.com/" target="_blank" rel="noopener">Website Flexi</a></span>
            <span><a href="https://styliiiish.com/" target="_blank" rel="noopener">styliiiish.com</a></span>
        </div>
    </footer>

    <div class="mini-cart" id="miniCart" aria-hidden="true">
        <div class="mini-cart-backdrop" data-close-mini-cart></div>
        <aside class="mini-cart-panel" role="dialog" aria-modal="true" aria-label="{{ $t('cart_title') }}">
            <div class="mini-cart-head">
                <h3>{{ $t('cart_title') }}</h3>
                <button class="mini-cart-close" type="button" data-close-mini-cart>{{ $t('close') }}</button>
            </div>
            <div class="mini-cart-list" id="miniCartList"></div>
            <div class="mini-cart-foot">
                <div class="mini-cart-subtotal"><span>{{ $t('subtotal') }}</span><strong id="miniCartSubtotal">—</strong></div>
                <div class="mini-cart-actions">
                    <a class="mini-cart-view" id="miniCartView" href="{{ $wpBaseUrl }}/cart/">{{ $t('view_cart') }}</a>
                    <a class="mini-cart-checkout" id="miniCartCheckout" href="{{ $wpBaseUrl }}/checkout/">{{ $t('checkout') }}</a>
                </div>
            </div>
        </aside>
    </div>

    <div class="sg-modal" id="size-guide-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-label="{{ $t('size_guide_open') }}">
        <div class="sg-backdrop" data-close-size-guide></div>
        <div class="sg-dialog">
            <div class="sg-head">
                <h3 class="sg-title">{{ $t('size_guide') }}</h3>
                <button type="button" class="sg-close" data-close-size-guide>{{ $t('close') }}</button>
            </div>
            <div class="sg-body">
                <div class="sg-table-wrap">
                    <table class="sg-table" id="size-guide-table">
                        <thead>
                            <tr>
                                <th>{{ $t('size_col') }}</th>
                                <th>{{ $t('weight_col') }}</th>
                                <th>{{ $t('bust_col') }}</th>
                                <th>{{ $t('waist_col') }}</th>
                                <th>{{ $t('hips_col') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr data-size="xs"><td>XS</td><td>40 – 48</td><td>78 – 82</td><td>60 – 64</td><td>86 – 90</td></tr>
                            <tr data-size="s"><td>S</td><td>48 – 60</td><td>84 – 90</td><td>66 – 72</td><td>92 – 98</td></tr>
                            <tr data-size="m"><td>M</td><td>60 – 72</td><td>92 – 100</td><td>74 – 82</td><td>100 – 108</td></tr>
                            <tr data-size="l"><td>L</td><td>72 – 85</td><td>102 – 110</td><td>84 – 92</td><td>110 – 118</td></tr>
                            <tr data-size="xl"><td>XL</td><td>85 – 100</td><td>112 – 120</td><td>94 – 104</td><td>120 – 128</td></tr>
                            <tr data-size="xxl"><td>XXL</td><td>100 – 110</td><td>122 – 130</td><td>106 – 114</td><td>130 – 138</td></tr>
                            <tr data-size="3xl"><td>3XL</td><td>110 – 120</td><td>132 – 140</td><td>116 – 124</td><td>140 – 148</td></tr>
                        </tbody>
                    </table>
                    <p class="sg-note">{{ $t('size_guide_note') }}</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        (() => {
            console.info('Styliiiish build:', @json($buildMarker));
            const variationRules = @json($variationRules);
            const hasVariations = @json((bool) ($hasVariations ?? false));
            const selectorsWrap = document.getElementById('attributeSelectors');
            const addToCartBtn = document.getElementById('addToCartBtn');
            const addToCartForm = document.getElementById('addToCartForm');
            const variationIdInput = document.getElementById('variationIdInput');
            const helpText = document.getElementById('cartHelpText');
            const attributeValueNodes = selectorsWrap ? Array.from(selectorsWrap.querySelectorAll('input[data-attribute-key]')) : [];
            const optionButtons = selectorsWrap ? Array.from(selectorsWrap.querySelectorAll('button[data-attr-option]')) : [];

            const chooseOptionsText = @json($t('choose_options_first'));
            const outOfStockText = @json($t('out_of_stock'));
            const addedToCartText = @json($t('added_to_cart'));
            const addFailedText = @json($t('add_to_cart_failed'));
            const cartEmptyText = @json($t('cart_empty'));
            const removeText = @json($t('remove'));
            const qtyShortText = @json($t('qty_short'));
            const loadingCartText = @json($t('loading_cart'));
            const adminAjaxUrl = @json($wpBaseUrl . '/wp-admin/admin-ajax.php');

            const cartTrigger = document.getElementById('miniCartTrigger');
            const cartBadge = document.getElementById('cartCountBadge');
            const plusOne = document.getElementById('cartPlusOne');
            const miniCart = document.getElementById('miniCart');
            const miniCartList = document.getElementById('miniCartList');
            const miniCartSubtotal = document.getElementById('miniCartSubtotal');
            const miniCartView = document.getElementById('miniCartView');
            const miniCartCheckout = document.getElementById('miniCartCheckout');
            const miniCartClosers = miniCart ? miniCart.querySelectorAll('[data-close-mini-cart]') : [];
            let currentCartCount = Number((cartBadge && cartBadge.textContent) || 0) || 0;
            let cartPayloadCache = null;
            let isAddingToCart = false;

            const setCartCount = (count) => {
                currentCartCount = Math.max(0, Number(count) || 0);
                if (!cartBadge) return;
                cartBadge.textContent = String(currentCartCount);
                cartBadge.style.display = currentCartCount > 0 ? 'inline-block' : 'none';
            };

            const resolveCountFromPayload = (payload) => {
                if (!payload) return 0;
                const items = Array.isArray(payload.items) ? payload.items : [];
                if (items.length > 0) {
                    return items.reduce((total, item) => total + Math.max(0, Number(item.qty || 0)), 0);
                }
                return Math.max(0, Number(payload.count || 0));
            };

            const syncPostedAttributes = () => {
                attributeValueNodes.forEach((inputNode) => {
                    const key = inputNode.getAttribute('data-attribute-key');
                    const hidden = document.getElementById('posted_' + key);
                    if (hidden) hidden.value = inputNode.value || '';
                });
            };

            const getSelected = () => {
                const values = {};
                attributeValueNodes.forEach((inputNode) => {
                    const key = inputNode.getAttribute('data-attribute-key');
                    values[key] = (inputNode.value || '').trim();
                });
                return values;
            };

            const setActiveOptionButton = (attributeKey, value) => {
                if (!selectorsWrap) return;

                selectorsWrap.querySelectorAll(`button[data-attr-option][data-attribute-key="${CSS.escape(attributeKey)}"]`).forEach((button) => {
                    const isActive = (button.getAttribute('data-option-value') || '') === value;
                    button.classList.toggle('is-active', isActive);
                    button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                });
            };

            const validateVariation = () => {
                syncPostedAttributes();

                if (!hasVariations) {
                    addToCartBtn.disabled = false;
                    variationIdInput.value = '0';
                    if (helpText.textContent === chooseOptionsText || helpText.textContent === outOfStockText) {
                        helpText.textContent = '';
                    }
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
                if (helpText.textContent === chooseOptionsText || helpText.textContent === outOfStockText) {
                    helpText.textContent = '';
                }
            };

            const openMiniCart = () => {
                if (!miniCart) return;
                miniCart.classList.add('is-open');
                miniCart.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            };

            const closeMiniCart = () => {
                if (!miniCart) return;
                miniCart.classList.remove('is-open');
                miniCart.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            };

            const animatePlusOne = () => {
                if (!plusOne) return;
                plusOne.classList.remove('show');
                void plusOne.offsetWidth;
                plusOne.classList.add('show');
            };

            const renderMiniCart = (payload) => {
                if (!payload) return;
                cartPayloadCache = payload;

                const count = resolveCountFromPayload(payload);
                setCartCount(count);

                if (miniCartSubtotal) miniCartSubtotal.innerHTML = payload.subtotal_html || '—';
                if (miniCartView && payload.cart_url) miniCartView.href = payload.cart_url;
                if (miniCartCheckout && payload.checkout_url) miniCartCheckout.href = payload.checkout_url;

                if (!miniCartList) return;
                const items = Array.isArray(payload.items) ? payload.items : [];
                if (items.length === 0) {
                    miniCartList.innerHTML = `<p class="mini-cart-empty">${cartEmptyText}</p>`;
                    return;
                }

                miniCartList.innerHTML = items.map((item) => {
                    return `
                        <article class="mini-cart-item">
                            <a href="${item.url || '#'}"><img src="${item.image || ''}" alt="${item.name || ''}"></a>
                            <div class="mini-cart-info">
                                <h4>${item.name || ''}</h4>
                                <div class="mini-cart-meta"><span>${qtyShortText}:</span><strong>${item.qty || 1}</strong></div>
                                <div class="mini-cart-price">${item.line_total_html || item.price_html || ''}</div>
                            </div>
                            <button type="button" class="mini-cart-remove" data-remove-cart-key="${item.key || ''}">${removeText}</button>
                        </article>
                    `;
                }).join('');
            };

            const getCartSummary = async () => {
                const response = await fetch(`${adminAjaxUrl}?action=styliiiish_cart_summary`, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (!response.ok) throw new Error('summary_failed');
                const result = await response.json();
                if (!result || !result.success) throw new Error('summary_failed');
                renderMiniCart(result.data);
            };

            const addToCartAjax = async () => {
                if (!addToCartForm) return;

                const formData = new FormData(addToCartForm);
                const params = new URLSearchParams();
                params.append('action', 'styliiiish_add_to_cart');
                params.append('_sty_add_token', `${Date.now()}_${Math.random().toString(36).slice(2, 10)}`);
                formData.forEach((value, key) => {
                    if (key === 'add-to-cart') return;
                    params.append(key, String(value));
                });

                const response = await fetch(adminAjaxUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: params.toString(),
                });

                const result = await response.json();
                if (!response.ok || !result || !result.success) {
                    throw new Error((result && result.data && result.data.message) ? result.data.message : addFailedText);
                }

                renderMiniCart(result.data);
                animatePlusOne();
                helpText.textContent = addedToCartText;
                openMiniCart();
            };

            if (optionButtons.length > 0) {
                optionButtons.forEach((button) => {
                    button.addEventListener('click', () => {
                        const attributeKey = (button.getAttribute('data-attribute-key') || '').trim();
                        const optionValue = (button.getAttribute('data-option-value') || '').trim();
                        if (!attributeKey) return;

                        const inputNode = selectorsWrap ? selectorsWrap.querySelector(`input[data-attribute-key="${CSS.escape(attributeKey)}"]`) : null;
                        if (!inputNode) return;

                        inputNode.value = optionValue;
                        setActiveOptionButton(attributeKey, optionValue);
                        validateVariation();
                        highlightSizeGuideRow();
                    });
                });
            }

            if (attributeValueNodes.length > 0) {
                attributeValueNodes.forEach((node) => {
                    const key = (node.getAttribute('data-attribute-key') || '').trim();
                    const value = (node.value || '').trim();
                    if (key && value) {
                        setActiveOptionButton(key, value);
                    }
                });
                validateVariation();
            } else {
                addToCartBtn.disabled = false;
            }

            const mainProductImage = document.getElementById('mainProductImage');
            const productMediaThumbs = document.getElementById('productMediaThumbs');

            if (mainProductImage && productMediaThumbs) {
                productMediaThumbs.addEventListener('click', (event) => {
                    const thumb = event.target.closest('.media-thumb[data-gallery-src]');
                    if (!thumb) return;

                    const nextSrc = (thumb.getAttribute('data-gallery-src') || '').trim();
                    if (!nextSrc) return;

                    mainProductImage.src = nextSrc;

                    const thumbImage = thumb.querySelector('img');
                    if (thumbImage && thumbImage.alt) {
                        mainProductImage.alt = thumbImage.alt;
                    }

                    productMediaThumbs.querySelectorAll('.media-thumb.is-active').forEach((node) => {
                        node.classList.remove('is-active');
                    });
                    thumb.classList.add('is-active');
                });
            }

            const handleAddToCartSubmit = async (event) => {
                event.preventDefault();
                event.stopPropagation();

                if (isAddingToCart) return;

                validateVariation();
                if (addToCartBtn.disabled) return;

                const originalText = addToCartBtn.textContent;
                isAddingToCart = true;
                addToCartBtn.disabled = true;
                addToCartBtn.textContent = '...';

                try {
                    await addToCartAjax();
                } catch (error) {
                    helpText.textContent = (error && error.message) ? error.message : addFailedText;
                } finally {
                    isAddingToCart = false;
                    addToCartBtn.textContent = originalText;
                    validateVariation();
                }
            };

            if (addToCartForm) {
                addToCartForm.addEventListener('submit', handleAddToCartSubmit);
            }

            if (cartTrigger) {
                cartTrigger.addEventListener('click', () => {
                    openMiniCart();
                    if (cartPayloadCache) {
                        renderMiniCart(cartPayloadCache);
                    } else if (miniCartList && miniCartList.innerHTML.trim() === '') {
                        miniCartList.innerHTML = `<div class="mini-cart-loading">${loadingCartText}</div>`;
                    }
                    getCartSummary().catch(() => {});
                });
            }

            if (miniCartClosers.length > 0) {
                miniCartClosers.forEach((node) => node.addEventListener('click', closeMiniCart));
            }

            if (miniCartList) {
                miniCartList.addEventListener('click', async (event) => {
                    const removeBtn = event.target.closest('[data-remove-cart-key]');
                    if (!removeBtn) return;

                    const cartKey = removeBtn.getAttribute('data-remove-cart-key') || '';
                    if (!cartKey) return;

                    const params = new URLSearchParams();
                    params.set('action', 'styliiiish_remove_from_cart');
                    params.set('cart_key', cartKey);

                    const response = await fetch(adminAjaxUrl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: params.toString(),
                    });

                    const result = await response.json();
                    if (response.ok && result && result.success) {
                        renderMiniCart(result.data);
                    }
                });
            }

            setCartCount(currentCartCount);
            getCartSummary().catch(() => {
                setCartCount(0);
            });

            const trigger = document.getElementById('open-size-guide');
            const modal = document.getElementById('size-guide-modal');
            const sizeGuideTable = document.getElementById('size-guide-table');

            const normalizeSizeValue = (value) => {
                const cleaned = String(value || '').trim().toLowerCase().replace(/\s+/g, '').replace(/_/g, '-');
                if (!cleaned) return '';
                const aliases = {
                    'xsmall': 'xs',
                    'x-small': 'xs',
                    'small': 's',
                    'medium': 'm',
                    'med': 'm',
                    'large': 'l',
                    'xlarge': 'xl',
                    'x-large': 'xl',
                    '2xl': 'xxl',
                    'xx-large': 'xxl',
                    'xxlarge': 'xxl',
                    '3xlarge': '3xl',
                    'xxxlarge': '3xl',
                    'xxx-large': '3xl'
                };
                return aliases[cleaned] || cleaned;
            };

            const getSelectedSizeSlug = () => {
                const sizeNode = attributeValueNodes.find((node) => {
                    const attrKey = String(node.getAttribute('data-attribute-key') || '').toLowerCase();
                    return attrKey.includes('size') || attrKey.includes('مقاس');
                });
                return normalizeSizeValue(sizeNode ? sizeNode.value : '');
            };

            const highlightSizeGuideRow = () => {
                if (!sizeGuideTable) return;
                const selectedSize = getSelectedSizeSlug();
                const rows = sizeGuideTable.querySelectorAll('tbody tr[data-size]');
                rows.forEach((row) => {
                    const rowSize = normalizeSizeValue(row.getAttribute('data-size') || '');
                    row.classList.toggle('sg-active-size', !!selectedSize && rowSize === selectedSize);
                });
            };

            if (trigger && modal) {
                const closeNodes = modal.querySelectorAll('[data-close-size-guide]');

                const openModal = () => {
                    highlightSizeGuideRow();
                    modal.classList.add('is-open');
                    modal.setAttribute('aria-hidden', 'false');
                    document.body.style.overflow = 'hidden';
                };

                const closeModal = () => {
                    modal.classList.remove('is-open');
                    modal.setAttribute('aria-hidden', 'true');
                    document.body.style.overflow = miniCart && miniCart.classList.contains('is-open') ? 'hidden' : '';
                };

                trigger.addEventListener('click', openModal);
                closeNodes.forEach((node) => node.addEventListener('click', closeModal));
            }

            highlightSizeGuideRow();

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    if (miniCart && miniCart.classList.contains('is-open')) {
                        closeMiniCart();
                    }

                    const modal = document.getElementById('size-guide-modal');
                    if (modal && modal.classList.contains('is-open')) {
                        modal.classList.remove('is-open');
                        modal.setAttribute('aria-hidden', 'true');
                        document.body.style.overflow = '';
                    }
                }
            });
        })();
    </script>
</body>
</html>
