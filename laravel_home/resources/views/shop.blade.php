<!DOCTYPE html>
@php
    $currentLocale = $currentLocale ?? 'ar';
    $localePrefix = $localePrefix ?? '/ar';
    $isEnglish = $currentLocale === 'en';
    $wpBaseUrl = rtrim((string) ($wpBaseUrl ?? env('WP_PUBLIC_URL', request()->getSchemeAndHttpHost())), '/');
    $canonicalPath = $localePrefix . '/shop';

    $translations = [
        'ar' => [
            'page_title' => 'المتجر | ستايلش',
            'meta_desc' => 'تسوقي الآن من متجر ستايلش أحدث فساتين السهرة والزفاف والخطوبة في مصر مع عروض حصرية، شحن سريع، وتجربة شراء آمنة وحديثة.',
        ],
        'en' => [
            'page_title' => 'Shop | Styliiiish',
            'meta_desc' => 'Shop the latest evening, bridal, and engagement dresses at Styliiiish with exclusive offers, fast Egypt-wide shipping, and a secure modern checkout experience.',
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
    <link rel="alternate" hreflang="ar" href="{{ $wpBaseUrl }}/ar/shop">
    <link rel="alternate" hreflang="en" href="{{ $wpBaseUrl }}/en/shop">
    <link rel="alternate" hreflang="x-default" href="{{ $wpBaseUrl }}/ar/shop">
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

        .shop-head { padding: 24px 0 14px; }
        .shop-head h1 { margin: 0 0 6px; font-size: clamp(25px, 4vw, 35px); }
        .shop-head p { margin: 0; color: var(--muted); }

        .toolbar { display: grid; grid-template-columns: 1fr auto; gap: 10px; margin: 14px 0 10px; }
        .search-form { display: flex; border: 1px solid var(--line); border-radius: 12px; overflow: hidden; background: #fff; }
        .search-input { flex: 1; border: 0; padding: 0 12px; min-height: 44px; font-size: 14px; outline: 0; }
        .search-btn { border: 0; background: var(--secondary); color: #fff; padding: 0 14px; font-weight: 700; cursor: pointer; }
        .sort { border: 1px solid var(--line); border-radius: 12px; background: #fff; min-height: 44px; padding: 0 10px; font-size: 14px; }

        .results-meta {
            color: var(--muted);
            font-size: 13px;
            margin: 0 0 12px;
            min-height: 20px;
        }

        .grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; }
        .card { background: #fff; border: 1px solid var(--line); border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 8px 20px rgba(23,39,59,.05); }
        .media { position: relative; }
        .thumb { width: 100%; aspect-ratio: 3/4; object-fit: cover; background: #f2f2f5; }
        .badge { position: absolute; top: 10px; right: 10px; background: rgba(213,21,34,.92); color: #fff; border-radius: 999px; padding: 5px 9px; font-size: 11px; font-weight: 800; }
        .content { padding: 11px; display: flex; flex-direction: column; gap: 8px; height: 100%; }
        .name { margin: 0; font-size: 14px; line-height: 1.4; min-height: 40px; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; overflow: hidden; }
        .prices { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .price { color: var(--primary); font-weight: 900; font-size: 17px; }
        .old { color: #8b8b97; text-decoration: line-through; font-size: 13px; }
        .save { display: inline-flex; width: fit-content; padding: 4px 8px; border-radius: 999px; background: #fff3f4; color: var(--primary); font-size: 11px; font-weight: 800; }
        .actions { margin-top: auto; display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        .btn-buy, .btn-view { min-height: 40px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; }
        .btn-buy { background: var(--primary); color: #fff; }
        .btn-view { border: 1px solid var(--line); color: var(--secondary); background: #fff; }

        .skeleton {
            border-radius: 12px;
            background: linear-gradient(90deg, #f2f2f5 25%, #e8e8ee 37%, #f2f2f5 63%);
            background-size: 400% 100%;
            animation: shimmer 1.2s infinite;
        }

        .skeleton-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 10px;
        }

        .skeleton-thumb { width: 100%; aspect-ratio: 3/4; }
        .skeleton-line { height: 12px; margin-top: 8px; }
        .skeleton-line.short { width: 60%; }

        @keyframes shimmer {
            0% { background-position: 100% 0; }
            100% { background-position: -100% 0; }
        }

        .empty { background: #fff; border: 1px solid var(--line); border-radius: 14px; padding: 24px; text-align: center; margin-bottom: 24px; }

        .load-status {
            margin: 18px 0 34px;
            text-align: center;
            color: var(--muted);
            font-size: 13px;
            min-height: 20px;
        }

        #lazySentinel {
            width: 100%;
            height: 1px;
        }

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

        @media (max-width: 980px) {
            .header-inner { grid-template-columns: 1fr; padding: 10px 0; }
            .brand, .nav { justify-content: center; text-align: center; }
            .grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .footer-grid { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 640px) {
            .toolbar { grid-template-columns: 1fr; }
            .grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
            .actions { grid-template-columns: 1fr; }
            .nav { overflow-x: auto; justify-content: flex-start; }
            .name { min-height: auto; font-size: 13px; }
            .price { font-size: 15px; }
        }

        @media (max-width: 390px) {
            .grid { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="topbar"><div class="container">المتجر الرسمي • شحن داخل مصر 2–10 أيام</div></div>

    <header class="header">
        <div class="container header-inner">
            <a class="brand" href="/">
                <img class="brand-logo" src="{{ $wpLogo }}" alt="Styliiiish" onerror="this.onerror=null;this.src='/brand/logo.png';">
                <span class="brand-sub">لأن كل امرأة تستحق أن تتألق</span>
            </a>

            <nav class="nav" aria-label="Main Navigation">
                <a href="/">الرئيسية</a>
                <a class="active" href="/shop">المتجر</a>
                <a href="/ar/marketplace">الماركت بليس</a>
                <a href="https://styliiiish.com/my-dresses/" target="_blank" rel="noopener">بيعي فستانك</a>
                <a href="https://styliiiish.com/blog/" target="_blank" rel="noopener">المدونة</a>
            </nav>

            <div style="display:flex; gap:8px; justify-content:center;">
                <a class="head-btn" href="https://styliiiish.com/my-account/" target="_blank" rel="noopener" title="حسابي" aria-label="حسابي">👤</a>
                <a class="head-btn" href="https://styliiiish.com/wishlist/" target="_blank" rel="noopener" title="قائمة الأمنيات" aria-label="قائمة الأمنيات">❤</a>
                <a class="head-btn" href="https://styliiiish.com/cart/" target="_blank" rel="noopener" title="السلة" aria-label="السلة">🛒</a>
            </div>
        </div>
    </header>

    <main class="container">
        <section class="shop-head">
            <h1>المتجر</h1>
            <p>اكتشفي أحدث فساتين السهرة والزفاف والخطوبة بأسعار تنافسية.</p>
        </section>

        <section class="toolbar">
            <form class="search-form" id="searchForm" method="GET" action="/shop">
                <input class="search-input" type="search" id="qInput" name="q" value="{{ $search }}" placeholder="ابحثي عن منتج..." aria-label="ابحثي عن منتج">
                <button class="search-btn" type="submit">بحث</button>
            </form>

            <select class="sort" id="sortSelect" name="sort" aria-label="ترتيب المنتجات">
                <option value="random" {{ $sort === 'random' ? 'selected' : '' }}>ترتيب عشوائي</option>
                <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>السعر: من الأقل للأعلى</option>
                <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>السعر: من الأعلى للأقل</option>
            </select>
        </section>

        <p class="results-meta" id="resultsMeta"></p>

        <section class="grid" id="productsGrid"></section>

        <div class="empty" id="emptyState" style="display:none;">لا توجد منتجات مطابقة الآن. جرّبي البحث بكلمات مختلفة.</div>
        <div class="load-status" id="loadStatus"></div>
        <div id="lazySentinel" aria-hidden="true"></div>
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

    <script>
        (() => {
            const grid = document.getElementById('productsGrid');
            const resultsMeta = document.getElementById('resultsMeta');
            const emptyState = document.getElementById('emptyState');
            const loadStatus = document.getElementById('loadStatus');
            const lazySentinel = document.getElementById('lazySentinel');
            const qInput = document.getElementById('qInput');
            const sortSelect = document.getElementById('sortSelect');
            const searchForm = document.getElementById('searchForm');

            const params = new URLSearchParams(window.location.search);
            const state = {
                q: params.get('q') ?? qInput.value ?? '',
                sort: params.get('sort') ?? sortSelect.value ?? 'random',
            };

            const renderState = {
                allProducts: [],
                renderedCount: 0,
                chunkSize: 12,
                observer: null,
            };

            qInput.value = state.q;
            sortSelect.value = state.sort;

            const fmt = new Intl.NumberFormat('en-US');
            const localePrefix = @json($localePrefix);

            const buildQuery = () => {
                const query = new URLSearchParams();
                if (state.q.trim() !== '') query.set('q', state.q.trim());
                if (state.sort !== 'random') query.set('sort', state.sort);
                return query;
            };

            const updateUrl = () => {
                const query = buildQuery().toString();
                const next = query ? `${localePrefix}/shop?${query}` : `${localePrefix}/shop`;
                window.history.pushState({}, '', next);
            };

            const skeleton = () => {
                grid.innerHTML = Array.from({ length: 8 }).map(() => `
                    <article class="skeleton-card">
                        <div class="skeleton skeleton-thumb"></div>
                        <div class="skeleton skeleton-line"></div>
                        <div class="skeleton skeleton-line short"></div>
                    </article>
                `).join('');
            };

            const productCard = (product) => {
                const priceText = product.price > 0 ? `${fmt.format(product.price)} ج.م` : 'تواصل لمعرفة السعر';
                const oldText = product.is_sale ? `<span class="old">${fmt.format(product.regular_price)} ج.م</span>` : '';
                const badge = product.is_sale ? `<span class="badge">خصم ${product.discount}%</span>` : '';
                const save = product.is_sale ? `<span class="save">وفّري ${fmt.format(product.saving)} ج.م</span>` : '';

                return `
                    <article class="card">
                        <div class="media">
                            <img class="thumb" src="${product.image}" alt="${product.title}" loading="lazy">
                            ${badge}
                        </div>
                        <div class="content">
                            <h3 class="name">${product.title}</h3>
                            <div class="prices">
                                <span class="price">${priceText}</span>
                                ${oldText}
                            </div>
                            ${save}
                            <div class="actions">
                                <a class="btn-buy" href="${localePrefix}/item/${product.slug}">اطلبي الآن</a>
                                <a class="btn-view" href="${localePrefix}/item/${product.slug}">معاينة</a>
                            </div>
                        </div>
                    </article>
                `;
            };

            const renderMeta = (total, rendered) => {
                if (!total) {
                    resultsMeta.textContent = 'لا توجد نتائج حاليًا.';
                    return;
                }
                resultsMeta.textContent = `عرض ${rendered} من ${total} منتج`;
            };

            const renderNextChunk = () => {
                const { allProducts, renderedCount, chunkSize } = renderState;

                if (renderedCount >= allProducts.length) {
                    loadStatus.textContent = allProducts.length ? 'تم عرض كل المنتجات' : '';
                    return;
                }

                const nextItems = allProducts.slice(renderedCount, renderedCount + chunkSize);
                const html = nextItems.map(productCard).join('');
                grid.insertAdjacentHTML('beforeend', html);

                renderState.renderedCount += nextItems.length;
                renderMeta(allProducts.length, renderState.renderedCount);

                if (renderState.renderedCount < allProducts.length) {
                    loadStatus.textContent = 'جاري تحميل المزيد...';
                } else {
                    loadStatus.textContent = 'تم عرض كل المنتجات';
                }
            };

            const setupLazyObserver = () => {
                if (renderState.observer) {
                    renderState.observer.disconnect();
                }

                renderState.observer = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            renderNextChunk();
                        }
                    });
                }, {
                    root: null,
                    rootMargin: '200px 0px 200px 0px',
                    threshold: 0,
                });

                renderState.observer.observe(lazySentinel);
            };

            const fetchProducts = async (pushHistory = true) => {
                skeleton();
                emptyState.style.display = 'none';

                const query = buildQuery();
                const endpoint = query.toString() ? `${localePrefix}/shop?${query.toString()}` : `${localePrefix}/shop`;
                const response = await fetch(endpoint, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    grid.innerHTML = '';
                    loadStatus.textContent = '';
                    resultsMeta.textContent = 'حدث خطأ أثناء تحميل المنتجات.';
                    return;
                }

                const data = await response.json();
                const list = data.products || [];

                if (list.length === 0) {
                    grid.innerHTML = '';
                    emptyState.style.display = 'block';
                    loadStatus.textContent = '';
                    renderMeta(0, 0);
                    if (pushHistory) updateUrl();
                    return;
                }

                renderState.allProducts = list;
                renderState.renderedCount = 0;
                grid.innerHTML = '';
                renderNextChunk();
                setupLazyObserver();

                if (pushHistory) updateUrl();
            };

            searchForm.addEventListener('submit', (event) => {
                event.preventDefault();
                state.q = qInput.value.trim();
                fetchProducts(true);
            });

            sortSelect.addEventListener('change', () => {
                state.sort = sortSelect.value;
                fetchProducts(true);
            });

            window.addEventListener('popstate', () => {
                const qs = new URLSearchParams(window.location.search);
                state.q = qs.get('q') ?? '';
                state.sort = qs.get('sort') ?? 'random';
                qInput.value = state.q;
                sortSelect.value = state.sort;
                fetchProducts(false);
            });

            fetchProducts(false);
        })();
    </script>
</body>
</html>
