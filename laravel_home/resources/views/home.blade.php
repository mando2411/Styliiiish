<!DOCTYPE html>
@php
    $currentLocale = $currentLocale ?? 'ar';
    $localePrefix = $localePrefix ?? '/ar';
    $isEnglish = $currentLocale === 'en';

    $translations = [
        'ar' => [
            'title' => 'ستايلش | فساتين سهرة وزفاف في مصر',
            'meta_desc' => 'ستايلش: متجر فساتين سهرة وزفاف وخطوبة في مصر مع خصومات يومية حتى 50%، شحن سريع داخل مصر، وتجربة شراء احترافية وآمنة.',
            'contact_anytime' => 'اتصلي بنا في أي وقت:',
            'daily_deals' => '⚡ خصومات يومية',
            'facebook' => 'فيسبوك',
            'instagram' => 'إنستجرام',
            'google' => 'جوجل',
            'brand_tag' => 'لأن كل امرأة تستحق أن تتألق',
            'nav_home' => 'الرئيسية',
            'nav_shop' => 'المتجر',
            'nav_marketplace' => 'الماركت بليس',
            'nav_sell' => 'بيعي فستانك',
            'nav_blog' => 'المدونة',
            'nav_contact' => 'تواصل معنا',
            'search_placeholder' => 'ابحثي عن فستانك...',
            'search_btn' => 'بحث',
            'account' => 'حسابي',
            'wishlist' => 'قائمة الأمنيات',
            'cart' => 'السلة',
            'login_title' => 'تسجيل الدخول',
            'login_subtitle' => 'أكملي إلى حسابك لإدارة الطلبات والمفضلة بسهولة.',
            'login_username' => 'اسم المستخدم أو البريد الإلكتروني',
            'login_password' => 'كلمة المرور',
            'remember_me' => 'تذكرني',
            'sign_in' => 'تسجيل الدخول',
            'sign_in_google' => 'تسجيل الدخول عبر Google',
            'register' => 'إنشاء حساب جديد',
            'forgot_password' => 'نسيت كلمة المرور؟',
            'wishlist_loading' => 'جاري تحميل المفضلة…',
            'wishlist_empty' => 'لا توجد منتجات في المفضلة حالياً.',
            'view_all_wishlist' => 'عرض كل المفضلة',
            'go_to_product' => 'عرض المنتج',
            'cart_title' => 'سلة التسوق',
            'close' => 'إغلاق',
            'subtotal' => 'الإجمالي الفرعي',
            'view_cart' => 'عرض السلة',
            'checkout' => 'إتمام الطلب',
            'cart_empty' => 'العربة فارغة حاليًا.',
            'loading_cart' => 'جاري تحميل العربة…',
            'remove' => 'حذف',
            'qty_short' => 'الكمية',
            'start_selling' => 'ابدئي البيع',
            'promo_line' => 'لأن كل امرأة تستحق أن تتألق • خصومات تصل إلى 50% • توصيل داخل مصر خلال 2–10 أيام عمل',
            'hero_badge' => '✨ مجموعة حصرية بتحديثات يومية',
            'hero_title' => 'اختاري فستان أحلامك لمناسبتك القادمة بأفضل قيمة في مصر',
            'hero_lead' => 'موديلات سهرة وزفاف وخطوبة مختارة بعناية، مع عروض قوية وتجربة شراء سريعة من منصة موثوقة.',
            'hero_p1' => '✔️ خصومات تصل إلى 50% على موديلات مختارة',
            'hero_p2' => '✔️ توصيل داخل مصر خلال 2–10 أيام',
            'hero_p3' => '✔️ خيارات متنوعة للمقاسات والستايلات',
            'hero_p4' => '✔️ منصة موثوقة للبيع والشراء',
            'shop_now' => 'تسوقي الفساتين الآن',
            'sell_now' => 'بيعي فستانك الآن',
            'ship_fast' => '🚚 شحن سريع',
            'secure_pay' => '💳 دفع آمن',
            'clear_policy' => '🔄 سياسات واضحة',
            'available_products' => 'منتج متاح الآن',
            'discounted_products' => 'منتج عليه خصم',
            'high_trust' => 'ثقة عالية',
            'support_before_after' => 'خدمة ودعم قبل وبعد الطلب',
            'why_sty' => 'لماذا Styliiiish؟',
            'why_note' => 'مزيج بين جودة التصميم وسهولة الشراء، مع روابط وسياسات واضحة لبناء ثقة حقيقية.',
            'why_1' => '✓ منتجات منشورة مباشرة من متجر ووردبريس لحظيًا',
            'why_2' => '✓ فساتين سهرة وزفاف وموديلات محتشمة بألوان ومقاسات متنوعة',
            'why_3' => '✓ عروض يومية وأسعار مميزة على موديلات مختارة',
            'why_4' => '✓ خدمة داخل مصر مع سياسات شحن واسترجاع واضحة',
            'available_now' => 'منتج متاح',
            'on_discount' => 'منتج عليه خصم',
            'min_price' => 'أقل سعر حالي',
            'max_price' => 'أعلى سعر حالي',
            'featured_title' => 'منتجات مختارة لك الآن',
            'featured_sub' => 'أحدث الفساتين من المتجر مع إبراز العروض والخصومات',
            'view_all' => 'عرض كل المنتجات',
            'featured_badge' => 'مختارات مميزة',
            'discount_badge' => 'خصم',
            'available_label' => 'متوفر الآن',
            'delivery_label' => 'توصيل سريع',
            'contact_for_price' => 'تواصل لمعرفة السعر',
            'save_prefix' => 'وفّري',
            'order_now' => 'اطلبي الآن',
            'view_product' => 'معاينة المنتج',
            'trust_1_t' => '🚚 شحن سريع داخل مصر',
            'trust_1_d' => 'توصيل الطلبات خلال 2–10 أيام عمل حسب المحافظة.',
            'trust_2_t' => '💬 دعم ومتابعة قبل الشراء',
            'trust_2_d' => 'فريقنا يساعدك تختاري المقاس والموديل الأنسب لمناسبتك.',
            'trust_3_t' => '💸 بيعي فستانك بسهولة',
            'trust_3_d' => 'حوّلي فستانك المستعمل إلى دخل إضافي عبر المنصة.',
            'reviews_title' => 'تجارب حقيقية من Google Reviews',
            'reviews_sub' => 'آراء عملائنا بالصورة كما هي لبناء ثقة كاملة قبل الطلب.',
            'prev' => 'السابق',
            'next' => 'التالي',
            'open_google_reviews' => 'فتح تقييمات Google',
            'review_overlay' => 'عرض على Google',
            'rating' => 'تقييم',
            'no_reviews_now' => 'لا توجد صور مراجعات متاحة الآن.',
            'final_title' => 'جاهزة تتألقي في مناسبتك القادمة؟',
            'final_sub' => 'اكتشفي أجدد الموديلات والعروض الحصرية الآن، أو ارفعي فستانك للبيع في دقائق ووصول أسرع لآلاف المشترين في مصر.',
            'start_shop' => 'ابدئي التسوق',
            'footer_title' => 'ستيليش فاشون هاوس',
            'footer_desc' => 'نعمل بشغف على تقديم أحدث تصاميم الفساتين لتناسب كل مناسبة خاصة بك.',
            'footer_hours' => 'مواعيد العمل: السبت إلى الجمعة من 11:00 صباحًا حتى 7:00 مساءً.',
            'status_label' => 'الحالة',
            'open_now' => 'مفتوح',
            'closed_now' => 'مغلق',
            'open_hours_label' => 'ساعات العمل',
            'open_hours_value' => 'السبت – الجمعة: 11:00 ص – 7:00 م',
            'contact_us' => 'تواصلي معنا',
            'direct_call' => 'اتصال مباشر',
            'quick_links' => 'روابط سريعة',
            'official_info' => 'معلومات رسمية',
            'policies' => 'سياسات وقوانين',
            'about_us' => 'من نحن',
            'privacy' => 'سياسة الخصوصية',
            'terms' => 'الشروط والأحكام',
            'market_policy' => 'سياسة الماركت بليس',
            'refund_policy' => 'سياسة الاسترجاع والاستبدال',
            'faq' => 'الأسئلة الشائعة',
            'shipping_policy' => 'سياسة الشحن والتوصيل',
            'cookies' => 'سياسة ملفات الارتباط',
            'categories' => 'الأقسام',
            'official_address' => '1 شارع نبيل خليل، مدينة نصر، القاهرة، مصر',
            'rights' => 'جميع الحقوق محفوظة © :year Styliiiish | تشغيل وتطوير',
            'footer_note' => 'الصفحة الرئيسية مطورة بـ Laravel ومتصلة ببيانات WordPress مباشرة',
            'home_mini' => 'الرئيسية',
            'shop_mini' => 'المتجر',
            'cart_mini' => 'السلة',
            'account_mini' => 'حسابي',
            'fav_mini' => 'المفضلة',
        ],
        'en' => [
            'title' => 'Styliiiish | Evening, Bridal & Engagement Dresses in Egypt',
            'meta_desc' => 'Styliiiish: Shop evening, bridal, and engagement dresses in Egypt with daily offers up to 50%, fast nationwide shipping, and a secure modern shopping experience.',
            'contact_anytime' => 'Call us anytime:',
            'daily_deals' => '⚡ Daily Deals',
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'google' => 'Google',
            'brand_tag' => 'Because every woman deserves to shine',
            'nav_home' => 'Home',
            'nav_shop' => 'Shop',
            'nav_marketplace' => 'Marketplace',
            'nav_sell' => 'Sell Your Dress',
            'nav_blog' => 'Blog',
            'nav_contact' => 'Contact Us',
            'search_placeholder' => 'Search for your dress...',
            'search_btn' => 'Search',
            'account' => 'My Account',
            'wishlist' => 'Wishlist',
            'cart' => 'Cart',
            'login_title' => 'Sign In',
            'login_subtitle' => 'Access your account to manage orders and wishlist easily.',
            'login_username' => 'Username or Email',
            'login_password' => 'Password',
            'remember_me' => 'Remember me',
            'sign_in' => 'Sign In',
            'sign_in_google' => 'Sign in with Google',
            'register' => 'Create an account',
            'forgot_password' => 'Forgot password?',
            'wishlist_loading' => 'Loading wishlist…',
            'wishlist_empty' => 'No products in wishlist yet.',
            'view_all_wishlist' => 'View full wishlist',
            'go_to_product' => 'View product',
            'cart_title' => 'Shopping Cart',
            'close' => 'Close',
            'subtotal' => 'Subtotal',
            'view_cart' => 'View Cart',
            'checkout' => 'Checkout',
            'cart_empty' => 'Your cart is currently empty.',
            'loading_cart' => 'Loading cart…',
            'remove' => 'Remove',
            'qty_short' => 'Qty',
            'start_selling' => 'Start Selling',
            'promo_line' => 'Because every woman deserves to shine • Up to 50% OFF • Delivery across Egypt in 2–10 business days',
            'hero_badge' => '✨ Exclusive collection with daily updates',
            'hero_title' => 'Find your dream dress for your next occasion at the best value in Egypt',
            'hero_lead' => 'Carefully selected evening, bridal, and engagement dresses with strong offers and a fast shopping experience.',
            'hero_p1' => '✔️ Up to 50% off selected styles',
            'hero_p2' => '✔️ Delivery across Egypt in 2–10 days',
            'hero_p3' => '✔️ Wide range of sizes and styles',
            'hero_p4' => '✔️ Trusted platform for buying and selling',
            'shop_now' => 'Shop Dresses Now',
            'sell_now' => 'Sell Your Dress Now',
            'ship_fast' => '🚚 Fast Shipping',
            'secure_pay' => '💳 Secure Payment',
            'clear_policy' => '🔄 Clear Policies',
            'available_products' => 'Products available now',
            'discounted_products' => 'Discounted products',
            'high_trust' => 'High Trust',
            'support_before_after' => 'Support before and after your order',
            'why_sty' => 'Why Styliiiish?',
            'why_note' => 'A blend of design quality and shopping simplicity with clear policies and trusted links.',
            'why_1' => '✓ Products synced live from WordPress store',
            'why_2' => '✓ Evening, bridal, and modest styles in many colors and sizes',
            'why_3' => '✓ Daily offers and standout pricing on selected styles',
            'why_4' => '✓ Egypt-wide service with clear shipping and return policies',
            'available_now' => 'Available Products',
            'on_discount' => 'Discounted Products',
            'min_price' => 'Current Min Price',
            'max_price' => 'Current Max Price',
            'featured_title' => 'Featured Products for You',
            'featured_sub' => 'Latest dresses from the store with highlighted offers and discounts',
            'view_all' => 'View All Products',
            'featured_badge' => 'Featured Pick',
            'discount_badge' => 'OFF',
            'available_label' => 'In Stock',
            'delivery_label' => 'Fast Delivery',
            'contact_for_price' => 'Contact for Price',
            'save_prefix' => 'Save',
            'order_now' => 'Order Now',
            'view_product' => 'View Product',
            'trust_1_t' => '🚚 Fast Shipping Across Egypt',
            'trust_1_d' => 'Orders delivered within 2–10 business days depending on location.',
            'trust_2_t' => '💬 Pre-purchase Support',
            'trust_2_d' => 'Our team helps you choose the best size and style for your occasion.',
            'trust_3_t' => '💸 Sell Your Dress Easily',
            'trust_3_d' => 'Turn your pre-loved dress into extra income through the platform.',
            'reviews_title' => 'Real Reviews from Google',
            'reviews_sub' => 'Authentic customer review screenshots to build confidence before ordering.',
            'prev' => 'Previous',
            'next' => 'Next',
            'open_google_reviews' => 'Open Google Reviews',
            'review_overlay' => 'View on Google',
            'rating' => 'Review',
            'no_reviews_now' => 'No review images available right now.',
            'final_title' => 'Ready to shine at your next event?',
            'final_sub' => 'Discover the latest styles and exclusive offers now, or list your dress for sale and reach thousands of buyers in Egypt.',
            'start_shop' => 'Start Shopping',
            'footer_title' => 'Styliiiish Fashion House',
            'footer_desc' => 'We are passionate about offering the latest dress designs for every special occasion.',
            'footer_hours' => 'Working hours: Saturday to Friday from 11:00 AM to 7:00 PM.',
            'status_label' => 'Status',
            'open_now' => 'Open',
            'closed_now' => 'Closed',
            'open_hours_label' => 'Open Hours',
            'open_hours_value' => 'Sat – Fri: 11:00 am – 7:00 pm.',
            'contact_us' => 'Contact Us',
            'direct_call' => 'Direct Call',
            'quick_links' => 'Quick Links',
            'official_info' => 'Official Info',
            'policies' => 'Policies & Legal',
            'about_us' => 'About Us',
            'privacy' => 'Privacy Policy',
            'terms' => 'Terms & Conditions',
            'market_policy' => 'Marketplace Policy',
            'refund_policy' => 'Refund & Return Policy',
            'faq' => 'FAQ',
            'shipping_policy' => 'Shipping & Delivery Policy',
            'cookies' => 'Cookie Policy',
            'categories' => 'Categories',
            'official_address' => '1 Nabil Khalil St, Nasr City, Cairo, Egypt',
            'rights' => 'All rights reserved © :year Styliiiish | Powered by',
            'footer_note' => 'Homepage is built with Laravel and connected directly to WordPress data',
            'home_mini' => 'Home',
            'shop_mini' => 'Shop',
            'cart_mini' => 'Cart',
            'account_mini' => 'Account',
            'fav_mini' => 'Wishlist',
        ],
    ];

    $businessTimezone = new DateTimeZone('Africa/Cairo');
    $nowInCairo = new DateTimeImmutable('now', $businessTimezone);
    $currentMinutes = ((int) $nowInCairo->format('H') * 60) + (int) $nowInCairo->format('i');
    $openFromMinutes = 11 * 60;
    $openUntilMinutes = 19 * 60;
    $isOpenNow = $currentMinutes >= $openFromMinutes && $currentMinutes < $openUntilMinutes;
    $wpBaseUrl = rtrim((string) ($wpBaseUrl ?? env('WP_PUBLIC_URL', 'https://styliiiish.com')), '/');
    $canonicalPath = $localePrefix;
    $wpMyAccountUrl = $wpBaseUrl . '/my-account/';
    $wpLoginUrl = $wpMyAccountUrl;
    $wpRegisterUrl = $wpMyAccountUrl . '?register=1';
    $wpForgotPasswordUrl = $wpMyAccountUrl . 'lost-password/';
    $wpCheckoutUrl = $isEnglish ? ($wpBaseUrl . '/en/checkout/') : ($wpBaseUrl . '/ar/الدفع/');
    $reviewsPrevArrow = $isEnglish ? '‹' : '›';
    $reviewsNextArrow = $isEnglish ? '›' : '‹';

    $normalizeBrandText = fn (string $value) => $currentLocale === 'en'
        ? (preg_replace('/ستايلش/iu', 'Styliiiish', $value) ?? $value)
        : (preg_replace('/styliiiish/iu', 'ستايلش', $value) ?? $value);
    $t = fn (string $key) => $normalizeBrandText((string) ($translations[$currentLocale][$key] ?? $translations['ar'][$key] ?? $key));
@endphp
<html lang="{{ $isEnglish ? 'en' : 'ar' }}" dir="{{ $isEnglish ? 'ltr' : 'rtl' }}">
<head>
    @php
        $wpLogo = 'https://styliiiish.com/wp-content/uploads/2025/11/ChatGPT-Image-Nov-2-2025-03_11_14-AM-e1762046066547.png';
        $wpIcon = 'https://styliiiish.com/wp-content/uploads/2025/11/cropped-ChatGPT-Image-Nov-2-2025-03_11_14-AM-e1762046066547.png';
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $t('meta_desc') }}">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <link rel="canonical" href="{{ $wpBaseUrl }}{{ $canonicalPath }}">
    <link rel="alternate" hreflang="ar" href="{{ $wpBaseUrl }}/ar">
    <link rel="alternate" hreflang="en" href="{{ $wpBaseUrl }}/en">
    <link rel="alternate" hreflang="x-default" href="{{ $wpBaseUrl }}/ar">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $isEnglish ? 'Styliiiish' : 'ستايلش' }}">
    <meta property="og:title" content="{{ $t('title') }}">
    <meta property="og:description" content="{{ $t('meta_desc') }}">
    <meta property="og:url" content="{{ $wpBaseUrl }}{{ $canonicalPath }}">
    <meta property="og:image" content="{{ $wpIcon }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $t('title') }}">
    <meta name="twitter:description" content="{{ $t('meta_desc') }}">
    <meta name="twitter:image" content="{{ $wpIcon }}">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ $wpIcon }}">
    <link rel="apple-touch-icon" href="{{ $wpIcon }}">
    <title>{{ $t('title') }}</title>
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
            --primary-2: #b70f1a;
            --secondary: var(--wf-secondary-color);
            --success: #0a8f5b;
            --soft: #ffeef0;
            --footer-bg: #0f1a2a;
            --footer-soft: #b8c2d1;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            -webkit-tap-highlight-color: transparent;
        }

        a { text-decoration: none; color: inherit; }

        .container {
            width: min(1180px, 92%);
            margin: 0 auto;
        }

        .promo {
            background: linear-gradient(90deg, var(--secondary), #24384f);
            color: #fff;
            text-align: center;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 600;
        }

        .topbar {
            background: var(--secondary);
            color: #fff;
            font-size: 13px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        }

        .topbar-inner {
            min-height: 42px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
        }

        .topbar-left,
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .topbar a {
            color: #fff;
            opacity: .92;
        }

        .topbar a:hover {
            opacity: 1;
        }

        .topbar-note {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.14);
            border-radius: 999px;
            padding: 4px 10px;
            font-weight: 700;
            font-size: 12px;
        }

        .lang-switch {
            position: relative;
            display: inline-grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            direction: ltr;
            width: 110px;
            height: 34px;
            background: rgba(255, 255, 255, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.28);
            border-radius: 999px;
            padding: 3px;
            overflow: hidden;
        }

        .lang-switch .lang-indicator {
            position: absolute;
            top: 3px;
            width: calc(50% - 3px);
            height: calc(100% - 6px);
            background: #fff;
            border-radius: 999px;
            transition: .25s ease;
            z-index: 1;
        }

        .lang-switch.is-ar .lang-indicator {
            left: 3px;
        }

        .lang-switch.is-en .lang-indicator {
            right: 3px;
        }

        .lang-switch a {
            position: relative;
            z-index: 2;
            text-align: center;
            font-size: 12px;
            font-weight: 800;
            opacity: .95;
            color: #fff;
            padding: 5px 0;
        }

        .lang-switch a.active {
            color: var(--secondary);
            opacity: 1;
        }

        .main-header {
            background: #fff;
            border-bottom: 1px solid var(--line);
            position: sticky;
            top: 0;
            z-index: 40;
            box-shadow: 0 8px 24px rgba(23, 39, 59, 0.06);
        }

        .main-header-inner {
            min-height: 84px;
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            gap: 16px;
        }

        .brand {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .brand-logo {
            height: 40px;
            width: auto;
            max-width: min(220px, 38vw);
            object-fit: contain;
        }

        .brand-tag {
            color: var(--muted);
            font-size: 12px;
            font-weight: 600;
        }

        .main-nav {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            background: #f9fbff;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 6px;
        }

        .main-nav a {
            color: var(--secondary);
            font-size: 14px;
            font-weight: 700;
            padding: 8px 12px;
            border-radius: 8px;
            transition: .2s ease;
            touch-action: manipulation;
        }

        .main-nav a:hover {
            color: var(--primary);
            background: #fff4f5;
        }

        .main-nav a.active {
            background: #fff4f5;
            color: var(--primary);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .action-account,
        .action-wishlist,
        .action-cart,
        .action-sell {
            white-space: nowrap;
        }

        .search-form {
            display: flex;
            align-items: center;
            height: 40px;
            border: 1px solid var(--line);
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
        }

        .search-input {
            border: 0;
            outline: 0;
            width: 190px;
            padding: 0 12px;
            color: var(--secondary);
            font-size: 13px;
            font-family: inherit;
        }

        .search-btn {
            height: 100%;
            border: 0;
            background: var(--secondary);
            color: #fff;
            padding: 0 12px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
        }

        .search-btn:hover {
            background: #24384f;
        }

        .icon-btn {
            border: 1px solid var(--line);
            background: #fff;
            color: var(--secondary);
            border-radius: 10px;
            min-width: 38px;
            min-height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 10px;
            font-size: 13px;
            font-weight: 700;
            touch-action: manipulation;
        }

        .icon-btn .icon {
            font-size: 16px;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .icon-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .wishlist-trigger-wrap,
        .cart-trigger-wrap {
            position: relative;
        }

        .wishlist-trigger,
        .cart-trigger {
            cursor: pointer;
            font-family: inherit;
        }

        .wishlist-count,
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
            display: none;
        }

        .wishlist-plus-one,
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

        .wishlist-plus-one.show,
        .cart-plus-one.show {
            animation: cartPlusOne .8s ease;
        }

        @keyframes cartPlusOne {
            0% { opacity: 0; transform: translateY(8px); }
            20% { opacity: 1; transform: translateY(0); }
            100% { opacity: 0; transform: translateY(-12px); }
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

        [dir="rtl"] .wishlist-dropdown { right: auto; left: 0; }
        [dir="ltr"] .wishlist-dropdown { left: auto; right: 0; }
        .wishlist-dropdown.is-open { display: block; }

        .wishlist-dropdown-list {
            display: grid;
            gap: 8px;
            max-height: 360px;
            overflow: auto;
        }

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

        .mini-cart {
            position: fixed;
            inset: 0;
            z-index: 110;
            pointer-events: none;
        }

        .mini-cart.is-open { pointer-events: auto; }

        .mini-cart-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(15, 26, 42, 0.52);
            opacity: 0;
            transition: .2s ease;
        }

        .mini-cart.is-open .mini-cart-backdrop { opacity: 1; }

        .mini-cart-panel {
            position: absolute;
            top: 0;
            right: 0;
            width: min(430px, 92vw);
            height: 100%;
            background: #fff;
            border-inline-start: 1px solid var(--line);
            display: grid;
            grid-template-rows: auto 1fr auto;
            transform: translateX(100%);
            transition: .24s ease;
            box-shadow: -10px 0 30px rgba(23, 39, 59, .14);
        }

        .mini-cart.is-open .mini-cart-panel { transform: translateX(0); }
        [dir="rtl"] .mini-cart-panel { right: auto; left: 0; border-inline-start: 0; border-inline-end: 1px solid var(--line); transform: translateX(-100%); }
        [dir="rtl"] .mini-cart.is-open .mini-cart-panel { transform: translateX(0); }

        .mini-cart-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 12px;
            border-bottom: 1px solid var(--line);
        }

        .mini-cart-head h3 { margin: 0; font-size: 17px; color: var(--secondary); }

        .mini-cart-close {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #fff;
            color: var(--secondary);
            padding: 6px 10px;
            cursor: pointer;
            font-family: inherit;
            font-weight: 700;
        }

        .mini-cart-list {
            overflow: auto;
            padding: 12px;
            display: grid;
            gap: 10px;
            align-content: start;
            grid-auto-rows: max-content;
        }

        .mini-cart-item {
            display: grid;
            grid-template-columns: 70px 1fr auto;
            gap: 10px;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 9px;
            align-items: center;
        }

        .mini-cart-item img {
            width: 70px;
            height: 92px;
            object-fit: cover;
            border-radius: 9px;
            background: #f2f2f5;
        }

        .mini-cart-info { min-width: 0; }

        .mini-cart-item h4 {
            margin: 0 0 4px;
            font-size: 13px;
            line-height: 1.35;
            color: var(--secondary);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .mini-cart-meta { font-size: 12px; color: var(--muted); display: flex; gap: 6px; align-items: center; }
        .mini-cart-price { font-size: 12px; color: var(--primary); font-weight: 800; margin-top: 4px; }

        .mini-cart-remove {
            border: 1px solid rgba(var(--wf-main-rgb), .2);
            background: #fff;
            color: var(--primary);
            border-radius: 8px;
            min-width: 34px;
            min-height: 34px;
            font-weight: 800;
            cursor: pointer;
        }

        .mini-cart-remove:hover { background: #ffeff1; }
        .mini-cart-empty { color: var(--muted); font-size: 14px; padding: 8px 0; text-align: center; }
        .mini-cart-loading { color: var(--muted); font-size: 13px; text-align: center; padding: 10px 0; }

        .mini-cart-foot {
            border-top: 1px solid var(--line);
            padding: 12px;
            display: grid;
            gap: 8px;
        }

        .mini-cart-subtotal { font-size: 13px; color: var(--secondary); display: flex; justify-content: space-between; }
        .mini-cart-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        .mini-cart-actions a { min-height: 40px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 800; }
        .mini-cart-view { border: 1px solid var(--line); background: #fff; color: var(--secondary); }
        .mini-cart-checkout { background: var(--primary); color: #fff; }

        .account-trigger {
            cursor: pointer;
            font-family: inherit;
        }

        .auth-modal {
            position: fixed;
            inset: 0;
            z-index: 120;
            pointer-events: none;
        }

        .auth-modal.is-open { pointer-events: auto; }

        .auth-modal-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(15, 26, 42, .54);
            opacity: 0;
            transition: .22s ease;
        }

        .auth-modal.is-open .auth-modal-backdrop { opacity: 1; }

        .auth-modal-panel {
            position: absolute;
            top: 50%;
            left: 50%;
            width: min(450px, 94vw);
            max-height: min(88vh, 760px);
            overflow: auto;
            transform: translate(-50%, calc(-50% + 16px));
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            box-shadow: 0 22px 50px rgba(23, 39, 59, .24);
            padding: 18px;
            transition: .22s ease;
            opacity: 0;
        }

        .auth-modal.is-open .auth-modal-panel {
            transform: translate(-50%, -50%);
            opacity: 1;
        }

        .auth-head {
            display: flex;
            align-items: start;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 14px;
        }

        .auth-title-wrap h3 {
            margin: 0;
            font-size: 22px;
            line-height: 1.2;
            color: var(--secondary);
        }

        .auth-title-wrap p {
            margin: 7px 0 0;
            font-size: 13px;
            color: var(--muted);
        }

        .auth-close {
            border: 1px solid var(--line);
            border-radius: 10px;
            min-width: 36px;
            min-height: 36px;
            background: #fff;
            color: var(--secondary);
            font-size: 18px;
            cursor: pointer;
            line-height: 1;
        }

        .auth-form {
            display: grid;
            gap: 10px;
        }

        .auth-field {
            display: grid;
            gap: 6px;
        }

        .auth-field label {
            font-size: 12px;
            font-weight: 800;
            color: var(--secondary);
        }

        .auth-field input[type="text"],
        .auth-field input[type="password"] {
            min-height: 44px;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 0 12px;
            font-size: 14px;
            color: var(--secondary);
            outline: none;
            font-family: inherit;
            background: #fff;
        }

        .auth-field input[type="text"]:focus,
        .auth-field input[type="password"]:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(var(--wf-main-rgb), .14);
        }

        .auth-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }

        .auth-remember {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--secondary);
            font-size: 13px;
            font-weight: 700;
        }

        .auth-remember input {
            accent-color: var(--primary);
            width: 16px;
            height: 16px;
        }

        .auth-forgot {
            color: var(--primary);
            font-size: 13px;
            font-weight: 700;
        }

        .auth-submit {
            min-height: 44px;
            border: 0;
            border-radius: 10px;
            background: var(--primary);
            color: #fff;
            font-size: 14px;
            font-weight: 800;
            font-family: inherit;
            cursor: pointer;
        }

        .auth-submit:hover {
            background: var(--primary-2);
        }

        .auth-divider {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--muted);
            font-size: 12px;
            margin: 2px 0;
        }

        .auth-divider::before,
        .auth-divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: var(--line);
        }

        .auth-google,
        .auth-register {
            min-height: 44px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 800;
        }

        .auth-google {
            border: 1px solid var(--line);
            color: var(--secondary);
            background: #fff;
        }

        .auth-google:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: #fff8f9;
        }

        .auth-google-wrap {
            display: grid;
            gap: 8px;
        }

        .auth-google-wrap .googlesitekit-sign-in-with-google__frontend-output-button {
            width: 100%;
            min-height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #fff;
            overflow: hidden;
        }

        .auth-google-fallback {
            min-height: 44px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 800;
            border: 1px solid var(--line);
            color: var(--secondary);
            background: #fff;
        }

        .auth-register {
            border: 1px dashed rgba(var(--wf-main-rgb), .38);
            color: var(--primary);
            background: #fff8f9;
        }

        .auth-register:hover {
            background: #ffeef1;
        }

        .header-cta {
            height: 40px;
            padding: 0 14px;
            border-radius: 10px;
            font-size: 13px;
        }

        .hero {
            padding: 56px 0 34px;
        }

        .hero-wrap {
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 24px;
            align-items: stretch;
        }

        .hero-main, .hero-side {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 28px;
            box-shadow: 0 10px 30px rgba(23, 39, 59, 0.06);
        }

        .hero-main {
            position: relative;
            overflow: hidden;
            background: linear-gradient(160deg, #ffffff 0%, #fff8f8 85%);
        }

        .hero-main::before {
            content: "";
            position: absolute;
            width: 360px;
            height: 360px;
            border-radius: 50%;
            background: radial-gradient(circle at center, rgba(213, 21, 34, 0.11), rgba(213, 21, 34, 0));
            top: -170px;
            left: -120px;
            pointer-events: none;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--soft);
            color: var(--primary);
            border-radius: 999px;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 14px;
            position: relative;
            z-index: 1;
        }

        .hero-main h1 {
            margin: 0 0 10px;
            line-height: 1.2;
            font-size: clamp(31px, 4vw, 46px);
            position: relative;
            z-index: 1;
        }

        .lead {
            margin: 0 0 22px;
            color: var(--muted);
            font-size: 18px;
            position: relative;
            z-index: 1;
        }

        .hero-points {
            list-style: none;
            margin: 0 0 16px;
            padding: 0;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px 12px;
            position: relative;
            z-index: 1;
        }

        .hero-points li {
            background: rgba(255, 255, 255, 0.86);
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 8px 10px;
            font-size: 13px;
            font-weight: 700;
            color: var(--secondary);
        }

        .actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 14px;
            position: relative;
            z-index: 1;
        }

        .hero-kpis {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            position: relative;
            z-index: 1;
        }

        .hero-mobile-trust {
            display: none;
        }

        .hero-kpi {
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #fff;
            padding: 10px;
            text-align: center;
        }

        .hero-kpi strong {
            display: block;
            color: var(--primary);
            font-size: 18px;
            line-height: 1.1;
        }

        .hero-kpi span {
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
        }

        .btn {
            padding: 12px 20px;
            border-radius: 12px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: .2s ease;
            touch-action: manipulation;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
        }

        .btn-primary:hover { background: var(--primary-2); }

        .btn-light {
            background: #fff;
            border: 1px solid var(--line);
            color: var(--secondary);
        }

        .btn-light:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .hero-side h3 {
            margin: 0 0 12px;
            font-size: 22px;
        }

        .hero-side-note {
            margin: 0 0 12px;
            color: var(--muted);
            font-size: 14px;
        }

        .list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            gap: 10px;
            color: var(--muted);
        }

        .stats {
            margin-top: 14px;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .stat {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px;
            text-align: center;
        }

        .stat strong {
            display: block;
            font-size: 18px;
            color: var(--primary);
        }

        .stat span {
            font-size: 13px;
            color: var(--muted);
        }

        .section {
            padding: 18px 0 34px;
        }

        .section-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .section-title {
            margin: 0;
            font-size: 24px;
        }

        .section-sub {
            margin: 2px 0 0;
            color: var(--muted);
            font-size: 14px;
        }

        .categories {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
        }

        .chip {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px 14px;
            text-align: center;
            font-weight: 700;
        }

        .chip:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: .25s ease;
            box-shadow: 0 8px 20px rgba(23, 39, 59, 0.04);
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 28px rgba(23, 39, 59, 0.12);
            border-color: rgba(213, 21, 34, 0.35);
        }

        .product-media {
            position: relative;
        }

        .thumb {
            width: 100%;
            aspect-ratio: 3/4;
            object-fit: cover;
            background: #f2f2f5;
            transition: .35s ease;
        }

        .card:hover .thumb {
            transform: scale(1.03);
        }

        .card-badges {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            z-index: 2;
        }

        .badge-chip {
            border-radius: 999px;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: 800;
            line-height: 1;
            backdrop-filter: blur(3px);
            width: fit-content;
        }

        .badge-hot {
            background: rgba(23, 39, 59, 0.86);
            color: #fff;
        }

        .badge-discount {
            background: rgba(213, 21, 34, 0.9);
            color: #fff;
        }

        .content {
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .name {
            margin: 0;
            font-size: 15px;
            line-height: 1.45;
            min-height: 46px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .meta {
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .prices {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 0;
        }

        .price {
            color: var(--primary);
            font-weight: 800;
            font-size: 17px;
        }

        .old {
            color: #8b8b97;
            text-decoration: line-through;
            font-size: 14px;
        }

        .sale {
            display: inline-flex;
            background: #f2fff8;
            color: var(--success);
            border-radius: 999px;
            padding: 5px 10px;
            font-size: 12px;
            font-weight: 700;
        }

        .save {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            padding: 4px 9px;
            border-radius: 999px;
            background: #fff3f4;
            color: var(--primary);
            font-size: 11px;
            font-weight: 800;
        }

        .card-actions {
            margin-top: auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .products-section .grid {
            align-items: stretch;
        }

        .buy {
            margin-top: auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--primary);
            color: #fff;
            padding: 10px;
            border-radius: 10px;
            font-weight: 700;
            min-height: 42px;
        }

        .view-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--line);
            color: var(--secondary);
            border-radius: 10px;
            min-height: 42px;
            font-size: 13px;
            font-weight: 700;
            background: #fff;
        }

        .view-link:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .trust {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .trust-item {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 14px;
        }

        .trust-item h4 {
            margin: 0 0 4px;
        }

        .trust-item p {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
        }

        .reviews-trust {
            margin-top: 4px;
        }

        .reviews-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        .reviews-head h3 {
            margin: 0;
            font-size: clamp(22px, 3vw, 30px);
            color: var(--secondary);
        }

        .reviews-head p {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
        }

        .reviews-slider-wrap {
            position: relative;
        }

        .reviews-slider {
            display: flex;
            direction: ltr;
            gap: 0;
            align-items: flex-start;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            scroll-behavior: smooth;
            padding: 2px 0 8px;
            scrollbar-width: none;
        }

        .reviews-slider::-webkit-scrollbar {
            display: none;
        }

        .reviews-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 40px;
            border: 1px solid var(--line);
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.96);
            color: var(--secondary);
            font-size: 18px;
            font-weight: 900;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 3;
            box-shadow: 0 8px 18px rgba(23, 39, 59, 0.15);
        }

        .reviews-nav:hover {
            border-color: rgba(213, 21, 34, .45);
            color: var(--primary);
        }

        .reviews-nav.prev {
            left: 10px;
        }

        .reviews-nav.next {
            right: 10px;
        }

        .review-shot {
            display: flex;
            position: relative;
            flex: 0 0 100%;
            max-width: 100%;
            scroll-snap-align: start;
            user-select: none;
            align-items: center;
            justify-content: center;
            padding: 14px;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid var(--line);
            background: linear-gradient(180deg, #ffffff 0%, #f7f9fc 100%);
            box-shadow: 0 8px 18px rgba(23, 39, 59, 0.06);
            transition: .2s ease;
        }

        .review-shot:hover {
            transform: translateY(-2px);
            border-color: rgba(213, 21, 34, .3);
            box-shadow: 0 12px 24px rgba(23, 39, 59, 0.12);
        }

        .review-shot img {
            width: 800px;
            max-width: 100%;
            height: 270px;
            object-fit: cover;
            display: block;
            margin: 0 auto;
            border-radius: 10px;
            box-shadow: 0 6px 18px rgba(23, 39, 59, 0.12);
            background: #f2f2f5;
        }

        .review-shot::after {
            content: attr(data-open-label);
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(255, 255, 255, 0.92);
            color: var(--secondary);
            border-radius: 999px;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: 800;
            border: 1px solid rgba(23, 39, 59, 0.14);
        }

        .review-meta {
            position: absolute;
            right: 10px;
            bottom: 10px;
            background: rgba(23, 39, 59, 0.82);
            color: #fff;
            border-radius: 8px;
            padding: 5px 8px;
            font-size: 11px;
            font-weight: 700;
            backdrop-filter: blur(2px);
        }

        .final-cta {
            background: linear-gradient(120deg, var(--secondary), #22354a);
            color: #fff;
            border-radius: 18px;
            padding: 28px;
            margin: 12px auto 46px;
            text-align: center;
        }

        .final-cta h3 {
            margin: 0 0 8px;
            font-size: 30px;
        }

        .final-cta p {
            margin: 0 auto 16px;
            color: #d7e0ed;
            max-width: 740px;
        }

        .final-cta .actions {
            justify-content: center;
            margin-bottom: 0;
        }

        .site-footer {
            margin-top: 8px;
            background: var(--footer-bg);
            color: #fff;
            border-top: 4px solid var(--primary);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr 1fr 1fr;
            gap: 18px;
            padding: 34px 0 22px;
        }

        .footer-brand,
        .footer-col {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 16px;
        }

        .footer-brand-logo {
            width: auto;
            height: 34px;
            max-width: min(220px, 100%);
            object-fit: contain;
            display: block;
            margin-bottom: 12px;
            filter: brightness(0) invert(1);
            opacity: 0.96;
        }

        .footer-brand h4,
        .footer-col h5 {
            margin: 0 0 10px;
            font-size: 18px;
            color: #fff;
        }

        .footer-brand p {
            margin: 0 0 10px;
            color: var(--footer-soft);
            font-size: 14px;
        }

        .footer-status,
        .footer-open-hours {
            margin: 0 0 10px;
            color: var(--footer-soft);
            font-size: 14px;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 3px 9px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            border: 1px solid transparent;
            line-height: 1.2;
        }

        .status-pill.is-open {
            color: var(--success);
            border-color: rgba(10, 143, 91, 0.45);
            background: rgba(10, 143, 91, 0.14);
        }

        .status-pill.is-closed {
            color: var(--primary);
            border-color: rgba(var(--wf-main-rgb), 0.45);
            background: rgba(var(--wf-main-rgb), 0.14);
        }

        .footer-links {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            gap: 7px;
        }

        .footer-links a {
            color: var(--footer-soft);
            font-size: 14px;
            transition: .2s ease;
        }

        .footer-links a:hover {
            color: #fff;
        }

        .footer-brand .footer-contact-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .footer-brand .footer-contact-row a {
            color: #fff;
            background: rgba(213, 21, 34, 0.16);
            border: 1px solid rgba(213, 21, 34, 0.35);
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 700;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.14);
            padding: 12px 0 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: space-between;
            color: var(--footer-soft);
            font-size: 13px;
        }

        .footer-mini-nav {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: center;
            padding-bottom: 18px;
        }

        .footer-mini-nav a {
            color: var(--footer-soft);
            font-size: 13px;
        }

        .footer-mini-nav a:hover {
            color: #fff;
        }

        .footer-note {
            text-align: center;
            color: var(--muted);
            font-size: 13px;
            padding-bottom: 24px;
        }

        @media (max-width: 980px) {
            .main-header-inner {
                grid-template-columns: 1fr;
                padding: 12px 0;
            }

            .brand,
            .main-nav,
            .header-actions {
                justify-content: center;
                text-align: center;
            }

            .search-input {
                width: 220px;
            }

            .main-nav {
                overflow-x: auto;
                flex-wrap: nowrap;
                justify-content: flex-start;
                scrollbar-width: thin;
            }

            .main-nav a {
                white-space: nowrap;
            }

            .hero-wrap,
            .categories,
            .grid,
            .trust {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .reviews-nav {
                width: 36px;
                height: 36px;
            }

            .hero-points,
            .hero-kpis {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .footer-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .container {
                width: min(1180px, 94%);
            }

            .hero,
            .section {
                padding-top: 14px;
            }

            .promo {
                font-size: 12px;
                line-height: 1.45;
                padding: 8px 12px;
            }

            .topbar-inner {
                justify-content: center;
                min-height: 36px;
            }

            .topbar-left {
                display: none;
            }

            .topbar-right {
                width: 100%;
                justify-content: center;
                gap: 8px;
            }

            .topbar-right strong {
                font-size: 12px;
            }

            .main-header {
                box-shadow: 0 6px 18px rgba(23, 39, 59, 0.08);
            }

            .main-header-inner {
                grid-template-columns: 1fr auto;
                gap: 8px;
                min-height: auto;
                padding: 10px 0;
            }

            .brand {
                align-items: center;
                text-align: center;
            }

            .brand-logo {
                height: 34px;
                max-width: 190px;
            }

            .brand-tag {
                font-size: 11px;
            }

            .main-nav {
                grid-column: 1 / -1;
                margin-top: 4px;
                border-radius: 10px;
                padding: 5px;
                gap: 6px;
                -webkit-overflow-scrolling: touch;
                scroll-snap-type: x proximity;
                scrollbar-width: none;
            }

            .main-nav::-webkit-scrollbar {
                display: none;
            }

            .main-nav a {
                font-size: 12px;
                padding: 7px 10px;
                scroll-snap-align: start;
            }

            .header-actions {
                justify-content: flex-end;
                gap: 6px;
                flex-wrap: nowrap;
            }

            .search-form {
                display: none;
            }

            .icon-btn {
                min-width: 34px;
                min-height: 34px;
                padding: 0 8px;
                font-size: 12px;
                border-radius: 8px;
            }

            .icon-btn .icon {
                font-size: 15px;
            }

            .action-wishlist,
            .action-sell {
                display: none;
            }

            .action-account,
            .action-cart {
                min-width: 46px;
                justify-content: center;
            }

            .search-form {
                width: 100%;
            }

            .search-input {
                width: 100%;
            }

            .hero {
                padding: 18px 0 20px;
            }

            .hero-wrap {
                gap: 12px;
            }

            .hero-main,
            .hero-side {
                border-radius: 14px;
                padding: 16px;
                box-shadow: 0 8px 20px rgba(23, 39, 59, 0.05);
            }

            .hero-main {
                border-top: 3px solid var(--primary);
                background: linear-gradient(165deg, #ffffff 0%, #fff2f3 100%);
            }

            .hero-side {
                border-top: 3px solid var(--secondary);
                background: #fff;
            }

            .badge {
                font-size: 12px;
                padding: 7px 10px;
                margin-bottom: 10px;
            }

            .hero-main h1 {
                font-size: clamp(24px, 7.2vw, 31px);
                margin-bottom: 8px;
                line-height: 1.25;
            }

            .lead {
                font-size: 15px;
                margin-bottom: 14px;
            }

            .hero-points li {
                font-size: 12px;
                padding: 8px;
            }

            .actions {
                margin-bottom: 10px;
            }

            .hero-main .actions .btn {
                width: 100%;
                min-height: 44px;
                font-size: 14px;
            }

            .hero-main .actions .btn-primary {
                box-shadow: 0 8px 16px rgba(213, 21, 34, 0.22);
            }

            .hero-kpi {
                padding: 9px;
            }

            .hero-kpi strong {
                font-size: 16px;
            }

            .hero-kpi span {
                font-size: 11px;
            }

            .hero-kpis {
                display: flex;
                overflow-x: auto;
                gap: 8px;
                padding-bottom: 4px;
                scroll-snap-type: x proximity;
                scrollbar-width: none;
            }

            .hero-kpis::-webkit-scrollbar {
                display: none;
            }

            .hero-kpi {
                min-width: 118px;
                flex: 0 0 auto;
                scroll-snap-align: start;
            }

            .hero-mobile-trust {
                margin-top: 6px;
                display: flex;
                overflow-x: auto;
                gap: 8px;
                scroll-snap-type: x proximity;
                scrollbar-width: none;
            }

            .hero-mobile-trust::-webkit-scrollbar {
                display: none;
            }

            .hero-mobile-trust span {
                flex: 0 0 auto;
                scroll-snap-align: start;
                padding: 7px 10px;
                border-radius: 999px;
                background: #fff;
                border: 1px solid var(--line);
                font-size: 11px;
                font-weight: 700;
                color: var(--secondary);
                white-space: nowrap;
            }

            .hero-side h3 {
                font-size: 20px;
                margin-bottom: 8px;
            }

            .hero-side-note,
            .list li {
                font-size: 13px;
            }

            .stats {
                gap: 8px;
            }

            .stat {
                padding: 10px;
            }

            .stat strong {
                font-size: 16px;
            }

            .section {
                padding: 10px 0 22px;
            }

            .section-head {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
                margin-bottom: 10px;
            }

            .section-title {
                font-size: 21px;
            }

            .section-sub {
                font-size: 13px;
            }

            .section-head .btn {
                width: 100%;
                min-height: 42px;
                justify-content: center;
            }

            .categories {
                display: flex;
                overflow-x: auto;
                gap: 8px;
                padding-bottom: 6px;
                scroll-snap-type: x proximity;
                scrollbar-width: none;
            }

            .categories::-webkit-scrollbar {
                display: none;
            }

            .chip {
                flex: 0 0 auto;
                white-space: nowrap;
                padding: 10px 12px;
                font-size: 13px;
                scroll-snap-align: start;
            }

            .products-section .grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
            }

            .reviews-nav {
                display: none;
            }

            .card {
                border-radius: 14px;
            }

            .card-badges {
                top: 8px;
                right: 8px;
            }

            .badge-chip {
                font-size: 10px;
                padding: 5px 8px;
            }

            .products-section .content {
                padding: 10px;
                gap: 6px;
            }

            .products-section .name {
                min-height: auto;
                font-size: 13px;
                line-height: 1.4;
                -webkit-line-clamp: 2;
            }

            .products-section .meta {
                font-size: 11px;
            }

            .products-section .price {
                font-size: 15px;
            }

            .products-section .old {
                font-size: 12px;
            }

            .products-section .sale {
                font-size: 10px;
                padding: 4px 8px;
            }

            .products-section .save {
                font-size: 10px;
                padding: 4px 8px;
            }

            .products-section .card-actions {
                grid-template-columns: 1fr;
                gap: 6px;
            }

            .products-section .buy {
                min-height: 42px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 13px;
            }

            .products-section .view-link {
                min-height: 40px;
                font-size: 12px;
            }

            .trust-item {
                padding: 12px;
            }

            .trust-item h4 {
                font-size: 16px;
            }

            .final-cta {
                border-radius: 14px;
                padding: 18px;
                margin: 8px 0 24px;
            }

            .final-cta h3 {
                font-size: 24px;
            }

            .final-cta p {
                font-size: 14px;
            }

            .footer-grid {
                gap: 14px;
                padding: 22px 0 14px;
            }

            .footer-brand,
            .footer-col {
                padding: 12px;
            }

            .footer-brand h4,
            .footer-col h5 {
                font-size: 17px;
                margin-bottom: 6px;
            }

            .footer-links a {
                display: inline-flex;
                padding: 2px 0;
            }

            .footer-bottom {
                flex-direction: column;
                align-items: flex-start;
                gap: 6px;
                padding: 10px 0 14px;
            }

            .footer-mini-nav {
                justify-content: flex-start;
                overflow-x: auto;
                white-space: nowrap;
                scrollbar-width: none;
                padding-bottom: 12px;
            }

            .footer-mini-nav::-webkit-scrollbar {
                display: none;
            }

            .footer-note {
                font-size: 12px;
                padding-bottom: 18px;
            }

            .hero-wrap,
            .categories,
            .trust,
            .stats,
            .footer-grid {
                grid-template-columns: 1fr;
            }

            .hero-points,
            .hero-kpis {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 390px) {
            .action-account,
            .action-cart {
                min-width: 42px;
                font-size: 11px;
            }

            .brand-logo {
                height: 30px;
                max-width: 165px;
            }

            .main-nav a {
                font-size: 11px;
                padding: 6px 9px;
            }

            .products-section .grid {
                grid-template-columns: 1fr;
            }

            .products-section .buy,
            .products-section .view-link {
                min-height: 38px;
            }
        }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="container topbar-inner">
            <div class="topbar-right">
                <strong>{{ $t('contact_anytime') }}</strong>
                <a href="tel:+201050874255">+20 010 5087 4255</a>
            </div>
            <div class="topbar-left">
                <div class="lang-switch {{ $isEnglish ? 'is-en' : 'is-ar' }}" aria-label="Language Switcher">
                    <span class="lang-indicator" aria-hidden="true"></span>
                    <a class="{{ $currentLocale === 'ar' ? 'active' : '' }}" href="/ar">AR</a>
                    <a class="{{ $currentLocale === 'en' ? 'active' : '' }}" href="/en">EN</a>
                </div>
                <span class="topbar-note">{{ $t('daily_deals') }}</span>
                <a href="https://www.facebook.com/Styliiish.Egypt/" target="_blank" rel="noopener">{{ $t('facebook') }}</a>
                <a href="https://www.instagram.com/styliiish.egypt/" target="_blank" rel="noopener">{{ $t('instagram') }}</a>
                <a href="https://g.page/styliish" target="_blank" rel="noopener">{{ $t('google') }}</a>
            </div>
        </div>
    </div>

    <header class="main-header">
        <div class="container main-header-inner">
            <a class="brand" href="{{ $localePrefix }}">
                <img class="brand-logo" src="{{ $wpLogo }}" alt="Styliiiish" onerror="this.onerror=null;this.src='/brand/logo.png';">
                <span class="brand-tag">{{ $t('brand_tag') }}</span>
            </a>

            <nav class="main-nav" aria-label="Main Navigation">
                <a class="active" href="{{ $localePrefix }}">{{ $t('nav_home') }}</a>
                <a href="{{ $localePrefix }}/shop">{{ $t('nav_shop') }}</a>
                <a href="{{ $localePrefix }}/about-us">{{ $t('about_us') }}</a>
                <a href="{{ $localePrefix }}/marketplace">{{ $t('nav_marketplace') }}</a>
                <a href="https://styliiiish.com/my-dresses/" target="_blank" rel="noopener">{{ $t('nav_sell') }}</a>
                <a href="{{ $localePrefix }}/blog">{{ $t('nav_blog') }}</a>
                <a href="{{ $localePrefix }}/contact-us">{{ $t('nav_contact') }}</a>
            </nav>

            <div class="header-actions">
                <form class="search-form" action="https://styliiiish.com/" method="get" target="_blank">
                    <input class="search-input" type="search" name="s" placeholder="{{ $t('search_placeholder') }}" aria-label="{{ $t('search_placeholder') }}">
                    <button class="search-btn" type="submit">{{ $t('search_btn') }}</button>
                </form>
                <button class="icon-btn action-account account-trigger" id="accountLoginTrigger" type="button" aria-label="{{ $t('account') }}" title="{{ $t('account') }}" aria-controls="authModal" aria-expanded="false"><span class="icon" aria-hidden="true">👤</span></button>
                <span class="wishlist-trigger-wrap action-wishlist">
                    <button class="icon-btn wishlist-trigger" id="wishlistTrigger" type="button" aria-label="{{ $t('wishlist') }}" title="{{ $t('wishlist') }}" aria-expanded="false" aria-controls="wishlistDropdown"><span class="icon" aria-hidden="true">❤</span>
                        <span class="wishlist-count" id="wishlistCountBadge">0</span>
                    </button>
                    <span class="wishlist-plus-one" id="wishlistPlusOne">+1</span>
                    <div class="wishlist-dropdown" id="wishlistDropdown" role="dialog" aria-label="{{ $t('wishlist') }}" aria-hidden="true">
                        <div class="wishlist-dropdown-list" id="wishlistDropdownList">
                            <p class="wishlist-dropdown-empty" id="wishlistDropdownLoading">{{ $t('wishlist_loading') }}</p>
                        </div>
                        <div class="wishlist-dropdown-footer">
                            <a class="wishlist-dropdown-all" href="{{ $localePrefix }}/wishlist">{{ $t('view_all_wishlist') }}</a>
                        </div>
                    </div>
                </span>
                <span class="cart-trigger-wrap action-cart">
                    <button class="icon-btn cart-trigger" id="miniCartTrigger" type="button" aria-label="{{ $t('cart') }}" title="{{ $t('cart') }}"><span class="icon" aria-hidden="true">🛒</span>
                        <span class="cart-count" id="cartCountBadge">0</span>
                    </button>
                    <span class="cart-plus-one" id="cartPlusOne">+1</span>
                </span>
                <a class="btn btn-primary header-cta action-sell" href="https://styliiiish.com/my-dresses/" target="_blank" rel="noopener">{{ $t('start_selling') }}</a>
            </div>
        </div>
    </header>

    <div class="promo">{{ $t('promo_line') }}</div>

    <section class="hero">
        <div class="container hero-wrap">
            <div class="hero-main">
                <span class="badge">{{ $t('hero_badge') }}</span>
                <h1>{{ $t('hero_title') }}</h1>
                <p class="lead">{{ $t('hero_lead') }}</p>

                <ul class="hero-points">
                    <li>{{ $t('hero_p1') }}</li>
                    <li>{{ $t('hero_p2') }}</li>
                    <li>{{ $t('hero_p3') }}</li>
                    <li>{{ $t('hero_p4') }}</li>
                </ul>

                <div class="actions">
                    <a class="btn btn-primary" href="{{ $localePrefix }}/shop">{{ $t('shop_now') }}</a>
                    <a class="btn btn-light" href="/my-dresses/">{{ $t('sell_now') }}</a>
                </div>

                <div class="hero-mobile-trust" aria-hidden="true">
                    <span>{{ $t('ship_fast') }}</span>
                    <span>{{ $t('secure_pay') }}</span>
                    <span>{{ $t('clear_policy') }}</span>
                </div>

                <div class="hero-kpis">
                    <div class="hero-kpi">
                        <strong>{{ number_format((int)($stats['total_products'] ?? 0)) }}+</strong>
                        <span>{{ $t('available_products') }}</span>
                    </div>
                    <div class="hero-kpi">
                        <strong>{{ number_format((int)($stats['sale_products'] ?? 0)) }}+</strong>
                        <span>{{ $t('discounted_products') }}</span>
                    </div>
                    <div class="hero-kpi">
                        <strong>{{ $t('high_trust') }}</strong>
                        <span>{{ $t('support_before_after') }}</span>
                    </div>
                </div>
            </div>

            <aside class="hero-side">
                <h3>{{ $t('why_sty') }}</h3>
                <p class="hero-side-note">{{ $t('why_note') }}</p>
                <ul class="list">
                    <li>{{ $t('why_1') }}</li>
                    <li>{{ $t('why_2') }}</li>
                    <li>{{ $t('why_3') }}</li>
                    <li>{{ $t('why_4') }}</li>
                </ul>

                <div class="stats">
                    <div class="stat">
                        <strong>{{ number_format((int)($stats['total_products'] ?? 0)) }}+</strong>
                        <span>{{ $t('available_now') }}</span>
                    </div>
                    <div class="stat">
                        <strong>{{ number_format((int)($stats['sale_products'] ?? 0)) }}+</strong>
                        <span>{{ $t('on_discount') }}</span>
                    </div>
                    <div class="stat">
                        <strong>
                            @if(!empty($stats['min_price']))
                                {{ number_format((float)$stats['min_price']) }} {{ $isEnglish ? 'EGP' : 'ج.م' }}
                            @else
                                —
                            @endif
                        </strong>
                        <span>{{ $t('min_price') }}</span>
                    </div>
                    <div class="stat">
                        <strong>
                            @if(!empty($stats['max_price']))
                                {{ number_format((float)$stats['max_price']) }} {{ $isEnglish ? 'EGP' : 'ج.م' }}
                            @else
                                —
                            @endif
                        </strong>
                        <span>{{ $t('max_price') }}</span>
                    </div>
                </div>
            </aside>
        </div>
    </section>

   

    <section class="section">
        <div class="container">
            <div class="section-head">
                <div>
                    <h2 class="section-title">{{ $t('featured_title') }}</h2>
                    <p class="section-sub">{{ $t('featured_sub') }}</p>
                </div>
                <a class="btn btn-light" href="{{ $localePrefix }}/shop">{{ $t('view_all') }}</a>
            </div>

            <div class="grid">
                @foreach($products as $product)
                    @php
                        $price = (float) ($product->price ?? 0);
                        $regular = (float) ($product->regular_price ?? 0);
                        $isSale = $regular > 0 && $price > 0 && $regular > $price;
                        $discount = $isSale ? round((($regular - $price) / $regular) * 100) : 0;
                        $saving = $isSale ? ($regular - $price) : 0;
                        $image = $product->image ?: 'https://styliiiish.com/wp-content/uploads/woocommerce-placeholder.png';
                    @endphp

                    <article class="card">
                        <div class="product-media">
                            <img class="thumb" src="{{ $image }}" alt="{{ $product->post_title }}" loading="lazy">
                            <div class="card-badges">
                                <span class="badge-chip badge-hot">{{ $t('featured_badge') }}</span>
                                @if($isSale)
                                    <span class="badge-chip badge-discount">{{ $t('discount_badge') }} {{ $discount }}%</span>
                                @endif
                            </div>
                        </div>

                        <div class="content">
                            <h3 class="name">{{ $product->post_title }}</h3>
                            <div class="meta">
                                <span>{{ $t('available_label') }}</span>
                                <span>{{ $t('delivery_label') }}</span>
                            </div>

                            <div class="prices">
                                <span class="price">
                                    @if($price > 0)
                                        {{ number_format($price) }} {{ $isEnglish ? 'EGP' : 'ج.م' }}
                                    @else
                                        {{ $t('contact_for_price') }}
                                    @endif
                                </span>
                                @if($isSale)
                                    <span class="old">{{ number_format($regular) }} {{ $isEnglish ? 'EGP' : 'ج.م' }}</span>
                                    <span class="sale">{{ $t('discount_badge') }} {{ $discount }}%</span>
                                @endif
                            </div>

                            @if($isSale)
                                <span class="save">{{ $t('save_prefix') }} {{ number_format($saving) }} {{ $isEnglish ? 'EGP' : 'ج.م' }}</span>
                            @endif

                            <div class="card-actions">
                                <a class="buy" href="{{ $localePrefix }}/item/{{ $product->post_name }}">{{ $t('order_now') }}</a>
                                <a class="view-link" href="{{ $localePrefix }}/item/{{ $product->post_name }}">{{ $t('view_product') }}</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container trust">
            <article class="trust-item">
                <h4>{{ $t('trust_1_t') }}</h4>
                <p>{{ $t('trust_1_d') }}</p>
            </article>
            <article class="trust-item">
                <h4>{{ $t('trust_2_t') }}</h4>
                <p>{{ $t('trust_2_d') }}</p>
            </article>
            <article class="trust-item">
                <h4>{{ $t('trust_3_t') }}</h4>
                <p>{{ $t('trust_3_d') }}</p>
            </article>
        </div>
    </section>

    <section class="section reviews-trust">
        <div class="container">
            <div class="reviews-head">
                <h3>{{ $t('reviews_title') }}</h3>
                <p>{{ $t('reviews_sub') }}</p>
            </div>

            <div class="reviews-slider-wrap">
                <button type="button" class="reviews-nav prev" id="reviewsPrev" aria-label="{{ $t('prev') }}">{{ $reviewsPrevArrow }}</button>
                <button type="button" class="reviews-nav next" id="reviewsNext" aria-label="{{ $t('next') }}">{{ $reviewsNextArrow }}</button>

                <div class="reviews-slider" id="reviewsSlider">
                @php
                    $googleReviewsLink = 'https://www.google.com/search?newwindow=1&sa=X&sca_esv=7a144a3578fe712f&rlz=1C1CHBD_arEG1137EG1137&hl=ar-NL&q=Styliiiiish+%D8%A7%D9%84%D8%A2%D8%B1%D8%A7%D8%A1&rflfq=1&num=20&stick=H4sIAAAAAAAAAONgkxIxtDC0MDA1MbMwM7UwMDaxMDYxM9jAyPiKUSK4pDInEwSKMxRuLL_ZcmPRjY03lt9YuIgVpxQApWPa_VEAAAA&rldimm=18180546865803483460&tbm=lcl&ved=2ahUKEwi12NOiw-qSAxVk0AIHHc_3KTEQ9fQKegQIQBAG&biw=1536&bih=852&dpr=1.25#lkt=LocalPoiReviews';
                @endphp
                @forelse (($reviewImages ?? collect()) as $index => $reviewImage)
                    <a class="review-shot" href="{{ $googleReviewsLink }}" target="_blank" rel="noopener nofollow" aria-label="{{ $t('open_google_reviews') }}" data-open-label="{{ $t('review_overlay') }}">
                        <img src="{{ $reviewImage }}" alt="Google Review {{ $index + 1 }}" loading="lazy">
                        <span class="review-meta">{{ $t('rating') }} {{ $index + 1 }}</span>
                    </a>
                @empty
                    <div class="review-shot" style="justify-content:center; min-height: 140px; color: var(--muted);">
                        {{ $t('no_reviews_now') }}
                    </div>
                @endforelse
                </div>
            </div>
        </div>
    </section>

    <script>
        (() => {
            const slider = document.getElementById('reviewsSlider');
            const prevBtn = document.getElementById('reviewsPrev');
            const nextBtn = document.getElementById('reviewsNext');

            if (!slider || !prevBtn || !nextBtn) return;

            const cards = Array.from(slider.querySelectorAll('.review-shot'));
            if (!cards.length) return;

            let currentIndex = 0;
            let autoSlideTimer = null;

            const updateButtons = () => {
                prevBtn.disabled = currentIndex <= 0;
                nextBtn.disabled = currentIndex >= cards.length - 1;
                prevBtn.style.opacity = prevBtn.disabled ? '0.45' : '1';
                nextBtn.style.opacity = nextBtn.disabled ? '0.45' : '1';
            };

            const goToIndex = (index) => {
                currentIndex = Math.max(0, Math.min(index, cards.length - 1));
                slider.scrollTo({
                    left: cards[currentIndex].offsetLeft,
                    behavior: 'smooth'
                });
                updateButtons();
            };

            const goToNext = () => {
                if (currentIndex >= cards.length - 1) {
                    goToIndex(0);
                    return;
                }
                goToIndex(currentIndex + 1);
            };

            const startAutoSlide = () => {
                stopAutoSlide();
                autoSlideTimer = setInterval(goToNext, 4000);
            };

            const stopAutoSlide = () => {
                if (!autoSlideTimer) return;
                clearInterval(autoSlideTimer);
                autoSlideTimer = null;
            };

            const detectActiveIndex = () => {
                const sliderRect = slider.getBoundingClientRect();
                let bestIndex = currentIndex;
                let bestDistance = Infinity;

                cards.forEach((card, index) => {
                    const rect = card.getBoundingClientRect();
                    const distance = Math.abs(rect.left - sliderRect.left);
                    if (distance < bestDistance) {
                        bestDistance = distance;
                        bestIndex = index;
                    }
                });

                currentIndex = bestIndex;
                updateButtons();
            };

            prevBtn.addEventListener('click', () => {
                goToIndex(currentIndex - 1);
                startAutoSlide();
            });

            nextBtn.addEventListener('click', () => {
                goToIndex(currentIndex + 1);
                startAutoSlide();
            });

            slider.addEventListener('scroll', detectActiveIndex, { passive: true });
            slider.addEventListener('mouseenter', stopAutoSlide);
            slider.addEventListener('mouseleave', startAutoSlide);
            slider.addEventListener('touchstart', stopAutoSlide, { passive: true });
            slider.addEventListener('touchend', startAutoSlide, { passive: true });

            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    stopAutoSlide();
                } else {
                    startAutoSlide();
                }
            });

            updateButtons();
            startAutoSlide();
        })();
    </script>

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

    <div class="auth-modal" id="authModal" aria-hidden="true">
        <div class="auth-modal-backdrop" data-close-auth-modal></div>
        <section class="auth-modal-panel" role="dialog" aria-modal="true" aria-label="{{ $t('login_title') }}">
            <div class="auth-head">
                <div class="auth-title-wrap">
                    <h3>{{ $t('login_title') }}</h3>
                    <p>{{ $t('login_subtitle') }}</p>
                </div>
                <button class="auth-close" type="button" data-close-auth-modal aria-label="{{ $t('close') }}">×</button>
            </div>

            <form class="auth-form" id="authLoginForm" action="{{ $wpLoginUrl }}" method="post" autocomplete="on">
                <input type="hidden" name="redirect" value="{{ $wpMyAccountUrl }}">
                <input type="hidden" name="login" value="Log in">
                <input type="hidden" id="authWooLoginNonce" name="woocommerce-login-nonce" value="">

                <div class="auth-field">
                    <label for="authLoginField">{{ $t('login_username') }}</label>
                    <input id="authLoginField" type="text" name="username" required>
                </div>

                <div class="auth-field">
                    <label for="authPasswordField">{{ $t('login_password') }}</label>
                    <input id="authPasswordField" type="password" name="password" required>
                </div>

                <div class="auth-row">
                    <label class="auth-remember" for="authRememberField">
                        <input id="authRememberField" type="checkbox" name="rememberme" value="forever">
                        <span>{{ $t('remember_me') }}</span>
                    </label>
                    <a class="auth-forgot" href="{{ $wpForgotPasswordUrl }}" target="_blank" rel="noopener">{{ $t('forgot_password') }}</a>
                </div>

                <button class="auth-submit" type="submit">{{ $t('sign_in') }}</button>

                <div class="auth-divider">or</div>

                <div class="auth-google-wrap">
                    <div class="googlesitekit-sign-in-with-google__frontend-output-button" id="authGoogleButton" data-googlesitekit-siwg-shape="pill" data-googlesitekit-siwg-text="continue_with" data-googlesitekit-siwg-theme="filled_blue" aria-label="{{ $t('sign_in_google') }}"></div>
                    <a class="auth-google-fallback" id="authGoogleFallback" href="{{ $wpMyAccountUrl }}" target="_blank" rel="noopener">{{ $t('sign_in_google') }}</a>
                </div>

                <a class="auth-register" href="{{ $wpRegisterUrl }}" target="_blank" rel="noopener">{{ $t('register') }}</a>
            </form>
        </section>
    </div>

    <script>
        (() => {
            const adminAjaxUrl = @json($wpBaseUrl . '/wp-admin/admin-ajax.php');
            const localePrefix = @json($localePrefix);
            const wpCheckoutUrl = @json($wpCheckoutUrl);
            const wpMyAccountUrl = @json($wpMyAccountUrl);
            const currentPageUrl = window.location.href;
            const wishlistLoadingText = @json($t('wishlist_loading'));
            const wishlistEmptyText = @json($t('wishlist_empty'));
            const goToProductText = @json($t('go_to_product'));
            const cartEmptyText = @json($t('cart_empty'));
            const loadingCartText = @json($t('loading_cart'));
            const removeText = @json($t('remove'));
            const qtyShortText = @json($t('qty_short'));

            const wishlistTrigger = document.getElementById('wishlistTrigger');
            const wishlistBadge = document.getElementById('wishlistCountBadge');
            const wishlistPlusOne = document.getElementById('wishlistPlusOne');
            const wishlistDropdown = document.getElementById('wishlistDropdown');
            const wishlistDropdownList = document.getElementById('wishlistDropdownList');

            const cartTrigger = document.getElementById('miniCartTrigger');
            const cartBadge = document.getElementById('cartCountBadge');
            const plusOne = document.getElementById('cartPlusOne');
            const miniCart = document.getElementById('miniCart');
            const miniCartList = document.getElementById('miniCartList');
            const miniCartSubtotal = document.getElementById('miniCartSubtotal');
            const miniCartView = document.getElementById('miniCartView');
            const miniCartCheckout = document.getElementById('miniCartCheckout');
            const miniCartClosers = miniCart ? miniCart.querySelectorAll('[data-close-mini-cart]') : [];
            const accountLoginTrigger = document.getElementById('accountLoginTrigger');
            const authModal = document.getElementById('authModal');
            const authModalClosers = authModal ? authModal.querySelectorAll('[data-close-auth-modal]') : [];
            const authLoginForm = document.getElementById('authLoginForm');
            const authWooLoginNonce = document.getElementById('authWooLoginNonce');
            const authGoogleButton = document.getElementById('authGoogleButton');
            const authGoogleFallback = document.getElementById('authGoogleFallback');
            const authRedirectField = authLoginForm ? authLoginForm.querySelector('input[name="redirect"]') : null;

            let siteKitGoogleConfig = null;
            let siteKitGoogleInitialized = false;

            const hasWpLoginCookie = () => {
                return document.cookie
                    .split(';')
                    .map((entry) => entry.trim())
                    .some((entry) => entry.startsWith('wordpress_logged_in_'));
            };

            const getSafeCurrentUrl = () => {
                try {
                    const parsed = new URL(window.location.href);
                    if (parsed.origin === window.location.origin) {
                        return parsed.toString();
                    }
                } catch (error) {
                }

                return wpMyAccountUrl;
            };

            const setSiteKitRedirectCookie = (redirectUrl) => {
                const expires = new Date(Date.now() + (5 * 60 * 1000)).toUTCString();
                const cookieValue = `googlesitekit_auth_redirect_to=${redirectUrl};expires=${expires};path=/`;
                document.cookie = cookieValue;
            };

            const extractSiteKitGoogleConfig = (doc) => {
                if (!doc) return null;
                const scripts = Array.from(doc.querySelectorAll('script'));

                for (const scriptTag of scripts) {
                    const text = String(scriptTag.textContent || '');
                    if (!text.includes('googlesitekit_auth') || !text.includes('google.accounts.id.initialize')) {
                        continue;
                    }

                    const endpointMatch = text.match(/fetch\('([^']*action=googlesitekit_auth[^']*)'/);
                    const clientMatch = text.match(/client_id:'([^']+)'/);
                    if (!endpointMatch || !clientMatch) continue;

                    return {
                        endpoint: endpointMatch[1],
                        clientId: clientMatch[1],
                    };
                }

                return null;
            };

            const loadGoogleIdentityScript = async () => {
                if (window.google && window.google.accounts && window.google.accounts.id) return;

                const existingScript = document.querySelector('script[src="https://accounts.google.com/gsi/client"]');
                if (existingScript) {
                    await new Promise((resolve) => {
                        if (window.google && window.google.accounts && window.google.accounts.id) {
                            resolve();
                            return;
                        }
                        existingScript.addEventListener('load', resolve, { once: true });
                        existingScript.addEventListener('error', resolve, { once: true });
                    });
                    return;
                }

                await new Promise((resolve) => {
                    const script = document.createElement('script');
                    script.src = 'https://accounts.google.com/gsi/client';
                    script.async = true;
                    script.defer = true;
                    script.onload = resolve;
                    script.onerror = resolve;
                    document.head.appendChild(script);
                });
            };

            const initGoogleButton = async () => {
                if (!authGoogleButton || !siteKitGoogleConfig) return;

                await loadGoogleIdentityScript();
                if (!(window.google && window.google.accounts && window.google.accounts.id)) return;

                const endpointUrl = String(siteKitGoogleConfig.endpoint || '');
                const absoluteEndpoint = endpointUrl.startsWith('http')
                    ? endpointUrl
                    : new URL(endpointUrl, wpMyAccountUrl).toString();

                const handleGoogleCredentialResponse = async (response) => {
                    response.integration = 'woocommerce';
                    const redirectTarget = getSafeCurrentUrl();
                    setSiteKitRedirectCookie(redirectTarget);

                    try {
                        const result = await fetch(absoluteEndpoint, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: new URLSearchParams(response),
                            credentials: 'same-origin',
                        });

                        if (result.ok && result.redirected) {
                            location.assign(result.url);
                            return;
                        }
                    } catch (error) {
                    }

                    location.assign(redirectTarget);
                };

                if (!siteKitGoogleInitialized) {
                    window.google.accounts.id.initialize({
                        client_id: siteKitGoogleConfig.clientId,
                        callback: handleGoogleCredentialResponse,
                        library_name: 'Site-Kit',
                    });
                    siteKitGoogleInitialized = true;
                }

                authGoogleButton.innerHTML = '';
                window.google.accounts.id.renderButton(authGoogleButton, {
                    shape: authGoogleButton.getAttribute('data-googlesitekit-siwg-shape') || 'pill',
                    text: authGoogleButton.getAttribute('data-googlesitekit-siwg-text') || 'continue_with',
                    theme: authGoogleButton.getAttribute('data-googlesitekit-siwg-theme') || 'filled_blue',
                });

                if (authGoogleFallback) authGoogleFallback.style.display = 'none';
            };

            const fetchWooLoginNonce = async () => {
                if (!authWooLoginNonce) return;
                if (authWooLoginNonce.value && siteKitGoogleConfig) return;

                const response = await fetch(`${wpMyAccountUrl}?_=${Date.now()}`, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!response.ok) return;

                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const nonceField = doc.querySelector('input[name="woocommerce-login-nonce"]');
                if (nonceField && nonceField.value) {
                    authWooLoginNonce.value = nonceField.value;
                }

                const extractedConfig = extractSiteKitGoogleConfig(doc);
                if (extractedConfig) {
                    siteKitGoogleConfig = extractedConfig;
                    await initGoogleButton();
                }
            };

            let currentWishlistCount = 0;
            let currentCartCount = 0;
            let wishlistItemsCache = [];
            let cartPayloadCache = null;

            const updateBodyScrollLock = () => {
                const isMiniCartOpen = !!(miniCart && miniCart.classList.contains('is-open'));
                const isAuthOpen = !!(authModal && authModal.classList.contains('is-open'));
                document.body.style.overflow = (isMiniCartOpen || isAuthOpen) ? 'hidden' : '';
            };

            const getWishlistCountUrl = () => `${localePrefix}/item/wishlist/count`;
            const getWishlistItemsUrl = () => `${localePrefix}/item/wishlist/items`;

            const escapeHtml = (value) => String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');

            const setWishlistCount = (count) => {
                currentWishlistCount = Math.max(0, Number(count) || 0);
                if (!wishlistBadge) return;
                wishlistBadge.textContent = String(currentWishlistCount);
                wishlistBadge.style.display = currentWishlistCount > 0 ? 'inline-block' : 'none';
            };

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

            const animatePlusOne = (node) => {
                if (!node) return;
                node.classList.remove('show');
                void node.offsetWidth;
                node.classList.add('show');
            };

            const renderWishlistDropdown = (items = []) => {
                if (!wishlistDropdownList) return;
                const safeItems = Array.isArray(items) ? items : [];
                if (safeItems.length === 0) {
                    wishlistDropdownList.innerHTML = `<p class="wishlist-dropdown-empty">${escapeHtml(wishlistEmptyText)}</p>`;
                    return;
                }

                wishlistDropdownList.innerHTML = safeItems.map((item) => {
                    const image = escapeHtml(item.image || '');
                    const name = escapeHtml(item.name || '');
                    const url = escapeHtml(item.url || '#');

                    return `
                        <article class="wishlist-dropdown-item">
                            <a href="${url}"><img src="${image}" alt="${name}"></a>
                            <div>
                                <h4 class="wishlist-dropdown-name">${name}</h4>
                                <a class="wishlist-dropdown-link" href="${url}">${escapeHtml(goToProductText)}</a>
                            </div>
                        </article>
                    `;
                }).join('');
            };

            const loadWishlistItems = async (forceReload = false) => {
                if (!wishlistDropdownList) return wishlistItemsCache;

                if (!forceReload && wishlistItemsCache.length > 0) {
                    renderWishlistDropdown(wishlistItemsCache);
                    return wishlistItemsCache;
                }

                wishlistDropdownList.innerHTML = `<p class="wishlist-dropdown-empty">${escapeHtml(wishlistLoadingText)}</p>`;

                const response = await fetch(`${getWishlistItemsUrl()}?_=${Date.now()}`, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) throw new Error('wishlist_items_failed');
                const result = await response.json();
                if (!result || !result.success) throw new Error('wishlist_items_failed');

                setWishlistCount(Math.max(0, Number(result.count || 0)));
                wishlistItemsCache = Array.isArray(result.items) ? result.items : [];
                renderWishlistDropdown(wishlistItemsCache);
                return wishlistItemsCache;
            };

            const refreshWishlistCount = async (withAnimation = false) => {
                const response = await fetch(`${getWishlistCountUrl()}?_=${Date.now()}`, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) throw new Error('wishlist_count_failed');
                const result = await response.json();
                if (!result || !result.success) throw new Error('wishlist_count_failed');

                const nextCount = Math.max(0, Number(result.count || 0));
                const shouldAnimate = withAnimation && nextCount > currentWishlistCount;
                setWishlistCount(nextCount);
                if (shouldAnimate) animatePlusOne(wishlistPlusOne);
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

            const openMiniCart = () => {
                if (!miniCart) return;
                miniCart.classList.add('is-open');
                miniCart.setAttribute('aria-hidden', 'false');
                updateBodyScrollLock();
            };

            const closeMiniCart = () => {
                if (!miniCart) return;
                miniCart.classList.remove('is-open');
                miniCart.setAttribute('aria-hidden', 'true');
                updateBodyScrollLock();
            };

            const openAuthModal = () => {
                if (!authModal) return;
                authModal.classList.add('is-open');
                authModal.setAttribute('aria-hidden', 'false');
                if (accountLoginTrigger) accountLoginTrigger.setAttribute('aria-expanded', 'true');
                updateBodyScrollLock();
            };

            const closeAuthModal = () => {
                if (!authModal) return;
                authModal.classList.remove('is-open');
                authModal.setAttribute('aria-hidden', 'true');
                if (accountLoginTrigger) accountLoginTrigger.setAttribute('aria-expanded', 'false');
                updateBodyScrollLock();
            };

            const renderMiniCart = (payload) => {
                if (!payload) return;
                cartPayloadCache = payload;

                const count = resolveCountFromPayload(payload);
                const shouldAnimate = count > currentCartCount;
                setCartCount(count);
                if (shouldAnimate) animatePlusOne(plusOne);

                if (miniCartSubtotal) miniCartSubtotal.innerHTML = payload.subtotal_html || '—';
                if (miniCartView) miniCartView.href = `${localePrefix}/cart`;
                if (miniCartCheckout) miniCartCheckout.href = wpCheckoutUrl;

                if (!miniCartList) return;
                const items = Array.isArray(payload.items) ? payload.items : [];
                if (items.length === 0) {
                    miniCartList.innerHTML = `<p class="mini-cart-empty">${escapeHtml(cartEmptyText)}</p>`;
                    return;
                }

                miniCartList.innerHTML = items.map((item) => {
                    return `
                        <article class="mini-cart-item">
                            <a href="${escapeHtml(item.url || '#')}"><img src="${escapeHtml(item.image || '')}" alt="${escapeHtml(item.name || '')}"></a>
                            <div class="mini-cart-info">
                                <h4>${escapeHtml(item.name || '')}</h4>
                                <div class="mini-cart-meta"><span>${escapeHtml(qtyShortText)}:</span><strong>${Number(item.qty || 1)}</strong></div>
                                <div class="mini-cart-price">${item.line_total_html || item.price_html || ''}</div>
                            </div>
                            <button type="button" class="mini-cart-remove" data-remove-cart-key="${escapeHtml(item.key || '')}">${escapeHtml(removeText)}</button>
                        </article>
                    `;
                }).join('');
            };

            const getCartSummary = async () => {
                const response = await fetch(`${adminAjaxUrl}?action=styliiiish_cart_summary`, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!response.ok) throw new Error('summary_failed');

                const result = await response.json();
                if (!result || !result.success) throw new Error('summary_failed');
                renderMiniCart(result.data);
            };

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
                if (!insideTrigger && !insideDropdown) closeWishlistDropdown();
            });

            if (cartTrigger) {
                cartTrigger.addEventListener('click', () => {
                    openMiniCart();
                    if (cartPayloadCache) {
                        renderMiniCart(cartPayloadCache);
                    } else if (miniCartList && miniCartList.innerHTML.trim() === '') {
                        miniCartList.innerHTML = `<div class="mini-cart-loading">${escapeHtml(loadingCartText)}</div>`;
                    }
                    getCartSummary().catch(() => {});
                });
            }

            if (miniCartClosers.length > 0) {
                miniCartClosers.forEach((node) => node.addEventListener('click', closeMiniCart));
            }

            if (accountLoginTrigger) {
                accountLoginTrigger.addEventListener('click', () => {
                    if (hasWpLoginCookie()) {
                        window.open(wpMyAccountUrl, '_blank', 'noopener');
                        return;
                    }

                    if (authRedirectField) {
                        authRedirectField.value = getSafeCurrentUrl();
                    }

                    openAuthModal();
                    fetchWooLoginNonce().catch(() => {});
                    const firstField = authModal ? authModal.querySelector('input[name="username"]') : null;
                    if (firstField) {
                        setTimeout(() => firstField.focus(), 40);
                    }
                });
            }

            if (authModalClosers.length > 0) {
                authModalClosers.forEach((node) => node.addEventListener('click', closeAuthModal));
            }

            if (authLoginForm) {
                authLoginForm.addEventListener('submit', async () => {
                    if (authRedirectField) {
                        authRedirectField.value = getSafeCurrentUrl();
                    }
                    await fetchWooLoginNonce().catch(() => {});
                });
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
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: params.toString(),
                    });

                    const result = await response.json();
                    if (response.ok && result && result.success) {
                        renderMiniCart(result.data);
                    }
                });
            }

            document.addEventListener('keydown', (event) => {
                if (event.key !== 'Escape') return;
                if (miniCart && miniCart.classList.contains('is-open')) closeMiniCart();
                if (wishlistDropdown && wishlistDropdown.classList.contains('is-open')) closeWishlistDropdown();
                if (authModal && authModal.classList.contains('is-open')) closeAuthModal();
            });

            setCartCount(0);
            getCartSummary().catch(() => setCartCount(0));
            setWishlistCount(0);
            refreshWishlistCount(false).catch(() => {});
        })();
    </script>

    <section class="container final-cta">
        <h3>{{ $t('final_title') }}</h3>
        <p>{{ $t('final_sub') }}</p>
        <div class="actions">
            <a class="btn btn-primary" href="{{ $localePrefix }}/shop">{{ $t('start_shop') }}</a>
            <a class="btn btn-light" href="/my-dresses/">{{ $t('start_selling') }}</a>
        </div>
    </section>

    <footer class="site-footer">
        <div class="container footer-grid">
            <div class="footer-brand">
                <img class="footer-brand-logo" src="{{ $wpLogo }}" alt="Styliiiish" onerror="this.onerror=null;this.src='/brand/logo.png';">
                <h4>{{ $t('footer_title') }}</h4>
                <p>{{ $t('footer_desc') }}</p>
                <p class="footer-status">
                    {{ $t('status_label') }} :
                    <span class="status-pill {{ $isOpenNow ? 'is-open' : 'is-closed' }}">{{ $isOpenNow ? $t('open_now') : $t('closed_now') }}</span>
                </p>
                <p class="footer-open-hours"><strong>{{ $t('open_hours_label') }}:</strong> {{ $t('open_hours_value') }}</p>
                <div class="footer-contact-row">
                    <a href="{{ $localePrefix }}/contact-us">{{ $t('contact_us') }}</a>
                    <a href="tel:+201050874255">{{ $t('direct_call') }}</a>
                </div>
            </div>

            <div class="footer-col">
                <h5>{{ $t('quick_links') }}</h5>
                <ul class="footer-links">
                    <li><a href="https://styliiiish.com/" target="_blank" rel="noopener">{{ $t('nav_home') }}</a></li>
                    <li><a href="{{ $localePrefix }}/blog">{{ $t('nav_blog') }}</a></li>
                    <li><a href="https://styliiiish.com/dress-rental-in-cairo/" target="_blank" rel="noopener">{{ $t('shop_now') }}</a></li>
                    <li><a href="https://styliiiish.com/dress-rental-in-cairo/" target="_blank" rel="noopener">{{ $t('nav_shop') }}</a></li>
                    <li><a href="{{ $localePrefix }}/marketplace">{{ $t('nav_marketplace') }}</a></li>
                    <li><a href="{{ $localePrefix }}/categories">{{ $t('categories') }}</a></li>
                    <li><a href="https://styliiiish.com/my-dresses/" target="_blank" rel="noopener">{{ $t('nav_sell') }}</a></li>
                    <li><a href="https://styliiiish.com/my-account/" target="_blank" rel="noopener">{{ $t('account') }}</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h5>{{ $t('official_info') }}</h5>
                <ul class="footer-links">
                    <li><a href="https://maps.app.goo.gl/MCdcFEcFoR4tEjpT8" target="_blank" rel="noopener">{{ $t('official_address') }}</a></li>
                    <li><a href="tel:+201050874255">+2 010-5087-4255</a></li>
                    <li><a href="{{ $localePrefix }}/contact-us">{{ $t('nav_contact') }}</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h5>{{ $t('policies') }}</h5>
                <ul class="footer-links">
                    <li><a href="{{ $localePrefix }}/about-us">{{ $t('about_us') }}</a></li>
                    <li><a href="{{ $localePrefix }}/privacy-policy">{{ $t('privacy') }}</a></li>
                    <li><a href="{{ $localePrefix }}/terms-conditions">{{ $t('terms') }}</a></li>
                    <li><a href="{{ $localePrefix }}/marketplace-policy">{{ $t('market_policy') }}</a></li>
                    <li><a href="{{ $localePrefix }}/refund-return-policy">{{ $t('refund_policy') }}</a></li>
                    <li><a href="{{ $localePrefix }}/faq">{{ $t('faq') }}</a></li>
                    <li><a href="{{ $localePrefix }}/shipping-delivery-policy">{{ $t('shipping_policy') }}</a></li>
                    <li><a href="{{ $localePrefix }}/cookie-policy">{{ $t('cookies') }}</a></li>
                </ul>
            </div>
        </div>

        <div class="container footer-bottom">
            <span>{{ str_replace(':year', (string) date('Y'), $t('rights')) }} <a href="https://websiteflexi.com/" target="_blank" rel="noopener">Website Flexi</a></span>
            <span><a href="https://styliiiish.com/" target="_blank" rel="noopener">styliiiish.com</a></span>
        </div>

        <div class="container footer-mini-nav">
            <a href="https://styliiiish.com/" target="_blank" rel="noopener">{{ $t('home_mini') }}</a>
            <a href="{{ $localePrefix }}/shop">{{ $t('shop_mini') }}</a>
            <a href="https://styliiiish.com/cart/" target="_blank" rel="noopener">{{ $t('cart_mini') }}</a>
            <a href="https://styliiiish.com/my-account/" target="_blank" rel="noopener">{{ $t('account_mini') }}</a>
            <a href="https://styliiiish.com/wishlist/" target="_blank" rel="noopener">{{ $t('fav_mini') }}</a>
        </div>
    </footer>

</body>
</html>