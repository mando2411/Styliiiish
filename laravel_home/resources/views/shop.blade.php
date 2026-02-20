<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    @php
        $wpLogo = 'https://styliiiish.com/wp-content/uploads/2025/11/ChatGPT-Image-Nov-2-2025-03_11_14-AM-e1762046066547.png';
        $wpIcon = 'https://styliiiish.com/wp-content/uploads/2025/11/cropped-ChatGPT-Image-Nov-2-2025-03_11_14-AM-e1762046066547.png';
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ $wpIcon }}">
    <link rel="apple-touch-icon" href="{{ $wpIcon }}">
    <title>المتجر | Styliiiish</title>
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

        .pager { margin: 20px 0 34px; display: flex; gap: 8px; justify-content: center; flex-wrap: wrap; }
        .page-link { padding: 8px 12px; border-radius: 10px; border: 1px solid var(--line); background: #fff; font-weight: 700; cursor: pointer; }
        .page-link[disabled] { opacity: .5; cursor: not-allowed; }

        @media (max-width: 980px) {
            .header-inner { grid-template-columns: 1fr; padding: 10px 0; }
            .brand, .nav { justify-content: center; text-align: center; }
            .grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
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
                <a href="https://styliiiish.com/product-category/used-dress/" target="_blank" rel="noopener">الماركت بليس</a>
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
                <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>الأحدث</option>
                <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>السعر: من الأقل للأعلى</option>
                <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>السعر: من الأعلى للأقل</option>
            </select>
        </section>

        <p class="results-meta" id="resultsMeta"></p>

        <section class="grid" id="productsGrid"></section>

        <div class="empty" id="emptyState" style="display:none;">لا توجد منتجات مطابقة الآن. جرّبي البحث بكلمات مختلفة.</div>

        <div class="pager" id="pager"></div>
    </main>

    <script>
        (() => {
            const grid = document.getElementById('productsGrid');
            const pager = document.getElementById('pager');
            const resultsMeta = document.getElementById('resultsMeta');
            const emptyState = document.getElementById('emptyState');
            const qInput = document.getElementById('qInput');
            const sortSelect = document.getElementById('sortSelect');
            const searchForm = document.getElementById('searchForm');

            const params = new URLSearchParams(window.location.search);
            const state = {
                q: params.get('q') ?? qInput.value ?? '',
                sort: params.get('sort') ?? sortSelect.value ?? 'newest',
                page: Number(params.get('page') || 1),
            };

            qInput.value = state.q;
            sortSelect.value = state.sort;

            const fmt = new Intl.NumberFormat('en-US');

            const buildQuery = () => {
                const query = new URLSearchParams();
                if (state.q.trim() !== '') query.set('q', state.q.trim());
                if (state.sort !== 'newest') query.set('sort', state.sort);
                if (state.page > 1) query.set('page', String(state.page));
                return query;
            };

            const updateUrl = () => {
                const query = buildQuery().toString();
                const next = query ? `/shop?${query}` : '/shop';
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
                                <a class="btn-buy" href="/product/${product.slug}/">اطلبي الآن</a>
                                <a class="btn-view" href="/product/${product.slug}/">معاينة</a>
                            </div>
                        </div>
                    </article>
                `;
            };

            const renderPager = (pagination) => {
                if (!pagination || pagination.last_page <= 1) {
                    pager.innerHTML = '';
                    return;
                }

                const prevDisabled = !pagination.prev_page;
                const nextDisabled = !pagination.next_page;

                pager.innerHTML = `
                    <button class="page-link" data-page="${pagination.prev_page || ''}" ${prevDisabled ? 'disabled' : ''}>السابق</button>
                    <span class="page-link">صفحة ${pagination.current_page} من ${pagination.last_page}</span>
                    <button class="page-link" data-page="${pagination.next_page || ''}" ${nextDisabled ? 'disabled' : ''}>التالي</button>
                `;
            };

            const renderMeta = (pagination) => {
                if (!pagination || !pagination.total) {
                    resultsMeta.textContent = 'لا توجد نتائج حاليًا.';
                    return;
                }
                resultsMeta.textContent = `عرض ${pagination.from} - ${pagination.to} من ${pagination.total} منتج`;
            };

            const fetchProducts = async (pushHistory = true) => {
                skeleton();
                emptyState.style.display = 'none';

                const query = buildQuery();
                const response = await fetch(`/shop?${query.toString()}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    grid.innerHTML = '';
                    resultsMeta.textContent = 'حدث خطأ أثناء تحميل المنتجات.';
                    return;
                }

                const data = await response.json();
                const list = data.products || [];

                if (list.length === 0) {
                    grid.innerHTML = '';
                    emptyState.style.display = 'block';
                    renderMeta(data.pagination);
                    renderPager(data.pagination);
                    if (pushHistory) updateUrl();
                    return;
                }

                grid.innerHTML = list.map(productCard).join('');
                renderMeta(data.pagination);
                renderPager(data.pagination);

                if (pushHistory) updateUrl();
            };

            searchForm.addEventListener('submit', (event) => {
                event.preventDefault();
                state.q = qInput.value.trim();
                state.page = 1;
                fetchProducts(true);
            });

            sortSelect.addEventListener('change', () => {
                state.sort = sortSelect.value;
                state.page = 1;
                fetchProducts(true);
            });

            pager.addEventListener('click', (event) => {
                const button = event.target.closest('[data-page]');
                if (!button || button.disabled) return;
                const nextPage = Number(button.dataset.page || 1);
                if (!nextPage) return;
                state.page = nextPage;
                fetchProducts(true);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });

            window.addEventListener('popstate', () => {
                const qs = new URLSearchParams(window.location.search);
                state.q = qs.get('q') ?? '';
                state.sort = qs.get('sort') ?? 'newest';
                state.page = Number(qs.get('page') || 1);
                qInput.value = state.q;
                sortSelect.value = state.sort;
                fetchProducts(false);
            });

            fetchProducts(false);
        })();
    </script>
</body>
</html>
