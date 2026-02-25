<!DOCTYPE html>
@php
    $currentLocale = $currentLocale ?? 'ar';
    $localePrefix = $localePrefix ?? '/ar';
    $isEnglish = $currentLocale === 'en';

    $wpBaseUrl = rtrim((string) ($wpBaseUrl ?? env('WP_PUBLIC_URL', request()->getSchemeAndHttpHost())), '/');
    $wpLogo = $wpBaseUrl . '/wp-content/uploads/2025/11/ChatGPT-Image-Nov-2-2025-03_11_14-AM-e1762046066547.png';
    $wpIcon = $wpBaseUrl . '/wp-content/uploads/2025/11/cropped-ChatGPT-Image-Nov-2-2025-03_11_14-AM-e1762046066547.png';

    $translations = [
        'ar' => [
            'page_title' => 'المدونة | ستايلش',
            'meta_desc' => 'اقرئي أحدث مقالات Styliiiish عن الموضة والفساتين ونصائح اختيار إطلالة مناسبة لكل مناسبة.',
            'brand_tag' => 'لأن كل امرأة تستحق أن تتألق',
            'nav_home' => 'الرئيسية',
            'nav_shop' => 'المتجر',
            'nav_marketplace' => 'الماركت بليس',
            'nav_sell' => 'بيعي فستانك',
            'nav_contact' => 'تواصل معنا',
            'nav_blog' => 'المدونة',
            'lang_switch' => 'تبديل اللغة',
            'hero_badge' => '📝 محتوى جديد باستمرار',
            'hero_title' => 'مدونة Styliiiish',
            'hero_desc' => 'دليلك لعالم الفساتين والموضة: أفكار للإطلالة، نصائح عملية، واتجاهات حديثة من خبراء Styliiiish.',
            'latest_articles' => 'أحدث المقالات',
            'article_count' => 'مقال منشور',
            'read_more' => 'قراءة المزيد',
            'published_on' => 'نُشر بتاريخ',
            'empty_title' => 'لا توجد مقالات متاحة الآن',
            'empty_desc' => 'يرجى العودة لاحقًا للاطلاع على أحدث المقالات.',
            'prev' => 'السابق',
            'next' => 'التالي',
            'page' => 'الصفحة',
            'of' => 'من',
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
            'page_title' => 'Blog | Styliiiish',
            'meta_desc' => 'Explore the latest Styliiiish blog posts about fashion, dresses, and practical styling tips for every occasion.',
            'brand_tag' => 'Because every woman deserves to shine',
            'nav_home' => 'Home',
            'nav_shop' => 'Shop',
            'nav_marketplace' => 'Marketplace',
            'nav_sell' => 'Sell Your Dress',
            'nav_contact' => 'Contact Us',
            'nav_blog' => 'Blog',
            'lang_switch' => 'Language Switcher',
            'hero_badge' => '📝 Fresh content regularly',
            'hero_title' => 'Styliiiish Blog',
            'hero_desc' => 'Your guide to dresses and fashion: styling ideas, practical tips, and modern trends from Styliiiish experts.',
            'latest_articles' => 'Latest Articles',
            'article_count' => 'Published Posts',
            'read_more' => 'Read More',
            'published_on' => 'Published on',
            'empty_title' => 'No articles available right now',
            'empty_desc' => 'Please check back later for the latest posts.',
            'prev' => 'Previous',
            'next' => 'Next',
            'page' => 'Page',
            'of' => 'of',
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

    $canonicalPath = $localePrefix . '/blog';
    $wpDisplayHost = preg_replace('#^https?://#', '', $wpBaseUrl);
    $wpBlogArchiveBase = $isEnglish
        ? '/blog/'
        : ('/' . ltrim((string) ($arBlogArchivePath ?? '/ar/%d9%85%d8%af%d9%88%d9%86%d8%a9/'), '/'));
@endphp
<html lang="{{ $isEnglish ? 'en' : 'ar' }}" dir="{{ $isEnglish ? 'ltr' : 'rtl' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $t('meta_desc') }}">
    <link rel="canonical" href="{{ $wpBaseUrl }}{{ $canonicalPath }}">
    <link rel="alternate" hreflang="ar" href="{{ $wpBaseUrl }}/ar/blog">
    <link rel="alternate" hreflang="en" href="{{ $wpBaseUrl }}/en/blog">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $t('page_title') }}">
    <meta property="og:description" content="{{ $t('meta_desc') }}">
    <meta property="og:url" content="{{ $wpBaseUrl }}{{ $canonicalPath }}">
    <meta property="og:image" content="{{ $wpIcon }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $t('page_title') }}">
    <meta name="twitter:description" content="{{ $t('meta_desc') }}">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ $wpIcon }}">
    <link rel="apple-touch-icon" href="{{ $wpIcon }}">
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
            --primary-2: #b70f1a;
            --secondary: var(--wf-secondary-color);
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

        .container { width: min(1180px, 92%); margin: 0 auto; }

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

        .brand { display: flex; flex-direction: column; gap: 2px; }

        .brand-logo {
            height: 40px;
            width: auto;
            max-width: min(220px, 38vw);
            object-fit: contain;
        }

        .brand-tag { color: var(--muted); font-size: 12px; font-weight: 600; }

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
        }

        .main-nav a:hover,
        .main-nav a.active {
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

        .lang-switch a.active { opacity: 1; }

        .hero {
            padding: 34px 0 18px;
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
            background: #ffeef0;
            color: var(--primary);
            border-radius: 999px;
            padding: 7px 12px;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .hero h1 {
            margin: 0 0 10px;
            font-size: clamp(28px, 4vw, 44px);
            line-height: 1.2;
        }

        .hero p { margin: 0; color: var(--muted); max-width: 840px; }

        .section { padding: 8px 0 28px; }

        .section-head {
            margin-bottom: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .section-title { margin: 0; font-size: 24px; }
        .section-meta { color: var(--muted); font-size: 14px; font-weight: 600; }

        .posts-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .post-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 8px 20px rgba(23, 39, 59, 0.05);
            transition: .25s ease;
        }

        .post-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 28px rgba(23, 39, 59, 0.12);
            border-color: rgba(213, 21, 34, 0.35);
        }

        .post-thumb {
            width: 100%;
            aspect-ratio: 16 / 10;
            object-fit: cover;
            background: #f1f3f7;
        }

        .post-content {
            padding: 14px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-height: 220px;
        }

        .post-title {
            margin: 0;
            font-size: 18px;
            line-height: 1.45;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .post-meta {
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
        }

        .post-excerpt {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .post-read {
            margin-top: auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--line);
            border-radius: 10px;
            min-height: 40px;
            font-size: 13px;
            font-weight: 700;
            color: var(--secondary);
            background: #fff;
            transition: .2s ease;
        }

        .post-read:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: #fff4f5;
        }

        .empty-box {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 24px;
            text-align: center;
        }

        .empty-box h3 { margin: 0 0 6px; }
        .empty-box p { margin: 0; color: var(--muted); }

        .pager {
            margin-top: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .pager-btn {
            border: 1px solid var(--line);
            background: #fff;
            color: var(--secondary);
            border-radius: 10px;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 700;
            min-width: 84px;
            text-align: center;
        }

        .pager-btn.disabled {
            opacity: .45;
            pointer-events: none;
        }

        .pager-info {
            color: var(--muted);
            font-size: 13px;
            font-weight: 700;
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

        .footer-links { list-style: none; margin: 0; padding: 0; display: grid; gap: 7px; }

        .footer-links a {
            color: #b8c2d1;
            font-size: 14px;
            transition: .2s ease;
        }

        .footer-links a:hover { color: #fff; }

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

        .footer-mini-nav a { color: #b8c2d1; font-size: 13px; }
        .footer-mini-nav a:hover { color: #fff; }

        @media (max-width: 980px) {
            .main-header-inner {
                grid-template-columns: 1fr;
                padding: 12px 0;
            }

            .brand,
            .main-nav,
            .header-tools {
                justify-content: center;
                text-align: center;
            }

            .posts-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .footer-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 640px) {
            .hero { padding-top: 20px; }
            .hero-box { border-radius: 14px; padding: 16px; }
            .posts-grid { grid-template-columns: 1fr; gap: 12px; }

            .footer-grid {
                grid-template-columns: 1fr;
                gap: 14px;
                padding: 22px 0 14px;
            }

            .footer-brand,
            .footer-col { padding: 12px; }

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

            .footer-mini-nav::-webkit-scrollbar { display: none; }
        }
    </style>
</head>
<body>
<header class="main-header">
    <div class="container main-header-inner">
        <a class="brand" href="{{ $localePrefix }}">
            <img class="brand-logo" src="{{ $wpLogo }}" alt="Styliiiish" onerror="this.onerror=null;this.src='/brand/logo.png';">
            <span class="brand-tag">{{ $t('brand_tag') }}</span>
        </a>

        <nav class="main-nav" aria-label="Main Navigation">
            <a href="{{ $localePrefix }}">{{ $t('nav_home') }}</a>
            <a href="{{ $localePrefix }}/shop">{{ $t('nav_shop') }}</a>
            <a class="active" href="{{ $localePrefix }}/blog">{{ $t('nav_blog') }}</a>
            <a href="{{ $localePrefix }}/marketplace">{{ $t('nav_marketplace') }}</a>
            <a href="{{ $wpBaseUrl }}/my-dresses/" target="_blank" rel="noopener">{{ $t('nav_sell') }}</a>
            <a href="{{ $localePrefix }}/contact-us">{{ $t('nav_contact') }}</a>
        </nav>

        <div class="header-tools">
            <div class="lang-switch {{ $isEnglish ? 'is-en' : 'is-ar' }}" aria-label="{{ $t('lang_switch') }}">
                <span class="lang-indicator" aria-hidden="true"></span>
                <a class="{{ $currentLocale === 'ar' ? 'active' : '' }}" href="/ar/blog">AR</a>
                <a class="{{ $currentLocale === 'en' ? 'active' : '' }}" href="/en/blog">EN</a>
            </div>
        </div>
    </div>
</header>

<section class="hero">
    <div class="container hero-box">
        <span class="badge">{{ $t('hero_badge') }}</span>
        <h1>{{ $t('hero_title') }}</h1>
        <p>{{ $t('hero_desc') }}</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <h2 class="section-title">{{ $t('latest_articles') }}</h2>
            <span class="section-meta">{{ number_format((int) $posts->total()) }} {{ $t('article_count') }}</span>
        </div>

        @if($posts->count() > 0)
            <div class="posts-grid">
                @foreach($posts as $post)
                    @php
                        $slug = rawurlencode(rawurldecode((string) $post->post_name));
                        $fallbackPostUrl = $wpBaseUrl . $wpBlogArchiveBase . $slug . '/';
                        $postUrl = !empty($post->permalink) ? (string) $post->permalink : $fallbackPostUrl;
                        $excerptSource = trim((string) ($post->post_excerpt ?: strip_tags((string) $post->post_content)));
                        $excerpt = mb_strlen($excerptSource) > 170 ? mb_substr($excerptSource, 0, 170) . '…' : $excerptSource;
                        $image = $post->image ?: ($wpBaseUrl . '/wp-content/uploads/woocommerce-placeholder.png');
                    @endphp

                    <article class="post-card">
                        <a href="{{ $postUrl }}" target="_blank" rel="noopener">
                            <img class="post-thumb" src="{{ $image }}" alt="{{ $post->post_title }}" loading="lazy">
                        </a>
                        <div class="post-content">
                            <h3 class="post-title">{{ $post->post_title }}</h3>
                            <span class="post-meta">{{ $t('published_on') }} {{ \Carbon\Carbon::parse($post->post_date)->format('Y-m-d') }}</span>
                            <p class="post-excerpt">{{ $excerpt }}</p>
                            <a class="post-read" href="{{ $postUrl }}" target="_blank" rel="noopener">{{ $t('read_more') }}</a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="pager">
                <a class="pager-btn {{ $posts->onFirstPage() ? 'disabled' : '' }}" href="{{ $posts->onFirstPage() ? '#' : $posts->previousPageUrl() }}">{{ $t('prev') }}</a>
                <span class="pager-info">{{ $t('page') }} {{ $posts->currentPage() }} {{ $t('of') }} {{ $posts->lastPage() }}</span>
                <a class="pager-btn {{ $posts->hasMorePages() ? '' : 'disabled' }}" href="{{ $posts->hasMorePages() ? $posts->nextPageUrl() : '#' }}">{{ $t('next') }}</a>
            </div>
        @else
            <div class="empty-box">
                <h3>{{ $t('empty_title') }}</h3>
                <p>{{ $t('empty_desc') }}</p>
            </div>
        @endif
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
        <span><a href="{{ $wpBaseUrl }}/" target="_blank" rel="noopener">{{ $wpDisplayHost }}</a></span>
    </div>

    <div class="container footer-mini-nav">
        <a href="{{ $localePrefix }}">{{ $t('home_mini') }}</a>
        <a href="{{ $localePrefix }}/shop">{{ $t('shop_mini') }}</a>
        <a href="{{ $wpBaseUrl }}/cart/" target="_blank" rel="noopener">{{ $t('cart_mini') }}</a>
        <a href="{{ $wpBaseUrl }}/my-account/" target="_blank" rel="noopener">{{ $t('account_mini') }}</a>
        <a href="{{ $wpBaseUrl }}/wishlist/" target="_blank" rel="noopener">{{ $t('fav_mini') }}</a>
    </div>
</footer>
</body>
</html>
