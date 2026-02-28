<!DOCTYPE html>
@php
    $currentLocale = $currentLocale ?? 'ar';
    $localePrefix = $localePrefix ?? '/ar';
    $isEnglish = $currentLocale === 'en';
    $wpBaseUrl = rtrim((string) ($wpBaseUrl ?? env('WP_PUBLIC_URL', request()->getSchemeAndHttpHost())), '/');
    $canonicalPath = $localePrefix . '/ads-landing';

    $translations = [
        'ar' => [
            'page_title' => 'عروض خاصة | ستايلش',
            'meta_desc' => 'اكتشفي عروض ستايلش الحصرية على فساتين السهرة والزفاف والخطوبة بخصومات قوية لفترة محدودة مع توصيل سريع داخل مصر.',
        ],
        'en' => [
            'page_title' => 'Special Offers | Styliiiish',
            'meta_desc' => 'Discover exclusive Styliiiish offers on evening, bridal, and engagement dresses with limited-time discounts and fast delivery across Egypt.',
        ],
    ];

    $normalizeBrandText = fn (string $value) => $currentLocale === 'en'
        ? (preg_replace('/ستايلش/iu', 'Styliiiish', $value) ?? $value)
        : (preg_replace('/styliiiish/iu', 'ستايلش', $value) ?? $value);
    $t = fn (string $key) => $normalizeBrandText((string) ($translations[$currentLocale][$key] ?? $translations['ar'][$key] ?? $key));

    $wpLogo = 'https://styliiiish.com/wp-content/uploads/2025/11/ChatGPT-Image-Nov-2-2025-03_11_14-AM-e1762046066547.png';
    $wpIcon = 'https://styliiiish.com/wp-content/uploads/2025/11/cropped-ChatGPT-Image-Nov-2-2025-03_11_14-AM-e1762046066547.png';
@endphp
<html lang="{{ $isEnglish ? 'en' : 'ar' }}" dir="{{ $isEnglish ? 'ltr' : 'rtl' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $t('meta_desc') }}">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <link rel="canonical" href="{{ $wpBaseUrl }}{{ $canonicalPath }}">
    <link rel="alternate" hreflang="ar" href="{{ $wpBaseUrl }}/ar/ads-landing">
    <link rel="alternate" hreflang="en" href="{{ $wpBaseUrl }}/en/ads-landing">
    <link rel="alternate" hreflang="x-default" href="{{ $wpBaseUrl }}/ar/ads-landing">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $isEnglish ? 'Styliiiish' : 'ستايلش' }}">
    <meta property="og:title" content="{{ $t('page_title') }}">
    <meta property="og:description" content="{{ $t('meta_desc') }}">
    <meta property="og:url" content="{{ $wpBaseUrl }}{{ $canonicalPath }}">
    <meta property="og:image" content="{{ $wpIcon }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $t('page_title') }}">
    <meta name="twitter:description" content="{{ $t('meta_desc') }}">
    <meta name="twitter:image" content="{{ $wpIcon }}">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ $wpIcon }}">
    <link rel="apple-touch-icon" href="{{ $wpIcon }}">
    <title>{{ $t('page_title') }}</title>
    <style>
        :root {
            --primary: #d51522;
            --secondary: #17273B;
            --bg: #f6f7fb;
            --card: #ffffff;
            --line: rgba(189, 189, 189, .4);
            --muted: #5a6678;
            --success: #0a8f5b;
        }

        * { box-sizing: border-box; }
        body { margin: 0; font-family: "Segoe UI", Tahoma, Arial, sans-serif; background: var(--bg); color: var(--secondary); }
        a { text-decoration: none; color: inherit; }
        .container { width: min(1100px, 92%); margin: 0 auto; }

        .hero { padding: 10px 0 8px; }
        .hero-wrap {
            background: linear-gradient(160deg, #ffffff 0%, #fff4f5 100%);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 14px;
            display: grid;
            grid-template-columns: 1.45fr .55fr;
            gap: 10px;
            align-items: center;
            box-shadow: 0 10px 20px rgba(23,39,59,.06);
        }

        .badge { display: inline-flex; background: #fff; border: 1px solid var(--line); border-radius: 999px; padding: 6px 10px; font-size: 11px; font-weight: 800; color: var(--primary); }
        .landing-brand {
            display: inline-flex;
            margin-bottom: 8px;
            align-items: center;
        }
        .landing-brand img {
            height: 36px;
            width: auto;
            max-width: min(220px, 55vw);
            object-fit: contain;
        }
        h1 { margin: 8px 0 6px; font-size: clamp(22px, 3.3vw, 32px); line-height: 1.2; }
        .lead { margin: 0 0 8px; color: var(--muted); font-size: 14px; }

        .points { margin: 0 0 10px; padding: 0; list-style: none; display: grid; gap: 6px; }
        .points li { background: #fff; border: 1px solid var(--line); border-radius: 10px; padding: 6px 8px; font-size: 12px; font-weight: 700; }

        .actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn { min-height: 38px; padding: 0 14px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px; }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-light { background: #fff; border: 1px solid var(--line); }

        .promo-box {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 10px;
            text-align: center;
            display: grid;
            gap: 8px;
        }

        .promo-box strong { display: block; font-size: 24px; color: var(--primary); line-height: 1; }
        .promo-box span { display: block; font-size: 12px; color: var(--muted); margin-top: 2px; }

        .mini-stat { border: 1px solid var(--line); border-radius: 10px; padding: 8px; background: #fff; }
        .mini-stat b { color: var(--primary); font-size: 16px; display: block; }
        .mini-stat small { color: var(--muted); font-size: 11px; }

        .section { padding: 4px 0 28px; }
        .section-head { display: flex; justify-content: space-between; align-items: center; gap: 10px; margin-bottom: 12px; }
        .section-head h2 { margin: 0; font-size: clamp(22px, 3vw, 31px); }
        .section-head p { margin: 0; color: var(--muted); font-size: 14px; }

        .grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }

        .product-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 8px 20px rgba(23,39,59,.05);
            transition: .25s ease;
        }

        .product-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 30px rgba(23,39,59,.12);
            border-color: rgba(213,21,34,.35);
        }

        .media { position: relative; }
        .thumb { width: 100%; aspect-ratio: 3/4; object-fit: cover; background: #f2f2f5; }
        .sale-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(213,21,34,.9);
            color: #fff;
            border-radius: 999px;
            padding: 5px 9px;
            font-size: 11px;
            font-weight: 800;
        }

        .content { padding: 11px; display: flex; flex-direction: column; gap: 8px; height: 100%; }
        .name {
            margin: 0;
            font-size: 14px;
            line-height: 1.45;
            min-height: 40px;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
        }

        .prices { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .price { color: var(--primary); font-size: 17px; font-weight: 900; }
        .old { color: #8b8b97; text-decoration: line-through; font-size: 13px; }
        .save { display: inline-flex; width: fit-content; padding: 4px 8px; border-radius: 999px; background: #f2fff8; color: var(--success); font-size: 11px; font-weight: 800; }

        .card-actions { margin-top: auto; display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        .btn-buy, .btn-view {
            min-height: 40px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 800;
        }
        .btn-buy { background: var(--primary); color: #fff; }
        .btn-view { background: #fff; border: 1px solid var(--line); }

        .bottom-cta {
            margin-top: 16px;
            background: linear-gradient(120deg, var(--secondary), #22354a);
            color: #fff;
            border-radius: 16px;
            padding: 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .bottom-cta strong { font-size: 20px; }
        .bottom-cta p { margin: 4px 0 0; color: #d7e0ed; font-size: 14px; }
        .bottom-cta .btn-light { min-width: 180px; }

        .cards { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
        .card { background: #fff; border: 1px solid var(--line); border-radius: 14px; padding: 14px; }
        .card h3 { margin: 0 0 6px; font-size: 17px; }
        .card p { margin: 0; color: var(--muted); font-size: 14px; }

        .site-footer { margin-top: 10px; background: #0f1a2a; color: #fff; border-top: 4px solid var(--primary); }
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

        @media (max-width: 900px) {
            .hero-wrap { grid-template-columns: 1fr; }
            .grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .cards { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 640px) {
            .hero { padding-top: 6px; }
            .hero-wrap { padding: 12px; border-radius: 12px; }
            .landing-brand img { height: 30px; max-width: 180px; }
            .lead { font-size: 13px; }
            .actions .btn { width: 100%; }
            .grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
            .card-actions { grid-template-columns: 1fr; }
            .bottom-cta .btn-light { width: 100%; }
        }

        @media (max-width: 390px) {
            .grid { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr; }
        }
    </style>
    @include('partials.shared-home-header-styles')
</head>
<body>
    @include('partials.shared-home-header')

    <main class="container">
        <section class="hero">
            <div class="hero-wrap">
                <div>
                    <a class="landing-brand" href="/" aria-label="Styliiiish Home">
                        <img src="{{ $wpLogo }}" alt="Styliiiish" onerror="this.onerror=null;this.src='/brand/logo.png';">
                    </a>
                    <span class="badge">عرض خاص من Styliiiish</span>
                    <h1>احجزي إطلالتك الآن بخصم يصل إلى 50%</h1>
                    <p class="lead">اكتشفي موديلات السهرة والزفاف والخطوبة الأكثر طلبًا مع توصيل سريع داخل مصر وسياسات واضحة.</p>

                    <ul class="points">
                        <li>✔️ خصومات حقيقية على منتجات مختارة</li>
                        <li>✔️ شحن داخل مصر خلال 2–10 أيام</li>
                        <li>✔️ شراء آمن وتجربة سلسة</li>
                    </ul>

                    <div class="actions">
                        <a class="btn btn-primary" href="/shop">تسوقي الآن</a>
                        <a class="btn btn-light" href="https://styliiiish.com/my-dresses/" target="_blank" rel="noopener">بيعي فستانك</a>
                    </div>
                </div>

                <aside class="promo-box">
                    <strong>50%</strong>
                    <span>خصم على مختارات مميزة</span>
                    <div class="mini-stat">
                        <b>{{ number_format((int) ($total ?? 0)) }}+</b>
                        <small>منتج جاهز للطلب الآن</small>
                    </div>
                    <div class="mini-stat">
                        <b>2-10 أيام</b>
                        <small>توصيل داخل مصر</small>
                    </div>
                </aside>
            </div>
        </section>

        <section class="section">
            <div class="section-head">
                <div>
                    <h2>اشتري الآن مباشرة</h2>
                    <p>منتجات ظاهرة فورًا لتسهيل الشراء من الإعلانات بدون أي خطوات إضافية.</p>
                </div>
                <a class="btn btn-light" href="/shop">عرض كل المنتجات</a>
            </div>

            <div class="grid">
                @foreach(($products ?? collect()) as $product)
                    @php
                        $price = (float) ($product->price ?? 0);
                        $regular = (float) ($product->regular_price ?? 0);
                        $isSale = $regular > 0 && $price > 0 && $regular > $price;
                        $discount = $isSale ? round((($regular - $price) / $regular) * 100) : 0;
                        $saving = $isSale ? ($regular - $price) : 0;
                        $image = $product->image ?: 'https://styliiiish.com/wp-content/uploads/woocommerce-placeholder.png';
                    @endphp

                    <article class="product-card">
                        <div class="media">
                            <img class="thumb" src="{{ $image }}" alt="{{ $product->post_title }}" loading="lazy">
                            @if($isSale)
                                <span class="sale-badge">خصم {{ $discount }}%</span>
                            @endif
                        </div>
                        <div class="content">
                            <h3 class="name">{{ $product->post_title }}</h3>
                            <div class="prices">
                                <span class="price">
                                    @if($price > 0)
                                        {{ number_format($price) }} ج.م
                                    @else
                                        تواصل لمعرفة السعر
                                    @endif
                                </span>
                                @if($isSale)
                                    <span class="old">{{ number_format($regular) }} ج.م</span>
                                @endif
                            </div>

                            @if($isSale)
                                <span class="save">وفّري {{ number_format($saving) }} ج.م</span>
                            @endif

                            <div class="card-actions">
                                <a class="btn-buy" href="{{ $localePrefix }}/item/{{ $product->post_name }}">اشتري الآن</a>
                                <a class="btn-view" href="{{ $localePrefix }}/item/{{ $product->post_name }}">معاينة</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="bottom-cta">
                <div>
                    <strong>جاهزة تختاري فستانك الآن؟</strong>
                    <p>أكملي الشراء فورًا أو تصفحي المتجر بالكامل للحصول على خيارات أكثر.</p>
                </div>
                <a class="btn btn-light" href="/shop">الانتقال للمتجر الكامل</a>
            </div>
        </section>

        <section class="section">
            <div class="cards">
                <article class="card">
                    <h3>تجربة تسوق سريعة</h3>
                    <p>صفحة محسنة للحملات الإعلانية لتحقيق أعلى معدل تحويل.</p>
                </article>
                <article class="card">
                    <h3>أسعار تنافسية</h3>
                    <p>مزيج قوي بين الجودة والسعر مع عروض متجددة يوميًا.</p>
                </article>
                <article class="card">
                    <h3>ثقة ووضوح</h3>
                    <p>روابط وسياسات واضحة لدعم قرار الشراء بسرعة.</p>
                </article>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container footer-grid">
            <div class="footer-brand">
                <img class="footer-brand-logo" src="{{ $wpLogo }}" alt="Styliiiish" onerror="this.onerror=null;this.src='/brand/logo.png';">
                <h4>ستيليش فاشون هاوس</h4>
                <p>نعمل بشغف على تقديم أحدث تصاميم الفساتين لتناسب كل مناسبة خاصة بك.</p>
                <p>مواعيد العمل: السبت إلى الجمعة من 11:00 صباحًا حتى 7:00 مساءً.</p>
                <div class="footer-contact-row">
                    <a href="/contact-us">تواصلي معنا</a>
                    <a href="tel:+201050874255">اتصال مباشر</a>
                </div>
            </div>

            <div class="footer-col">
                <h5>روابط سريعة</h5>
                <ul class="footer-links">
                    <li><a href="/">الرئيسية</a></li>
                    <li><a href="/shop">المتجر</a></li>
                    <li><a href="/blog">المدونة</a></li>
                    <li><a href="/about-us">من نحن</a></li>
                    <li><a href="/contact-us">تواصل معنا</a></li>
                    <li><a href="/categories">الأقسام</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h5>معلومات رسمية</h5>
                <ul class="footer-links">
                    <li><a href="https://maps.app.goo.gl/MCdcFEcFoR4tEjpT8" target="_blank" rel="noopener">1 شارع نبيل خليل، مدينة نصر، القاهرة، مصر</a></li>
                    <li><a href="tel:+201050874255">+2 010-5087-4255</a></li>
                    <li><a href="mailto:email@styliiiish.com">email@styliiiish.com</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h5>سياسات وقوانين</h5>
                <ul class="footer-links">
                    <li><a href="/about-us">من نحن</a></li>
                    <li><a href="/privacy-policy">سياسة الخصوصية</a></li>
                    <li><a href="/terms-conditions">الشروط والأحكام</a></li>
                    <li><a href="/refund-return-policy">سياسة الاسترجاع والاستبدال</a></li>
                    <li><a href="/faq">الأسئلة الشائعة</a></li>
                    <li><a href="/shipping-delivery-policy">سياسة الشحن والتوصيل</a></li>
                    <li><a href="/cookie-policy">سياسة ملفات الارتباط</a></li>
                </ul>
            </div>
        </div>

        <div class="container footer-bottom">
            <span>جميع الحقوق محفوظة © {{ date('Y') }} Styliiiish | تشغيل وتطوير <a href="https://websiteflexi.com/" target="_blank" rel="noopener">Website Flexi</a></span>
            <span><a href="https://styliiiish.com/" target="_blank" rel="noopener">styliiiish.com</a></span>
        </div>
    </footer>
</body>
</html>
