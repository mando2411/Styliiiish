<!DOCTYPE html>
@php
    $currentLocale = $currentLocale ?? 'ar';
    $localePrefix = $localePrefix ?? '/ar';
    $isEnglish = $currentLocale === 'en';

    $businessTimezone = new DateTimeZone('Africa/Cairo');
    $nowInCairo = new DateTimeImmutable('now', $businessTimezone);
    $currentMinutes = ((int) $nowInCairo->format('H') * 60) + (int) $nowInCairo->format('i');
    $openFromMinutes = 11 * 60;
    $openUntilMinutes = 19 * 60;
    $isOpenNow = $currentMinutes >= $openFromMinutes && $currentMinutes < $openUntilMinutes;

    $wpLogo = 'https://styliiiish.com/wp-content/uploads/2025/11/ChatGPT-Image-Nov-2-2025-03_11_14-AM-e1762046066547.png';
    $wpIcon = 'https://styliiiish.com/wp-content/uploads/2025/11/cropped-ChatGPT-Image-Nov-2-2025-03_11_14-AM-e1762046066547.png';

    $translations = [
        'ar' => [
            'page_title' => 'تواصل معنا | ستايلش',
            'meta_desc' => 'تواصلي مع Styliiiish Fashion House في القاهرة لحجز زيارة أو الاستفسار عن فساتين جاهزة، تفصيل، أو تأجير.',
            'brand_tag' => 'لأن كل امرأة تستحق أن تتألق',
            'nav_home' => 'الرئيسية',
            'nav_shop' => 'المتجر',
            'nav_blog' => 'المدونة',
            'nav_marketplace' => 'الماركت بليس',
            'nav_sell' => 'بيعي فستانك',
            'nav_contact' => 'تواصل معنا',
            'lang_switch' => 'تبديل اللغة',
            'hero_badge' => '📞 نحن هنا دائمًا لخدمتك',
            'hero_title' => 'تواصل معنا',
            'hero_desc' => 'في Styliiiish Fashion House نسعد دائمًا بتواصلك. سواء كنتِ تبحثين عن فستان جاهز، تصميم خاص، أو تأجير لفستان مناسبتك، فريقنا هنا لدعمك بخبرة واهتمام.',
            'status_label' => 'الحالة',
            'open' => 'مفتوح',
            'closed' => 'مغلق',
            'open_hours' => 'ساعات العمل',
            'open_hours_value' => 'السبت – الجمعة: 11:00 ص – 7:00 م',
            'section_get_in_touch' => 'تواصلي معنا مباشرة',
            'section_get_in_touch_desc' => 'هل لديكِ سؤال أو تحتاجين مساعدة؟ تواصلي معنا وسنرد عليكِ بأسرع وقت ممكن.',
            'phone' => 'الهاتف',
            'email' => 'البريد الإلكتروني',
            'visit_note' => 'الزيارات',
            'visit_note_value' => 'يرجى حجز موعد قبل الزيارة',
            'studio_title' => 'زوري الأتيليه',
            'studio_desc' => 'يمكنك زيارة الأتيليه للتعرف على أحدث الموديلات أو مناقشة تصميمك الخاص.',
            'address_title' => 'العنوان',
            'address_line_1' => '1 شارع نبيل خليل',
            'address_line_2' => 'متفرع من شارع حسنين هيكل',
            'address_line_3' => 'خلف مطعم جاد، مدينة نصر',
            'address_line_4' => 'القاهرة، مصر',
            'view_map' => 'عرض على Google Maps',
            'message_title' => 'أرسلي لنا رسالة',
            'message_desc' => 'احكي لنا عن مناسبتك القادمة — زفاف، خطوبة، أو احتفال خاص — وفريقنا يساعدك في اختيار الإطلالة المثالية.',
            'field_name' => 'الاسم',
            'field_email' => 'البريد الإلكتروني',
            'field_subject' => 'الموضوع',
            'field_message' => 'رسالتك (اختياري)',
            'send_message' => 'إرسال الرسالة',
            'send_note' => 'سيتم فتح تطبيق البريد الإلكتروني لإرسال رسالتك.',
            'map_title' => 'موقعنا على الخريطة',
            'map_desc' => 'تعرفي على موقعنا بدقة وزورينا لاكتشاف أحدث المجموعات وخيارات التفصيل والتأجير.',
            'footer_title' => 'ستيليش فاشون هاوس',
            'footer_desc' => 'نعمل بشغف على تقديم أحدث تصاميم الفساتين لتناسب كل مناسبة خاصة بك.',
            'footer_hours' => 'مواعيد العمل: السبت إلى الجمعة من 11:00 صباحًا حتى 7:00 مساءً.',
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
            'home_mini' => 'الرئيسية',
            'shop_mini' => 'المتجر',
            'cart_mini' => 'السلة',
            'account_mini' => 'حسابي',
            'fav_mini' => 'المفضلة',
        ],
        'en' => [
            'page_title' => 'Contact Us | Styliiiish',
            'meta_desc' => 'Get in touch with Styliiiish Fashion House in Cairo for ready-made dresses, custom designs, or dress rental support.',
            'brand_tag' => 'Because every woman deserves to shine',
            'nav_home' => 'Home',
            'nav_shop' => 'Shop',
            'nav_blog' => 'Blog',
            'nav_marketplace' => 'Marketplace',
            'nav_sell' => 'Sell Your Dress',
            'nav_contact' => 'Contact Us',
            'lang_switch' => 'Language Switcher',
            'hero_badge' => '📞 We are always here to help',
            'hero_title' => 'Contact Us',
            'hero_desc' => 'At Styliiiish Fashion House, we are always happy to hear from you. Whether you need a ready-made dress, a custom design, or dress rental support, our team is here with care and professionalism.',
            'status_label' => 'Status',
            'open' => 'Open',
            'closed' => 'Closed',
            'open_hours' => 'Open Hours',
            'open_hours_value' => 'Sat – Fri: 11:00 am – 7:00 pm.',
            'section_get_in_touch' => 'Get in Touch',
            'section_get_in_touch_desc' => 'Have a question or need assistance? Contact us and we will reply as quickly as possible.',
            'phone' => 'Phone',
            'email' => 'Email',
            'visit_note' => 'Visits',
            'visit_note_value' => 'Appointment required before visit',
            'studio_title' => 'Visit Our Studio',
            'studio_desc' => 'You are welcome to visit our studio to explore collections or discuss your custom design.',
            'address_title' => 'Address',
            'address_line_1' => '1 Nabil Khalil Street',
            'address_line_2' => 'off Hassanein Heikal Street',
            'address_line_3' => 'behind Gad Restaurant, Nasr City',
            'address_line_4' => 'Cairo, Egypt',
            'view_map' => 'View on Google Maps',
            'message_title' => 'Send Us a Message',
            'message_desc' => 'Tell us about your upcoming event — wedding, engagement, or a special celebration — and our team will guide you to the perfect look.',
            'field_name' => 'Your Name',
            'field_email' => 'Your Email',
            'field_subject' => 'Subject',
            'field_message' => 'Your Message (Optional)',
            'send_message' => 'Send Message',
            'send_note' => 'Your email app will open to send the message.',
            'map_title' => 'Our Location on the Map',
            'map_desc' => 'Find us on the map below and visit Styliiiish to explore our latest collections and rental/custom options.',
            'footer_title' => 'Styliiiish Fashion House',
            'footer_desc' => 'We are passionate about offering the latest dress designs for every special occasion.',
            'footer_hours' => 'Working hours: Saturday to Friday from 11:00 AM to 7:00 PM.',
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
            'home_mini' => 'Home',
            'shop_mini' => 'Shop',
            'cart_mini' => 'Cart',
            'account_mini' => 'Account',
            'fav_mini' => 'Wishlist',
        ],
    ];

    $normalizeBrandText = fn (string $value) => $currentLocale === 'en'
        ? (preg_replace('/ستايلش/iu', 'Styliiiish', $value) ?? $value)
        : (preg_replace('/styliiiish/iu', 'ستايلش', $value) ?? $value);
    $t = fn (string $key) => $normalizeBrandText((string) ($translations[$currentLocale][$key] ?? $translations['ar'][$key] ?? $key));

    $canonicalPath = $localePrefix . '/contact-us';
@endphp
<html lang="{{ $isEnglish ? 'en' : 'ar' }}" dir="{{ $isEnglish ? 'ltr' : 'rtl' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $t('meta_desc') }}">
    <link rel="canonical" href="https://styliiiish.com{{ $canonicalPath }}">
    <link rel="alternate" hreflang="ar" href="https://styliiiish.com/ar/contact-us">
    <link rel="alternate" hreflang="en" href="https://styliiiish.com/en/contact-us">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $t('page_title') }}">
    <meta property="og:description" content="{{ $t('meta_desc') }}">
    <meta property="og:url" content="https://styliiiish.com{{ $canonicalPath }}">
    <meta property="og:image" content="{{ $wpIcon }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $t('page_title') }}">
    <meta name="twitter:description" content="{{ $t('meta_desc') }}">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ $wpIcon }}">
    <link rel="apple-touch-icon" href="{{ $wpIcon }}">
    <title>{{ $t('page_title') }}</title>

    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => 'Styliiiish Fashion House',
            'url' => 'https://styliiiish.com' . $canonicalPath,
            'telephone' => '+20 105 087 4255',
            'email' => 'email@styliiiish.com',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => '1 Nabil Khalil Street, behind Gad Restaurant, Nasr City',
                'addressLocality' => 'Cairo',
                'addressCountry' => 'EG',
            ],
            'openingHours' => 'Sa-Fr 11:00-19:00',
            'sameAs' => [
                'https://www.facebook.com/Styliiish.Egypt/',
                'https://www.instagram.com/styliiish.egypt/',
                'https://g.page/styliish'
            ]
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>

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
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.65;
        }

        a { color: inherit; text-decoration: none; }

        .container {
            width: min(1120px, 92%);
            margin: 0 auto;
        }

        .header {
            background: #fff;
            border-bottom: 1px solid var(--line);
            position: sticky;
            top: 0;
            z-index: 40;
            box-shadow: 0 8px 24px rgba(23, 39, 59, 0.06);
        }

        .header-inner {
            min-height: 78px;
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 14px;
            align-items: center;
        }

        .brand {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .brand-logo {
            height: 38px;
            max-width: min(220px, 38vw);
            object-fit: contain;
        }

        .brand-tag {
            color: var(--muted);
            font-size: 12px;
            font-weight: 600;
        }

        .nav {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 8px;
            background: #f9fbff;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 6px;
        }

        .nav a {
            font-size: 14px;
            font-weight: 700;
            color: var(--secondary);
            padding: 8px 12px;
            border-radius: 8px;
            transition: .2s ease;
        }

        .nav a:hover,
        .nav a.active {
            color: var(--primary);
            background: #fff4f5;
        }

        .lang-switch {
            position: relative;
            display: inline-grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            direction: ltr;
            width: 110px;
            height: 34px;
            background: rgba(23, 39, 59, 0.1);
            border: 1px solid rgba(23, 39, 59, 0.18);
            border-radius: 999px;
            padding: 3px;
            overflow: hidden;
        }

        .lang-indicator {
            position: absolute;
            top: 3px;
            width: calc(50% - 3px);
            height: calc(100% - 6px);
            background: #fff;
            border-radius: 999px;
            transition: .25s ease;
            z-index: 1;
        }

        .lang-switch.is-ar .lang-indicator { left: 3px; }
        .lang-switch.is-en .lang-indicator { right: 3px; }

        .lang-switch a {
            position: relative;
            z-index: 2;
            text-align: center;
            font-size: 12px;
            font-weight: 800;
            color: var(--secondary);
            opacity: .75;
            padding: 5px 0;
        }

        .lang-switch a.active {
            opacity: 1;
            color: var(--secondary);
        }

        .hero {
            padding: 34px 0 16px;
        }

        .hero-box {
            background: linear-gradient(160deg, #ffffff 0%, #fff4f5 100%);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(23, 39, 59, 0.07);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--soft);
            color: var(--primary);
            border-radius: 999px;
            padding: 7px 12px;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        h1 {
            margin: 0 0 10px;
            font-size: clamp(28px, 4vw, 44px);
            line-height: 1.2;
        }

        .hero-box p {
            margin: 0;
            color: var(--muted);
            max-width: 860px;
            font-size: 16px;
        }

        .status-row {
            margin-top: 16px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px 14px;
            font-size: 14px;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 10px;
            border-radius: 999px;
            font-weight: 800;
            font-size: 12px;
            border: 1px solid transparent;
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

        .section {
            padding: 12px 0 28px;
        }

        .grid {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 16px;
        }

        .card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 18px;
            box-shadow: 0 8px 20px rgba(23, 39, 59, 0.05);
        }

        .card h2 {
            margin: 0 0 8px;
            font-size: 24px;
        }

        .desc {
            margin: 0 0 14px;
            color: var(--muted);
            font-size: 14px;
        }

        .contact-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            gap: 10px;
        }

        .contact-list li {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 11px 12px;
            background: #fbfcff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .contact-list strong {
            color: var(--secondary);
            font-size: 13px;
        }

        .contact-list span,
        .contact-list a {
            color: var(--muted);
            font-size: 14px;
            font-weight: 600;
        }

        .contact-list a:hover { color: var(--primary); }

        .address {
            margin: 10px 0 14px;
            color: var(--muted);
            display: grid;
            gap: 3px;
            font-size: 14px;
            font-style: normal;
        }

        .btn-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            border: 1px solid var(--line);
            border-radius: 10px;
            min-height: 42px;
            padding: 10px 14px;
            font-size: 14px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: .2s ease;
        }

        .btn-primary {
            color: #fff;
            background: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover { background: var(--primary-2); }

        .btn-light {
            color: var(--secondary);
            background: #fff;
        }

        .btn-light:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .form {
            display: grid;
            gap: 10px;
        }

        .input,
        .textarea {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #fff;
            font: inherit;
            color: var(--secondary);
            padding: 10px 12px;
            outline: none;
        }

        .textarea {
            resize: vertical;
            min-height: 130px;
        }

        .input:focus,
        .textarea:focus {
            border-color: rgba(var(--wf-main-rgb), 0.45);
            box-shadow: 0 0 0 3px rgba(var(--wf-main-rgb), 0.12);
        }

        .form-note {
            margin: 4px 0 0;
            color: var(--muted);
            font-size: 12px;
        }

        .map-wrap {
            margin-top: 12px;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid var(--line);
            box-shadow: 0 8px 20px rgba(23, 39, 59, 0.08);
            background: #fff;
        }

        .map-wrap iframe {
            width: 100%;
            height: 360px;
            border: 0;
            display: block;
        }

        .site-footer {
            margin-top: 8px;
            background: #0f1a2a;
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
            color: #b8c2d1;
            font-size: 14px;
        }

        .footer-links {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            gap: 7px;
        }

        .footer-links a {
            color: #b8c2d1;
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
            color: #b8c2d1;
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
            color: #b8c2d1;
            font-size: 13px;
        }

        .footer-mini-nav a:hover {
            color: #fff;
        }

        @media (max-width: 900px) {
            .header-inner {
                grid-template-columns: 1fr;
                padding: 10px 0;
            }

            .brand,
            .nav,
            .header-tools {
                justify-content: center;
                text-align: center;
            }

            .grid {
                grid-template-columns: 1fr;
            }

            .footer-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .hero { padding-top: 20px; }
            .hero-box,
            .card { padding: 14px; border-radius: 14px; }
            .card h2 { font-size: 21px; }
            .map-wrap iframe { height: 300px; }

            .footer-grid {
                grid-template-columns: 1fr;
                gap: 14px;
                padding: 22px 0 14px;
            }

            .footer-brand,
            .footer-col {
                padding: 12px;
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
        }
    </style>
    @include('partials.shared-home-header-styles')
</head>
<body>
@include('partials.shared-home-header')

<section class="hero">
    <div class="container hero-box">
        <span class="badge">{{ $t('hero_badge') }}</span>
        <h1>{{ $t('hero_title') }}</h1>
        <p>{{ $t('hero_desc') }}</p>

        <div class="status-row">
            <span><strong>{{ $t('status_label') }}:</strong></span>
            <span class="status-pill {{ $isOpenNow ? 'is-open' : 'is-closed' }}">{{ $isOpenNow ? $t('open') : $t('closed') }}</span>
            <span><strong>{{ $t('open_hours') }}:</strong> {{ $t('open_hours_value') }}</span>
        </div>
    </div>
</section>

<section class="section">
    <div class="container grid">
        <article class="card">
            <h2>{{ $t('section_get_in_touch') }}</h2>
            <p class="desc">{{ $t('section_get_in_touch_desc') }}</p>

            <ul class="contact-list">
                <li><strong>{{ $t('phone') }}</strong><a href="tel:+201050874255">+20 105 087 4255</a></li>
                <li><strong>{{ $t('email') }}</strong><a href="mailto:email@styliiiish.com">email@styliiiish.com</a></li>
                <li><strong>{{ $t('visit_note') }}</strong><span>{{ $t('visit_note_value') }}</span></li>
            </ul>
        </article>

        <article class="card">
            <h2>{{ $t('studio_title') }}</h2>
            <p class="desc">{{ $t('studio_desc') }}</p>

            <h3 style="margin:0 0 6px; font-size:16px;">{{ $t('address_title') }}</h3>
            <address class="address">
                <span>{{ $t('address_line_1') }}</span>
                <span>{{ $t('address_line_2') }}</span>
                <span>{{ $t('address_line_3') }}</span>
                <span>{{ $t('address_line_4') }}</span>
            </address>

            <div class="btn-row">
                <a class="btn btn-light" href="https://maps.app.goo.gl/MCdcFEcFoR4tEjpT8" target="_blank" rel="noopener">{{ $t('view_map') }}</a>
                <a class="btn btn-primary" href="tel:+201050874255">{{ $t('phone') }}</a>
            </div>
        </article>
    </div>
</section>

<section class="section">
    <div class="container grid">
        <article class="card">
            <h2>{{ $t('message_title') }}</h2>
            <p class="desc">{{ $t('message_desc') }}</p>

            <form class="form" action="mailto:email@styliiiish.com" method="post" enctype="text/plain">
                <input class="input" type="text" name="name" placeholder="{{ $t('field_name') }}" required>
                <input class="input" type="email" name="email" placeholder="{{ $t('field_email') }}" required>
                <input class="input" type="text" name="subject" placeholder="{{ $t('field_subject') }}" required>
                <textarea class="textarea" name="message" placeholder="{{ $t('field_message') }}"></textarea>
                <button class="btn btn-primary" type="submit">{{ $t('send_message') }}</button>
                <p class="form-note">{{ $t('send_note') }}</p>
            </form>
        </article>

        <article class="card">
            <h2>{{ $t('map_title') }}</h2>
            <p class="desc">{{ $t('map_desc') }}</p>

            <div class="map-wrap">
                <iframe
                    src="https://www.google.com/maps?q=30.0639814,31.3461491&z=16&output=embed"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    title="Styliiiish Location">
                </iframe>
            </div>
        </article>
    </div>
</section>

<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-brand">
            <img class="footer-brand-logo" src="{{ $wpLogo }}" alt="Styliiiish" onerror="this.onerror=null;this.src='/brand/logo.png';">
            <h4>{{ $t('footer_title') }}</h4>
            <p>{{ $t('footer_desc') }}</p>
            <p>{{ $t('footer_hours') }}</p>
            <div class="footer-contact-row">
                <a href="{{ $localePrefix }}/contact-us">{{ $t('contact_us') }}</a>
                <a href="tel:+201050874255">{{ $t('direct_call') }}</a>
            </div>
        </div>

        <div class="footer-col">
            <h5>{{ $t('quick_links') }}</h5>
            <ul class="footer-links">
                <li><a href="{{ $localePrefix }}">{{ $t('nav_home') }}</a></li>
                <li><a href="{{ $localePrefix }}/shop">{{ $t('nav_shop') }}</a></li>
                <li><a href="{{ $localePrefix }}/blog">{{ $t('nav_blog') }}</a></li>
                <li><a href="{{ $localePrefix }}/contact-us">{{ $t('nav_contact') }}</a></li>
                <li><a href="{{ $localePrefix }}/categories">{{ $t('categories') }}</a></li>
                <li><a href="{{ $localePrefix }}/marketplace">{{ $t('nav_marketplace') }}</a></li>
                <li><a href="https://styliiiish.com/my-dresses/" target="_blank" rel="noopener">{{ $t('nav_sell') }}</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h5>{{ $t('official_info') }}</h5>
            <ul class="footer-links">
                <li><a href="https://maps.app.goo.gl/MCdcFEcFoR4tEjpT8" target="_blank" rel="noopener">{{ $t('official_address') }}</a></li>
                <li><a href="tel:+201050874255">+2 010-5087-4255</a></li>
                <li><a href="mailto:email@styliiiish.com">email@styliiiish.com</a></li>
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
        <a href="{{ $localePrefix }}">{{ $t('home_mini') }}</a>
        <a href="{{ $localePrefix }}/shop">{{ $t('shop_mini') }}</a>
        <a href="https://styliiiish.com/cart/" target="_blank" rel="noopener">{{ $t('cart_mini') }}</a>
        <a href="https://styliiiish.com/my-account/" target="_blank" rel="noopener">{{ $t('account_mini') }}</a>
        <a href="https://styliiiish.com/wishlist/" target="_blank" rel="noopener">{{ $t('fav_mini') }}</a>
    </div>
</footer>
</body>
</html>

