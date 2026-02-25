<!DOCTYPE html>
@php
    $currentLocale = $currentLocale ?? 'ar';
    $localePrefix = $localePrefix ?? '/ar';
    $isEnglish = $currentLocale === 'en';
    $wpBaseUrl = rtrim((string) ($wpBaseUrl ?? env('WP_PUBLIC_URL', request()->getSchemeAndHttpHost())), '/');
        $wpCheckoutUrl = $isEnglish ? ($wpBaseUrl . '/checkout/') : ($wpBaseUrl . '/ar/الدفع/');
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
            'category' => 'التصنيف',
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
            'add_to_wishlist' => 'أضيفي إلى المفضلة',
            'added_to_wishlist' => 'تمت إضافة المنتج إلى المفضلة',
            'wishlist_already_added' => 'هذا المنتج مضاف إلى المفضلة بالفعل.',
            'wishlist_add_failed' => 'تعذر إضافة المنتج إلى المفضلة',
            'wishlist_loading' => 'جاري تحميل المفضلة…',
            'wishlist_empty' => 'لا توجد منتجات في المفضلة حالياً.',
            'go_to_product' => 'اذهب إلى المنتج',
            'view_all_wishlist' => 'عرض كل المفضلة',
            'cart' => 'العربة',
            'about' => 'من نحن',
            'categories' => 'الأقسام',
            'categories_intro' => 'تصفحي أقسام المنتجات للوصول السريع لكل الفئات المتاحة.',
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
            'report_title' => 'الإبلاغ عن هذا المنتج',
            'report_subtitle' => 'لو في أي ملاحظة تخص جودة المحتوى أو السعر أو الوصف، أرسلي بلاغك وسيتم مراجعته سريعًا.',
            'report_name' => 'الاسم',
            'report_email' => 'البريد الإلكتروني (اختياري)',
            'report_reason' => 'سبب البلاغ',
            'report_submit' => 'إرسال البلاغ',
            'report_placeholder' => 'اكتبي تفاصيل البلاغ بشكل واضح...',
            'tab_description' => 'الوصف',
            'tab_specifications' => 'المواصفات',
            'tab_reviews' => 'المراجعات والتعليقات',
            'tab_policies' => 'السياسات',
            'tab_loading' => 'جاري تحميل المحتوى...',
            'tab_load_failed' => 'تعذر تحميل المحتوى حالياً. حاولي مرة أخرى.',
            'leave_review' => 'اترك تعليق',
            'review_title' => 'إضافة تقييم وتعليق',
            'review_name' => 'الاسم',
            'review_email' => 'البريد الإلكتروني',
            'review_rating' => 'التقييم',
            'review_comment' => 'التعليق',
            'review_submit' => 'إرسال المراجعة',
            'review_placeholder' => 'اكتبي تجربتك مع المنتج...',
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
            'category' => 'Category',
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
            'add_to_wishlist' => 'Add to Wishlist',
            'added_to_wishlist' => 'Product added to wishlist',
            'wishlist_already_added' => 'This product is already in your wishlist.',
            'wishlist_add_failed' => 'Unable to add product to wishlist',
            'wishlist_loading' => 'Loading wishlist…',
            'wishlist_empty' => 'No products in wishlist yet.',
            'go_to_product' => 'Go to product',
            'view_all_wishlist' => 'View full wishlist',
            'cart' => 'Cart',
            'about' => 'About',
            'categories' => 'Categories',
            'categories_intro' => 'Browse product categories for quick access to all available collections.',
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
            'report_title' => 'Report this product',
            'report_subtitle' => 'If you notice any issue with product content, pricing, or details, send a report and our team will review it quickly.',
            'report_name' => 'Name',
            'report_email' => 'Email (optional)',
            'report_reason' => 'Report reason',
            'report_submit' => 'Send report',
            'report_placeholder' => 'Write your report details clearly...',
            'tab_description' => 'Description',
            'tab_specifications' => 'Specifications',
            'tab_reviews' => 'Reviews & Comments',
            'tab_policies' => 'Policies',
            'tab_loading' => 'Loading content...',
            'tab_load_failed' => 'Unable to load content right now. Please try again.',
            'leave_review' => 'Leave a review',
            'review_title' => 'Add rating & review',
            'review_name' => 'Name',
            'review_email' => 'Email',
            'review_rating' => 'Rating',
            'review_comment' => 'Comment',
            'review_submit' => 'Submit review',
            'review_placeholder' => 'Write your product feedback...',
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
    $productCategoryNames = collect($productCategoryNames ?? [])->map(fn ($name) => trim((string) $name))->filter(fn ($name) => $name !== '')->values();
    $allProductCategories = collect($allProductCategories ?? [])->map(function ($row) {
        return [
            'name' => trim((string) (is_array($row) ? ($row['name'] ?? '') : ($row->name ?? ''))),
            'slug' => trim((string) (is_array($row) ? ($row['slug'] ?? '') : ($row->slug ?? ''))),
        ];
    })->filter(fn ($row) => $row['name'] !== '' && $row['slug'] !== '')->values();

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
    $addToWishlistUrl = $wpBaseUrl . '/?add_to_wishlist=' . (int) ($product->ID ?? 0);
    $wishlistPageUrl = $localePrefix . '/wishlist';
    $buildMarker = 'PRODUCT_SINGLE_BUILD_2026-02-23_01';
    $wpLogo = 'https://styliiiish.com/wp-content/uploads/2025/11/ChatGPT-Image-Nov-2-2025-03_11_14-AM-e1762046066547.png';
    $wpIcon = 'https://styliiiish.com/wp-content/uploads/2025/11/cropped-ChatGPT-Image-Nov-2-2025-03_11_14-AM-e1762046066547.png';

    $seoProductName = trim((string) ($product->post_title ?? ($isEnglish ? 'Product' : 'المنتج')));
    $seoTitle = $seoProductName !== '' ? ($seoProductName . ' | Styliiiish') : 'Styliiiish';

    $seoDescriptionSource = trim((string) ($product->post_excerpt ?: $product->post_content ?: $seoProductName));
    $seoDescriptionSource = html_entity_decode(strip_tags($seoDescriptionSource), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $seoDescriptionSource = preg_replace('/\s+/u', ' ', $seoDescriptionSource);
    $seoDescriptionSource = trim((string) $seoDescriptionSource);
    if ($seoDescriptionSource === '') {
        $seoDescriptionSource = $isEnglish
            ? 'Discover this elegant dress on Styliiiish with premium quality and fast shipping in Egypt.'
            : 'اكتشفي هذا الفستان الأنيق على Styliiiish بجودة عالية وتوصيل سريع داخل مصر.';
    }
    $seoDescription = mb_strlen($seoDescriptionSource) > 165
        ? rtrim(mb_substr($seoDescriptionSource, 0, 162)) . '…'
        : $seoDescriptionSource;

    $seoUrl = $wpBaseUrl . $canonicalPath;
    $seoImage = trim((string) ($mainImage ?: $image ?: $placeholderImage));
    if (!str_starts_with($seoImage, 'http://') && !str_starts_with($seoImage, 'https://')) {
        $seoImage = rtrim($wpBaseUrl, '/') . '/' . ltrim($seoImage, '/');
    }

    $seoKeywords = collect([$seoProductName, $material, $color, $condition])
        ->merge($productCategoryNames)
        ->map(fn ($value) => trim((string) $value))
        ->filter(fn ($value) => $value !== '')
        ->unique()
        ->values()
        ->implode(', ');

    $hasAnyInStockVariation = collect($variationRules)
        ->contains(fn ($rule) => strtolower((string) ($rule['stock_status'] ?? '')) === 'instock');
    $productStockStatus = strtolower(trim((string) ($product->stock_status ?? '')));
    $isInStock = !empty($hasVariations)
        ? $hasAnyInStockVariation
        : ($productStockStatus === '' || $productStockStatus === 'instock');

    $schemaProduct = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $seoProductName,
        'description' => $seoDescription,
        'image' => [$seoImage],
        'sku' => (string) ((int) ($product->ID ?? 0)),
        'url' => $seoUrl,
        'brand' => [
            '@type' => 'Brand',
            'name' => 'Styliiiish',
        ],
        'offers' => [
            '@type' => 'Offer',
            'url' => $seoUrl,
            'priceCurrency' => 'EGP',
            'price' => $price > 0 ? number_format($price, 2, '.', '') : '0.00',
            'availability' => $isInStock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'itemCondition' => 'https://schema.org/NewCondition',
        ],
    ];

    $seoLocale = $isEnglish ? 'en_US' : 'ar_EG';
    $seoAlternateLocale = $isEnglish ? 'ar_EG' : 'en_US';
@endphp
<html lang="{{ $isEnglish ? 'en' : 'ar' }}" dir="{{ $isEnglish ? 'ltr' : 'rtl' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    @if($seoKeywords !== '')
        <meta name="keywords" content="{{ $seoKeywords }}">
    @endif
    <meta name="author" content="Styliiiish">
    <meta name="theme-color" content="#17273B">

    <link rel="canonical" href="{{ $seoUrl }}">
    <link rel="alternate" hreflang="ar" href="{{ $wpBaseUrl }}/ar/item/{{ rawurlencode((string) ($product->post_name ?? '')) }}">
    <link rel="alternate" hreflang="en" href="{{ $wpBaseUrl }}/en/item/{{ rawurlencode((string) ($product->post_name ?? '')) }}">
    <link rel="alternate" hreflang="x-default" href="{{ $wpBaseUrl }}/ar/item/{{ rawurlencode((string) ($product->post_name ?? '')) }}">

    <meta property="og:type" content="product">
    <meta property="og:site_name" content="Styliiiish">
    <meta property="og:locale" content="{{ $seoLocale }}">
    <meta property="og:locale:alternate" content="{{ $seoAlternateLocale }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ $seoUrl }}">
    <meta property="og:image" content="{{ $seoImage }}">
    <meta property="og:image:alt" content="{{ $seoProductName }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $seoImage }}">

    <link rel="icon" type="image/png" href="{{ $wpIcon }}">
    <link rel="shortcut icon" href="{{ $wpIcon }}">
    <link rel="apple-touch-icon" href="{{ $wpIcon }}">

    <script type="application/ld+json">{!! json_encode($schemaProduct, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
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
        .wishlist-trigger-wrap { position: relative; }
        .wishlist-trigger { position: relative; }
        .wishlist-count {
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
            display: none;
        }
        .wishlist-plus-one {
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
        .wishlist-plus-one.show {
            animation: cartPlusOne .8s ease;
        }
        .wishlist-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            width: min(360px, 82vw);
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 12px;
            box-shadow: 0 12px 30px rgba(23, 39, 59, .14);
            padding: 10px;
            display: none;
            z-index: 80;
        }
        [dir="rtl"] .wishlist-dropdown {
            right: auto;
            left: 0;
        }
        [dir="ltr"] .wishlist-dropdown {
            left: auto;
            right: 0;
        }
        .wishlist-dropdown.is-open { display: block; }
        .wishlist-dropdown-list { display: grid; gap: 8px; max-height: 360px; overflow: auto; }
        .wishlist-dropdown-item {
            display: grid;
            grid-template-columns: 56px 1fr;
            gap: 10px;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 8px;
            background: #fff;
        }
        .wishlist-dropdown-item img {
            width: 56px;
            height: 56px;
            object-fit: cover;
            border-radius: 8px;
            background: #f2f2f5;
        }
        .wishlist-dropdown-name {
            font-size: 13px;
            font-weight: 800;
            color: var(--secondary);
            margin: 0 0 6px;
            line-height: 1.35;
        }
        .wishlist-dropdown-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 30px;
            padding: 0 10px;
            border-radius: 8px;
            background: var(--primary);
            color: #fff;
            font-size: 12px;
            font-weight: 700;
        }
        .wishlist-dropdown-empty {
            margin: 0;
            font-size: 13px;
            color: var(--muted);
            text-align: center;
            padding: 12px 8px;
            border: 1px dashed var(--line);
            border-radius: 10px;
            background: #fbfcff;
        }
        .wishlist-dropdown-footer {
            display: flex;
            justify-content: center;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid var(--line);
        }
        .wishlist-dropdown-all {
            font-size: 13px;
            color: var(--primary);
            font-weight: 800;
        }
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
        .attr-option-btn:disabled,
        .attr-option-btn.is-disabled {
            opacity: .4;
            cursor: not-allowed;
            border-color: var(--line);
            color: var(--muted);
            background: #f3f4f7;
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
        .attr-warning {
            margin-top: 8px;
            border: 1px solid #F2D58A;
            background: #FFF9E8;
            color: var(--secondary);
            border-radius: 10px;
            padding: 8px 10px;
            font-size: 12px;
            line-height: 1.6;
            display: none;
        }
        .attr-warning.is-visible { display: block; }
        .attr-warning strong { color: #9A6B00; }

        .guide-row { margin-top: 12px; display: flex; gap: 8px; flex-wrap: wrap; }
        .btn-ghost { border: 1px solid var(--line); border-radius: 10px; background: #fff; color: var(--secondary); padding: 10px 14px; font-size: 14px; font-weight: 700; cursor: pointer; }
        .btn-accent {
            border: 1px solid var(--primary);
            border-radius: 10px;
            background: var(--primary);
            color: #fff;
            padding: 10px 14px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: .16s ease;
        }
        .btn-accent:hover {
            filter: brightness(0.95);
            transform: translateY(-1px);
        }

        .description { margin-top: 14px; color: var(--muted); line-height: 1.7; }

        .report-trigger-wrap {
            margin-top: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            border: 1px solid var(--line);
            background: #fff;
            border-radius: 12px;
            padding: 10px 12px;
        }
        .report-trigger-text { margin: 0; font-size: 13px; color: var(--muted); line-height: 1.7; }
        .report-trigger-btn {
            border: 1px solid var(--line);
            background: #fff;
            color: var(--secondary);
            border-radius: 999px;
            min-height: 36px;
            padding: 0 12px;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            white-space: nowrap;
        }
        .report-trigger-btn:hover { border-color: var(--primary); color: var(--primary); }

        .report-modal { position: fixed; inset: 0; z-index: 130; display: none; align-items: center; justify-content: center; padding: 20px; }
        .report-modal.is-open { display: flex; }
        .report-modal-backdrop { position: absolute; inset: 0; background: rgba(15, 26, 42, 0.66); }
        .report-modal-dialog {
            position: relative;
            z-index: 1;
            width: min(760px, 96vw);
            max-height: 88vh;
            overflow: auto;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 14px;
            display: grid;
            gap: 10px;
        }
        .report-modal-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid var(--line);
            padding-bottom: 8px;
        }
        .report-modal-close {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #fff;
            color: var(--secondary);
            padding: 6px 10px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }
        .report-title { margin: 0; font-size: 18px; color: var(--secondary); }
        .report-subtitle { margin: 0; font-size: 13px; color: var(--muted); line-height: 1.7; }
        .report-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .report-field { display: grid; gap: 6px; }
        .report-field label { font-size: 12px; font-weight: 700; color: var(--secondary); }
        .report-field input,
        .report-field textarea {
            width: 100%;
            min-height: 42px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #fff;
            padding: 10px;
            font-size: 13px;
            color: var(--secondary);
            font-family: inherit;
        }
        .report-field textarea { min-height: 100px; resize: vertical; }
        .report-submit {
            border: 0;
            border-radius: 10px;
            min-height: 42px;
            background: var(--primary);
            color: #fff;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            padding: 0 14px;
            justify-self: start;
        }
        .report-submit:disabled { opacity: .6; cursor: not-allowed; }
        .report-message { margin: 0; font-size: 12px; min-height: 18px; color: var(--muted); }

        .review-modal { position: fixed; inset: 0; z-index: 131; display: none; align-items: center; justify-content: center; padding: 20px; }
        .review-modal.is-open { display: flex; }
        .review-modal-backdrop { position: absolute; inset: 0; background: rgba(15, 26, 42, 0.66); }
        .review-modal-dialog {
            position: relative;
            z-index: 1;
            width: min(700px, 96vw);
            max-height: 88vh;
            overflow: auto;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 14px;
            display: grid;
            gap: 10px;
        }
        .review-rating-row { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .review-star {
            border: 1px solid var(--line);
            background: #fff;
            color: #D4AF37;
            border-radius: 8px;
            min-width: 36px;
            min-height: 36px;
            font-size: 18px;
            line-height: 1;
            cursor: pointer;
        }
        .review-star.is-active {
            border-color: #D4AF37;
            background: #FFF9E8;
        }

        .product-tabs {
            margin-top: 16px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            overflow: hidden;
        }
        .product-tabs-head {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            padding: 10px;
            border-bottom: 1px solid var(--line);
            background: #fcfdff;
        }
        .product-tab-btn {
            border: 1px solid var(--line);
            background: #fff;
            color: var(--secondary);
            border-radius: 999px;
            min-height: 36px;
            padding: 0 12px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }
        .product-tab-btn.is-active {
            border-color: var(--primary);
            color: var(--primary);
            background: rgba(var(--wf-main-rgb), .08);
        }
        .product-tabs-body {
            padding: 14px;
            color: var(--secondary);
            line-height: 1.8;
        }
        .product-tabs-loading {
            border: 1px dashed var(--line);
            border-radius: 10px;
            padding: 12px;
            color: var(--muted);
            font-size: 13px;
            background: #fbfcff;
        }

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

        .category-section {
            margin-top: 16px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 14px;
        }
        .category-intro {
            margin: -4px 0 12px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.7;
        }
        .category-chip-list { display: flex; flex-wrap: wrap; gap: 10px; }
        .category-chip {
            display: inline-flex;
            align-items: center;
            min-height: 36px;
            padding: 0 12px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: #fbfcff;
            color: var(--secondary);
            font-size: 13px;
            font-weight: 700;
            transition: .16s ease;
        }
        .category-chip:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: #fff;
            transform: translateY(-1px);
        }

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
            .report-trigger-wrap { flex-direction: column; align-items: flex-start; }
            .report-grid { grid-template-columns: 1fr; }
            .related-grid { grid-template-columns: 1fr; gap: 10px; }
            .r-actions { grid-template-columns: 1fr; }
            .sg-table th,
            .sg-table td { font-size: 12px; padding: 8px; }
            .media-thumbs { grid-template-columns: repeat(5, minmax(0, 1fr)); }
            .wishlist-dropdown {
                left: 50% !important;
                right: auto !important;
                transform: translateX(-50%);
                width: min(360px, 92vw);
            }
        }
    </style>
</head>
<body>
    <!-- {{ $buildMarker }} -->
    @include('partials.site-header')

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

                @if($contentHtml !== '')
                    <section class="description" style="margin-top: 14px;">
                        <h3 class="section-title" style="font-size:18px;">{{ $t('description') }}</h3>
                        {!! $contentHtml !!}
                    </section>
                @endif
            </article>

            <article class="panel details">
                <h1 class="title">{{ $product->post_title }}</h1>

                <div class="prices">
                    <span class="price" id="productPrice">
                        @if($price > 0)
                            {{ number_format($price) }} {{ $t('currency') }}
                        @else
                            {{ $t('contact_for_price') }}
                        @endif
                    </span>
                    <span class="old" id="productOldPrice" style="{{ $isSale ? '' : 'display:none;' }}">{{ $isSale ? number_format($regular) . ' ' . $t('currency') : '' }}</span>
                </div>

                <h2 class="section-title">{{ $t('dress_details') }}</h2>
                <ul class="detail-list">
                    <li><strong>{{ $t('category') }}:</strong> {{ $productCategoryNames->isNotEmpty() ? $productCategoryNames->implode(', ') : $t('na') }}</li>
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
                        <div class="attr-warning" id="conditionAvailabilityHint" aria-live="polite"></div>
                    @endif

                    <div class="cart-row">
                        <input class="qty-input" type="number" min="1" step="1" value="1" name="quantity" aria-label="{{ $t('qty') }}">
                        <button class="btn-main" id="addToCartBtn" type="submit">{{ $t('add_to_cart') }}</button>
                    </div>
                    <div class="help-text" id="cartHelpText"></div>
                </form>

                <div class="guide-row">
                    <button type="button" class="btn-ghost" id="open-size-guide">{{ $t('size_guide') }}</button>
                    <button type="button" class="btn-accent" id="addToWishlistBtn">{{ $t('add_to_wishlist') }}</button>
                    <button type="button" class="btn-accent" data-open-review-modal>{{ $t('leave_review') }}</button>
                </div>
            </article>
        </section>

        <section class="report-trigger-wrap" id="productReportSection">
            <p class="report-trigger-text">{{ $t('report_subtitle') }}</p>
            <button type="button" class="report-trigger-btn" id="openReportModal">{{ $t('report_title') }}</button>
        </section>

        <div class="report-modal" id="productReportModal" aria-hidden="true" role="dialog" aria-modal="true" aria-label="{{ $t('report_title') }}">
            <div class="report-modal-backdrop" data-close-report-modal></div>
            <div class="report-modal-dialog">
                <div class="report-modal-head">
                    <h2 class="report-title">{{ $t('report_title') }}</h2>
                    <button type="button" class="report-modal-close" data-close-report-modal>{{ $t('close') }}</button>
                </div>
                <p class="report-subtitle">{{ $t('report_subtitle') }}</p>

                <form id="productReportForm" novalidate>
                    <div class="report-grid">
                        <div class="report-field">
                            <label for="reportName">{{ $t('report_name') }}</label>
                            <input type="text" id="reportName" name="name" maxlength="120" required>
                        </div>
                        <div class="report-field">
                            <label for="reportEmail">{{ $t('report_email') }}</label>
                            <input type="email" id="reportEmail" name="email" maxlength="190">
                        </div>
                    </div>
                    <div class="report-field">
                        <label for="reportReason">{{ $t('report_reason') }}</label>
                        <textarea id="reportReason" name="reason" maxlength="2000" required placeholder="{{ $t('report_placeholder') }}"></textarea>
                    </div>
                    <button class="report-submit" id="reportSubmitBtn" type="submit">{{ $t('report_submit') }}</button>
                    <p class="report-message" id="reportMessage"></p>
                </form>
            </div>
        </div>

        <div class="review-modal" id="productReviewModal" aria-hidden="true" role="dialog" aria-modal="true" aria-label="{{ $t('review_title') }}">
            <div class="review-modal-backdrop" data-close-review-modal></div>
            <div class="review-modal-dialog">
                <div class="report-modal-head">
                    <h2 class="report-title">{{ $t('review_title') }}</h2>
                    <button type="button" class="report-modal-close" data-close-review-modal>{{ $t('close') }}</button>
                </div>

                <form id="productReviewForm" novalidate>
                    <div class="report-grid">
                        <div class="report-field">
                            <label for="reviewName">{{ $t('review_name') }}</label>
                            <input type="text" id="reviewName" name="name" maxlength="120" required>
                        </div>
                        <div class="report-field">
                            <label for="reviewEmail">{{ $t('review_email') }}</label>
                            <input type="email" id="reviewEmail" name="email" maxlength="190" required>
                        </div>
                    </div>

                    <div class="report-field">
                        <label>{{ $t('review_rating') }}</label>
                        <div class="review-rating-row" id="reviewStarsRow">
                            <button type="button" class="review-star" data-review-star="1" aria-label="1 star">☆</button>
                            <button type="button" class="review-star" data-review-star="2" aria-label="2 stars">☆</button>
                            <button type="button" class="review-star" data-review-star="3" aria-label="3 stars">☆</button>
                            <button type="button" class="review-star" data-review-star="4" aria-label="4 stars">☆</button>
                            <button type="button" class="review-star" data-review-star="5" aria-label="5 stars">☆</button>
                        </div>
                        <input type="hidden" id="reviewRatingInput" name="rating" value="0">
                    </div>

                    <div class="report-field">
                        <label for="reviewComment">{{ $t('review_comment') }}</label>
                        <textarea id="reviewComment" name="comment" maxlength="2000" required placeholder="{{ $t('review_placeholder') }}"></textarea>
                    </div>

                    <button class="report-submit" id="reviewSubmitBtn" type="submit">{{ $t('review_submit') }}</button>
                    <p class="report-message" id="reviewMessage"></p>
                </form>
            </div>
        </div>

        <section class="product-tabs" id="productAjaxTabs">
            <div class="product-tabs-head" role="tablist" aria-label="Product Sections">
                <button type="button" class="product-tab-btn is-active" data-product-tab="description" role="tab" aria-selected="true">{{ $t('tab_description') }}</button>
                <button type="button" class="product-tab-btn" data-product-tab="specifications" role="tab" aria-selected="false">{{ $t('tab_specifications') }}</button>
                <button type="button" class="product-tab-btn" data-product-tab="reviews" role="tab" aria-selected="false">{{ $t('tab_reviews') }}</button>
                <button type="button" class="product-tab-btn" data-product-tab="policies" role="tab" aria-selected="false">{{ $t('tab_policies') }}</button>
            </div>
            <div class="product-tabs-body" id="productTabsBody">
                <div class="product-tabs-loading">{{ $t('tab_loading') }}</div>
            </div>
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

        @if($allProductCategories->isNotEmpty())
            <section class="category-section">
                <h2 class="section-title">{{ $t('categories') }}</h2>
                <p class="category-intro">{{ $t('categories_intro') }}</p>
                <div class="category-chip-list">
                    @foreach($allProductCategories as $category)
                        <a class="category-chip" href="{{ $wpBaseUrl }}/product-category/{{ rawurlencode($category['slug']) }}/" target="_blank" rel="noopener">{{ $category['name'] }}</a>
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
                    <a class="mini-cart-view" id="miniCartView" href="{{ $localePrefix }}/cart">{{ $t('view_cart') }}</a>
                    <a class="mini-cart-checkout" id="miniCartCheckout" href="{{ $wpCheckoutUrl }}">{{ $t('checkout') }}</a>
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
            const addedToWishlistText = @json($t('added_to_wishlist'));
            const wishlistAlreadyAddedText = @json($t('wishlist_already_added'));
            const wishlistAddFailedText = @json($t('wishlist_add_failed'));
            const cartEmptyText = @json($t('cart_empty'));
            const removeText = @json($t('remove'));
            const qtyShortText = @json($t('qty_short'));
            const loadingCartText = @json($t('loading_cart'));
            const wishlistLoadingText = @json($t('wishlist_loading'));
            const wishlistEmptyText = @json($t('wishlist_empty'));
            const goToProductText = @json($t('go_to_product'));
            const currentLocale = @json($currentLocale);
            const currencyText = @json($t('currency'));
            const contactForPriceText = @json($t('contact_for_price'));
            const tabLoadingText = @json($t('tab_loading'));
            const tabLoadFailedText = @json($t('tab_load_failed'));
            const leaveReviewText = @json($t('leave_review'));
            const adminAjaxUrl = @json($wpBaseUrl . '/wp-admin/admin-ajax.php');
                const wpCheckoutUrl = @json($wpCheckoutUrl);
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const productSlug = @json((string) ($product->post_name ?? ''));
            const productId = Number(@json((int) ($product->ID ?? 0))) || 0;
            const wishlistPageUrl = @json($wishlistPageUrl);

            const priceNode = document.getElementById('productPrice');
            const oldPriceNode = document.getElementById('productOldPrice');
            const conditionAvailabilityHint = document.getElementById('conditionAvailabilityHint');
            const addToWishlistBtn = document.getElementById('addToWishlistBtn');
            const basePrice = Number(@json((float) ($price ?? 0))) || 0;
            const baseRegularPrice = Number(@json((float) ($regular ?? 0))) || 0;

            const tabsWrap = document.getElementById('productAjaxTabs');
            const tabButtons = tabsWrap ? Array.from(tabsWrap.querySelectorAll('[data-product-tab]')) : [];
            const tabsBody = document.getElementById('productTabsBody');
            const reportForm = document.getElementById('productReportForm');
            const reportSubmitBtn = document.getElementById('reportSubmitBtn');
            const reportMessage = document.getElementById('reportMessage');
            const reportModal = document.getElementById('productReportModal');
            const reportOpenBtn = document.getElementById('openReportModal');
            const reportModalClosers = reportModal ? reportModal.querySelectorAll('[data-close-report-modal]') : [];
            const reviewModal = document.getElementById('productReviewModal');
            const reviewModalClosers = reviewModal ? reviewModal.querySelectorAll('[data-close-review-modal]') : [];
            const reviewForm = document.getElementById('productReviewForm');
            const reviewSubmitBtn = document.getElementById('reviewSubmitBtn');
            const reviewMessage = document.getElementById('reviewMessage');
            const reviewRatingInput = document.getElementById('reviewRatingInput');
            const reviewStars = reviewModal ? Array.from(reviewModal.querySelectorAll('[data-review-star]')) : [];

            const cartTrigger = document.getElementById('miniCartTrigger');
            const wishlistTrigger = document.getElementById('wishlistTrigger');
            const wishlistBadge = document.getElementById('wishlistCountBadge');
            const wishlistPlusOne = document.getElementById('wishlistPlusOne');
            const wishlistDropdown = document.getElementById('wishlistDropdown');
            const wishlistDropdownList = document.getElementById('wishlistDropdownList');
            const cartBadge = document.getElementById('cartCountBadge');
            const plusOne = document.getElementById('cartPlusOne');
            const miniCart = document.getElementById('miniCart');
            const miniCartList = document.getElementById('miniCartList');
            const miniCartSubtotal = document.getElementById('miniCartSubtotal');
            const miniCartView = document.getElementById('miniCartView');
            const miniCartCheckout = document.getElementById('miniCartCheckout');
            const miniCartClosers = miniCart ? miniCart.querySelectorAll('[data-close-mini-cart]') : [];
            let currentCartCount = Number((cartBadge && cartBadge.textContent) || 0) || 0;
            let currentWishlistCount = Number((wishlistBadge && wishlistBadge.textContent) || 0) || 0;
            let cartPayloadCache = null;
            let wishlistItemsCache = [];
            let isAddingToCart = false;
            let isAddingToWishlist = false;

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

            const setWishlistCount = (count) => {
                currentWishlistCount = Math.max(0, Number(count) || 0);
                if (!wishlistBadge) return;
                wishlistBadge.textContent = String(currentWishlistCount);
                wishlistBadge.style.display = currentWishlistCount > 0 ? 'inline-block' : 'none';
            };

            const animateWishlistPlusOne = () => {
                if (!wishlistPlusOne) return;
                wishlistPlusOne.classList.remove('show');
                void wishlistPlusOne.offsetWidth;
                wishlistPlusOne.classList.add('show');
            };

            const fetchWishlistCount = async () => {
                const response = await fetch(`${getWishlistCountUrl()}?_=${Date.now()}`, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) {
                    throw new Error('wishlist_count_failed');
                }

                const result = await response.json();
                if (!result || !result.success) {
                    throw new Error('wishlist_count_failed');
                }

                return Math.max(0, Number(result.count || 0));
            };

            const refreshWishlistCount = async (withAnimation = false) => {
                try {
                    const count = await fetchWishlistCount();
                    const shouldAnimate = withAnimation && count > currentWishlistCount;
                    setWishlistCount(count);
                    if (shouldAnimate) {
                        animateWishlistPlusOne();
                    }
                } catch (error) {
                }
            };

            const addToWishlistAjax = async () => {
                const response = await fetch(getWishlistAddUrl(), {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: '',
                });

                if (!response.ok) {
                    throw new Error(wishlistAddFailedText);
                }

                const result = await response.json();
                if (!result || !result.success) {
                    throw new Error((result && result.message) ? result.message : wishlistAddFailedText);
                }

                const nextCount = Math.max(0, Number(result.count || 0));
                const shouldAnimate = nextCount > currentWishlistCount;
                setWishlistCount(nextCount);
                if (shouldAnimate) {
                    animateWishlistPlusOne();
                }
                loadWishlistItems(true).catch(() => {});
                if (nextCount <= currentWishlistCount) {
                    helpText.textContent = wishlistAlreadyAddedText;
                } else {
                    helpText.textContent = String(result.message || addedToWishlistText);
                }
            };

            const isCurrentProductAlreadyInWishlist = async () => {
                if (productId <= 0) return false;

                try {
                    const items = await loadWishlistItems(false);
                    const normalizedSlug = String(productSlug || '').trim().toLowerCase();

                    return (Array.isArray(items) ? items : []).some((item) => {
                        const itemId = Number(item && item.id ? item.id : 0) || 0;
                        if (itemId > 0 && itemId === productId) {
                            return true;
                        }

                        const url = String(item && item.url ? item.url : '').toLowerCase();
                        if (!normalizedSlug || !url) {
                            return false;
                        }

                        return url.includes(`/item/${normalizedSlug}`) || decodeURIComponent(url).includes(`/item/${normalizedSlug}`);
                    });
                } catch (error) {
                    return false;
                }
            };

            const renderWishlistDropdown = (items = []) => {
                if (!wishlistDropdownList) return;
                const safeItems = Array.isArray(items) ? items : [];

                if (safeItems.length === 0) {
                    wishlistDropdownList.innerHTML = `<p class="wishlist-dropdown-empty">${wishlistEmptyText}</p>`;
                    return;
                }

                wishlistDropdownList.innerHTML = safeItems.map((item) => {
                    const image = String(item.image || '').trim();
                    const name = String(item.name || '').trim();
                    const url = String(item.url || '#').trim();

                    return `
                        <article class="wishlist-dropdown-item">
                            <a href="${url}"><img src="${image}" alt="${name}"></a>
                            <div>
                                <h4 class="wishlist-dropdown-name">${name}</h4>
                                <a class="wishlist-dropdown-link" href="${url}">${goToProductText}</a>
                            </div>
                        </article>
                    `;
                }).join('');
            };

            const loadWishlistItems = async (forceReload = false) => {
                if (!wishlistDropdownList) return wishlistItemsCache;

                if (!forceReload && Array.isArray(wishlistItemsCache) && wishlistItemsCache.length > 0) {
                    renderWishlistDropdown(wishlistItemsCache);
                    return wishlistItemsCache;
                }

                wishlistDropdownList.innerHTML = `<p class="wishlist-dropdown-empty">${wishlistLoadingText}</p>`;

                const response = await fetch(`${getWishlistItemsUrl()}?_=${Date.now()}`, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) {
                    throw new Error('wishlist_items_failed');
                }

                const result = await response.json();
                if (!result || !result.success) {
                    throw new Error('wishlist_items_failed');
                }

                const nextCount = Math.max(0, Number(result.count || 0));
                setWishlistCount(nextCount);

                wishlistItemsCache = Array.isArray(result.items) ? result.items : [];
                renderWishlistDropdown(wishlistItemsCache);
                return wishlistItemsCache;
            };

            const openWishlistDropdown = async () => {
                if (!wishlistDropdown || !wishlistTrigger) return;
                wishlistDropdown.classList.add('is-open');
                wishlistDropdown.setAttribute('aria-hidden', 'false');
                wishlistTrigger.setAttribute('aria-expanded', 'true');

                try {
                    await loadWishlistItems(true);
                } catch (error) {
                    renderWishlistDropdown([]);
                }
            };

            const closeWishlistDropdown = () => {
                if (!wishlistDropdown || !wishlistTrigger) return;
                wishlistDropdown.classList.remove('is-open');
                wishlistDropdown.setAttribute('aria-hidden', 'true');
                wishlistTrigger.setAttribute('aria-expanded', 'false');
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

            const formatMoney = (amount) => {
                const value = Number(amount) || 0;
                return `${Math.round(value).toLocaleString()} ${currencyText}`;
            };

            const updateDisplayedPrice = (rule = null) => {
                if (!priceNode || !oldPriceNode) return;

                const sourcePrice = rule ? Number(rule.price || 0) : basePrice;
                const sourceRegular = rule ? Number(rule.regular_price || 0) : baseRegularPrice;

                if (sourcePrice > 0) {
                    priceNode.textContent = formatMoney(sourcePrice);
                } else {
                    priceNode.textContent = contactForPriceText;
                }

                const hasSale = sourceRegular > 0 && sourcePrice > 0 && sourceRegular > sourcePrice;
                if (hasSale) {
                    oldPriceNode.textContent = formatMoney(sourceRegular);
                    oldPriceNode.style.display = '';
                } else {
                    oldPriceNode.textContent = '';
                    oldPriceNode.style.display = 'none';
                }
            };

            const getRuleAttributeValue = (rule, key) => {
                const attrs = rule && rule.attributes ? rule.attributes : {};
                return String(attrs[key] || '').trim();
            };

            const findCompatibleRules = (selected, forcedKey = null, forcedValue = '') => {
                return variationRules.filter((rule) => {
                    const stockStatus = String(rule.stock_status || 'instock').toLowerCase();
                    if (stockStatus !== 'instock') return false;

                    for (const [key, selectedValue] of Object.entries(selected)) {
                        if (key === forcedKey) continue;
                        if (!selectedValue) continue;
                        if (getRuleAttributeValue(rule, key) !== selectedValue) {
                            return false;
                        }
                    }

                    if (forcedKey && forcedValue) {
                        return getRuleAttributeValue(rule, forcedKey) === forcedValue;
                    }

                    return true;
                });
            };

            const normalizeLookupValue = (value) => String(value || '').trim().toLowerCase();

            const findAttributeKey = (needles) => {
                const normalizedNeedles = needles.map((needle) => normalizeLookupValue(needle));
                const keys = [...new Set(attributeValueNodes.map((node) => String(node.getAttribute('data-attribute-key') || '').trim()).filter(Boolean))];
                return keys.find((key) => {
                    const normalizedKey = normalizeLookupValue(key);
                    return normalizedNeedles.some((needle) => normalizedKey.includes(needle));
                }) || null;
            };

            const getOptionLabelByValue = (attributeKey) => {
                const map = new Map();
                optionButtons
                    .filter((button) => (button.getAttribute('data-attribute-key') || '').trim() === attributeKey)
                    .forEach((button) => {
                        const value = (button.getAttribute('data-option-value') || '').trim();
                        const labelNode = button.querySelector('span:last-child');
                        const label = (labelNode ? labelNode.textContent : button.textContent || '').trim();
                        if (value !== '' && label !== '') {
                            map.set(value, label);
                        }
                    });
                return map;
            };

            const isUsedConditionOption = (label, value) => {
                const normalizedLabel = normalizeLookupValue(label);
                const normalizedValue = normalizeLookupValue(value);
                return normalizedLabel.includes('مستعمل')
                    || normalizedLabel.includes('used')
                    || normalizedLabel.includes('pre-loved')
                    || normalizedValue.includes('used')
                    || normalizedValue.includes('pre-loved')
                    || normalizedValue.includes('mosta')
                    || normalizedValue.includes('musta');
            };

            const updateConditionAvailabilityHint = () => {
                if (!conditionAvailabilityHint || !hasVariations) return;

                const conditionKey = findAttributeKey(['condition', 'حاله', 'حالة']);
                const sizeKey = findAttributeKey(['size', 'مقاس']);

                if (!conditionKey || !sizeKey) {
                    conditionAvailabilityHint.classList.remove('is-visible');
                    conditionAvailabilityHint.innerHTML = '';
                    return;
                }

                const sizeLabelMap = getOptionLabelByValue(sizeKey);
                const usedConditionButtons = optionButtons.filter((button) => {
                    if ((button.getAttribute('data-attribute-key') || '').trim() !== conditionKey) {
                        return false;
                    }
                    const value = (button.getAttribute('data-option-value') || '').trim();
                    const labelNode = button.querySelector('span:last-child');
                    const label = (labelNode ? labelNode.textContent : button.textContent || '').trim();
                    return isUsedConditionOption(label, value);
                });

                if (usedConditionButtons.length === 0) {
                    conditionAvailabilityHint.classList.remove('is-visible');
                    conditionAvailabilityHint.innerHTML = '';
                    return;
                }

                const warnings = [];
                usedConditionButtons.forEach((button) => {
                    if (!button.disabled) return;

                    const conditionValue = (button.getAttribute('data-option-value') || '').trim();
                    if (!conditionValue) return;

                    const labelNode = button.querySelector('span:last-child');
                    const conditionLabel = (labelNode ? labelNode.textContent : button.textContent || '').trim();

                    const availableSizes = variationRules
                        .filter((rule) => {
                            const stockStatus = String(rule.stock_status || 'instock').toLowerCase();
                            if (stockStatus !== 'instock') return false;
                            return getRuleAttributeValue(rule, conditionKey) === conditionValue;
                        })
                        .map((rule) => getRuleAttributeValue(rule, sizeKey))
                        .filter((value) => value !== '')
                        .filter((value, index, array) => array.indexOf(value) === index)
                        .map((value) => sizeLabelMap.get(value) || value.toUpperCase());

                    if (availableSizes.length === 0) return;

                    if (currentLocale === 'en') {
                        warnings.push(`<strong>${conditionLabel}</strong> is available only in: ${availableSizes.join(', ')}`);
                    } else {
                        warnings.push(`<strong>${conditionLabel}</strong> متاحة فقط في المقاسات: ${availableSizes.join('، ')}`);
                    }
                });

                if (warnings.length === 0) {
                    conditionAvailabilityHint.classList.remove('is-visible');
                    conditionAvailabilityHint.innerHTML = '';
                    return;
                }

                const intro = currentLocale === 'en' ? 'Note:' : 'تنبيه:';
                conditionAvailabilityHint.innerHTML = `<strong>${intro}</strong> ${warnings.join('<br>')}`;
                conditionAvailabilityHint.classList.add('is-visible');
            };

            const tabCache = new Map();

            const getTabUrl = (tab) => `${@json($localePrefix)}/item/${encodeURIComponent(productSlug)}/tabs/${encodeURIComponent(tab)}`;
            const getReportUrl = () => `${@json($localePrefix)}/item/${encodeURIComponent(productSlug)}/report`;
            const getReviewUrl = () => `${@json($localePrefix)}/item/${encodeURIComponent(productSlug)}/review`;
            const getWishlistAddUrl = () => `${@json($localePrefix)}/item/${encodeURIComponent(productSlug)}/wishlist/add`;
            const getWishlistCountUrl = () => `${@json($localePrefix)}/item/wishlist/count`;
            const getWishlistItemsUrl = () => `${@json($localePrefix)}/item/wishlist/items`;
            const getWishlistBridgeUrl = () => '/wishlist-bridge.php';

            const addToWishlistViaBridge = async () => {
                if (productId <= 0) {
                    throw new Error(wishlistAddFailedText);
                }

                const params = new URLSearchParams();
                params.set('action', 'add');
                params.set('pid', String(productId));

                const response = await fetch(getWishlistBridgeUrl(), {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: params.toString(),
                });

                if (!response.ok) {
                    throw new Error(wishlistAddFailedText);
                }

                const result = await response.json();
                if (!result || !result.success) {
                    throw new Error((result && result.message) ? result.message : wishlistAddFailedText);
                }

                const nextCount = Math.max(0, Number(result.count || 0));
                const shouldAnimate = nextCount > currentWishlistCount;
                setWishlistCount(nextCount);
                if (shouldAnimate) {
                    animateWishlistPlusOne();
                }

                loadWishlistItems(true).catch(() => {});
                helpText.textContent = String(result.message || addedToWishlistText);
            };

            const renderTabLoading = () => {
                if (!tabsBody) return;
                tabsBody.innerHTML = `<div class="product-tabs-loading">${tabLoadingText}</div>`;
            };

            const renderTabError = () => {
                if (!tabsBody) return;
                tabsBody.innerHTML = `<div class="product-tabs-loading">${tabLoadFailedText}</div>`;
            };

            const setActiveTabButton = (tab) => {
                tabButtons.forEach((button) => {
                    const isActive = (button.getAttribute('data-product-tab') || '') === tab;
                    button.classList.toggle('is-active', isActive);
                    button.setAttribute('aria-selected', isActive ? 'true' : 'false');
                });
            };

            const loadTabContent = async (tab, forceReload = false) => {
                if (!tabsBody) return;

                setActiveTabButton(tab);

                if (!forceReload && tabCache.has(tab)) {
                    tabsBody.innerHTML = tabCache.get(tab) || '';
                    return;
                }

                renderTabLoading();

                try {
                    const response = await fetch(getTabUrl(tab), {
                        method: 'GET',
                        credentials: 'same-origin',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    });

                    const result = await response.json();
                    if (!response.ok || !result || !result.success) {
                        throw new Error('tab_failed');
                    }

                    const html = String(result.html || '').trim();
                    tabCache.set(tab, html);
                    tabsBody.innerHTML = html !== '' ? html : `<div class="product-tabs-loading">${tabLoadFailedText}</div>`;
                } catch (error) {
                    renderTabError();
                }
            };

            if (tabButtons.length > 0) {
                tabButtons.forEach((button) => {
                    button.addEventListener('click', () => {
                        const tab = (button.getAttribute('data-product-tab') || '').trim();
                        if (!tab) return;
                        loadTabContent(tab);
                    });
                });

                loadTabContent('description');
            }

            document.addEventListener('click', (event) => {
                const trigger = event.target.closest('[data-open-review-modal]');
                if (!trigger) return;
                event.preventDefault();
                openReviewModal();
            });

            const setReportMessage = (message, isSuccess = false) => {
                if (!reportMessage) return;
                reportMessage.textContent = message;
                reportMessage.style.color = isSuccess ? '#197A3A' : 'var(--muted)';
            };

            const openReportModal = () => {
                if (!reportModal) return;
                reportModal.classList.add('is-open');
                reportModal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            };

            const closeReportModal = () => {
                if (!reportModal) return;
                reportModal.classList.remove('is-open');
                reportModal.setAttribute('aria-hidden', 'true');
                const keepLocked = (miniCart && miniCart.classList.contains('is-open'))
                    || (modal && modal.classList.contains('is-open'));
                document.body.style.overflow = keepLocked ? 'hidden' : '';
            };

            if (reportOpenBtn) {
                reportOpenBtn.addEventListener('click', openReportModal);
            }

            if (reportModalClosers.length > 0) {
                reportModalClosers.forEach((node) => node.addEventListener('click', closeReportModal));
            }

            const setReviewMessage = (message, isSuccess = false) => {
                if (!reviewMessage) return;
                reviewMessage.textContent = message;
                reviewMessage.style.color = isSuccess ? '#197A3A' : 'var(--muted)';
            };

            const paintReviewStars = (rating) => {
                const value = Math.max(0, Math.min(5, Number(rating) || 0));
                reviewStars.forEach((starButton) => {
                    const starValue = Number(starButton.getAttribute('data-review-star') || 0);
                    const active = starValue <= value;
                    starButton.classList.toggle('is-active', active);
                    starButton.textContent = active ? '★' : '☆';
                });
                if (reviewRatingInput) {
                    reviewRatingInput.value = String(value);
                }
            };

            const openReviewModal = () => {
                if (!reviewModal) return;
                reviewModal.classList.add('is-open');
                reviewModal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            };

            const closeReviewModal = () => {
                if (!reviewModal) return;
                reviewModal.classList.remove('is-open');
                reviewModal.setAttribute('aria-hidden', 'true');
                const keepLocked = (miniCart && miniCart.classList.contains('is-open'))
                    || (modal && modal.classList.contains('is-open'))
                    || (reportModal && reportModal.classList.contains('is-open'));
                document.body.style.overflow = keepLocked ? 'hidden' : '';
            };

            if (reviewModalClosers.length > 0) {
                reviewModalClosers.forEach((node) => node.addEventListener('click', closeReviewModal));
            }

            if (reviewStars.length > 0) {
                reviewStars.forEach((starButton) => {
                    starButton.addEventListener('click', () => {
                        const value = Number(starButton.getAttribute('data-review-star') || 0);
                        paintReviewStars(value);
                    });
                });
            }

            if (reviewForm) {
                reviewForm.addEventListener('submit', async (event) => {
                    event.preventDefault();
                    if (!reviewSubmitBtn) return;

                    const ratingValue = Number(reviewRatingInput ? reviewRatingInput.value : 0) || 0;
                    if (ratingValue < 1) {
                        setReviewMessage(currentLocale === 'en' ? 'Please select a rating first.' : 'يرجى اختيار التقييم أولاً.', false);
                        return;
                    }

                    const formData = new FormData(reviewForm);
                    const params = new URLSearchParams();
                    formData.forEach((value, key) => params.append(key, String(value || '').trim()));

                    reviewSubmitBtn.disabled = true;
                    setReviewMessage(tabLoadingText, false);

                    try {
                        const response = await fetch(getReviewUrl(), {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: params.toString(),
                        });

                        const result = await response.json();
                        if (!response.ok || !result || !result.success) {
                            throw new Error((result && result.message) ? result.message : tabLoadFailedText);
                        }

                        setReviewMessage(String(result.message || ''), true);
                        reviewForm.reset();
                        paintReviewStars(0);
                        tabCache.delete('reviews');
                        await loadTabContent('reviews', true);
                        setTimeout(() => {
                            closeReviewModal();
                        }, 900);
                    } catch (error) {
                        const message = (error && error.message) ? error.message : tabLoadFailedText;
                        setReviewMessage(message, false);
                    } finally {
                        reviewSubmitBtn.disabled = false;
                    }
                });
            }

            paintReviewStars(0);

            if (reportForm) {
                reportForm.addEventListener('submit', async (event) => {
                    event.preventDefault();

                    if (!reportSubmitBtn) return;

                    const formData = new FormData(reportForm);
                    const params = new URLSearchParams();
                    formData.forEach((value, key) => params.append(key, String(value || '').trim()));

                    reportSubmitBtn.disabled = true;
                    setReportMessage(tabLoadingText, false);

                    try {
                        const response = await fetch(getReportUrl(), {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: params.toString(),
                        });

                        const result = await response.json();
                        if (!response.ok || !result || !result.success) {
                            throw new Error((result && result.message) ? result.message : tabLoadFailedText);
                        }

                        setReportMessage(String(result.message || ''), true);
                        reportForm.reset();
                        setTimeout(() => {
                            closeReportModal();
                        }, 900);
                    } catch (error) {
                        const message = (error && error.message) ? error.message : tabLoadFailedText;
                        setReportMessage(message, false);
                    } finally {
                        reportSubmitBtn.disabled = false;
                    }
                });
            }

            const refreshOptionAvailability = () => {
                if (!selectorsWrap) return;

                const selected = getSelected();
                const keys = [...new Set(optionButtons.map((button) => (button.getAttribute('data-attribute-key') || '').trim()).filter(Boolean))];

                keys.forEach((attributeKey) => {
                    const groupButtons = optionButtons.filter((button) => (button.getAttribute('data-attribute-key') || '').trim() === attributeKey);
                    groupButtons.forEach((button) => {
                        const optionValue = (button.getAttribute('data-option-value') || '').trim();
                        const hasCompatible = findCompatibleRules(selected, attributeKey, optionValue).length > 0;
                        button.disabled = !hasCompatible;
                        button.classList.toggle('is-disabled', !hasCompatible);
                    });

                    const inputNode = selectorsWrap.querySelector(`input[data-attribute-key="${CSS.escape(attributeKey)}"]`);
                    if (!inputNode) return;

                    const currentValue = (inputNode.value || '').trim();
                    if (!currentValue) {
                        const enabledButtons = groupButtons.filter((button) => !button.disabled);
                        if (enabledButtons.length === 1) {
                            const autoValue = (enabledButtons[0].getAttribute('data-option-value') || '').trim();
                            inputNode.value = autoValue;
                            setActiveOptionButton(attributeKey, autoValue);
                        }
                        return;
                    }

                    const currentButton = groupButtons.find((button) => (button.getAttribute('data-option-value') || '').trim() === currentValue);
                    if (!currentButton || currentButton.disabled) {
                        inputNode.value = '';
                        setActiveOptionButton(attributeKey, '');
                    }
                });
            };

            const validateVariation = () => {
                syncPostedAttributes();

                if (!hasVariations) {
                    addToCartBtn.disabled = false;
                    variationIdInput.value = '0';
                    updateDisplayedPrice();
                    if (helpText.textContent === chooseOptionsText || helpText.textContent === outOfStockText) {
                        helpText.textContent = '';
                    }
                    return;
                }

                refreshOptionAvailability();
                syncPostedAttributes();
                updateConditionAvailabilityHint();

                const selected = getSelected();
                const selectedInStockRules = findCompatibleRules(selected);

                if (selectedInStockRules.length === 1) {
                    updateDisplayedPrice(selectedInStockRules[0]);
                } else {
                    updateDisplayedPrice();
                }

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
                    updateDisplayedPrice(matched);
                    return;
                }

                variationIdInput.value = String(matched.variation_id || 0);
                addToCartBtn.disabled = false;
                updateDisplayedPrice(matched);
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
                if (miniCartView) miniCartView.href = `${@json($localePrefix)}/cart`;
                if (miniCartCheckout) miniCartCheckout.href = wpCheckoutUrl;

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

            if (addToWishlistBtn) {
                addToWishlistBtn.addEventListener('click', async () => {
                    if (isAddingToWishlist) return;

                    const alreadyAdded = await isCurrentProductAlreadyInWishlist();
                    if (alreadyAdded) {
                        helpText.textContent = wishlistAlreadyAddedText;
                        return;
                    }

                    const originalText = addToWishlistBtn.textContent;
                    isAddingToWishlist = true;
                    addToWishlistBtn.disabled = true;
                    addToWishlistBtn.textContent = '...';

                    try {
                        await addToWishlistAjax();
                    } catch (error) {
                        try {
                            await addToWishlistViaBridge();
                        } catch (fallbackError) {
                            helpText.textContent = (fallbackError && fallbackError.message) ? fallbackError.message : wishlistAddFailedText;
                        }
                    } finally {
                        isAddingToWishlist = false;
                        addToWishlistBtn.disabled = false;
                        addToWishlistBtn.textContent = originalText;
                    }
                });
            }

            if (wishlistTrigger) {
                wishlistTrigger.addEventListener('click', async (event) => {
                    event.preventDefault();
                    if (!wishlistDropdown) return;

                    if (wishlistDropdown.classList.contains('is-open')) {
                        closeWishlistDropdown();
                        return;
                    }

                    await openWishlistDropdown();
                });
            }

            document.addEventListener('click', (event) => {
                if (!wishlistDropdown || !wishlistTrigger) return;
                const insideTrigger = wishlistTrigger.contains(event.target);
                const insideDropdown = wishlistDropdown.contains(event.target);
                if (!insideTrigger && !insideDropdown) {
                    closeWishlistDropdown();
                }
            });

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
            setWishlistCount(currentWishlistCount);
            refreshWishlistCount(false);

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

                    if (reportModal && reportModal.classList.contains('is-open')) {
                        closeReportModal();
                    }

                    if (reviewModal && reviewModal.classList.contains('is-open')) {
                        closeReviewModal();
                    }

                    if (wishlistDropdown && wishlistDropdown.classList.contains('is-open')) {
                        closeWishlistDropdown();
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
