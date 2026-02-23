@php
    $localePrefix = $localePrefix ?? '/ar';
    $isEnglish = isset($isEnglish) ? (bool) $isEnglish : false;
    $dir = $isEnglish ? 'ltr' : 'rtl';

    $t = function (string $key) use ($isEnglish): string {
        $dict = $isEnglish
            ? [
                'title' => 'Wishlist',
                'subtitle' => 'Your saved products in one place',
                'count_loading' => 'Loading count…',
                'continue_shopping' => 'Continue shopping',
                'loading' => 'Loading wishlist…',
                'empty_title' => 'Your wishlist is empty',
                'empty_desc' => 'Add products you love from the product page to see them here.',
                'go_shop' => 'Browse products',
                'view_product' => 'Go to product',
                'remove' => 'Remove',
                'removing' => 'Removing…',
                'remove_failed' => 'Unable to remove this item right now.',
                'load_failed' => 'Unable to load wishlist items right now.',
                'items' => 'items',
            ]
            : [
                'title' => 'المفضلة',
                'subtitle' => 'كل المنتجات المحفوظة في مكان واحد',
                'count_loading' => 'جاري تحميل العدد…',
                'continue_shopping' => 'متابعة التسوق',
                'loading' => 'جاري تحميل المفضلة…',
                'empty_title' => 'المفضلة فارغة حالياً',
                'empty_desc' => 'أضيفي المنتجات التي تعجبك من صفحة المنتج لتظهر هنا.',
                'go_shop' => 'تصفح المنتجات',
                'view_product' => 'اذهب إلى المنتج',
                'remove' => 'حذف',
                'removing' => 'جاري الحذف…',
                'remove_failed' => 'تعذر حذف هذا المنتج حالياً.',
                'load_failed' => 'تعذر تحميل عناصر المفضلة حالياً.',
                'items' => 'منتج',
            ];

        return $dict[$key] ?? $key;
    };
@endphp
<!doctype html>
<html lang="{{ $isEnglish ? 'en' : 'ar' }}" dir="{{ $dir }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $t('title') }} | Styliiiish</title>
    <style>
        :root {
            --primary: #7c3aed;
            --text: #1f2937;
            --muted: #6b7280;
            --bg: #f6f7fb;
            --card: #ffffff;
            --line: #e5e7eb;
            --danger: #dc2626;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);
            font-family: "Cairo", "Segoe UI", Tahoma, Arial, sans-serif;
        }
        .container {
            max-width: 1100px;
            margin: 32px auto;
            padding: 0 16px;
        }
        .panel {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 16px;
            overflow: hidden;
        }
        .head {
            padding: 20px;
            border-bottom: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }
        .head h1 {
            margin: 0;
            font-size: 28px;
            line-height: 1.2;
        }
        .head p {
            margin: 6px 0 0;
            color: var(--muted);
        }
        .head-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .badge {
            border-radius: 999px;
            background: rgba(124, 58, 237, 0.1);
            color: var(--primary);
            font-weight: 700;
            padding: 8px 12px;
            font-size: 13px;
        }
        .btn {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px 14px;
            text-decoration: none;
            color: var(--text);
            font-weight: 700;
            background: #fff;
            transition: .2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
        }
        .btn:hover { border-color: var(--primary); color: var(--primary); }
        .content { padding: 20px; }
        .grid {
            display: grid;
            gap: 14px;
        }
        .item {
            display: grid;
            grid-template-columns: 96px 1fr auto;
            gap: 14px;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px;
            align-items: center;
            background: #fff;
        }
        .thumb {
            width: 96px;
            height: 96px;
            border-radius: 10px;
            object-fit: cover;
            border: 1px solid var(--line);
        }
        .name {
            margin: 0;
            font-size: 17px;
            line-height: 1.35;
        }
        .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }
        .btn-danger {
            border-color: rgba(220, 38, 38, 0.3);
            color: var(--danger);
            background: #fff;
        }
        .btn-danger:hover { background: #fff5f5; }
        .state {
            border: 1px dashed var(--line);
            border-radius: 14px;
            padding: 34px 16px;
            text-align: center;
            color: var(--muted);
        }
        .state h3 {
            margin: 0 0 8px;
            color: var(--text);
            font-size: 22px;
        }
        @media (max-width: 720px) {
            .item { grid-template-columns: 78px 1fr; }
            .thumb { width: 78px; height: 78px; }
            .actions { grid-column: 1 / -1; justify-content: stretch; }
            .actions .btn { flex: 1 1 100%; }
        }
    </style>
</head>
<body>
    <main class="container">
        <section class="panel">
            <header class="head">
                <div>
                    <h1>{{ $t('title') }}</h1>
                    <p>{{ $t('subtitle') }}</p>
                </div>
                <div class="head-actions">
                    <span class="badge" id="wishlistCount">{{ $t('count_loading') }}</span>
                    <a class="btn" href="{{ $localePrefix }}/shop">{{ $t('continue_shopping') }}</a>
                </div>
            </header>
            <div class="content">
                <div id="wishlistContainer" class="grid">
                    <div class="state">{{ $t('loading') }}</div>
                </div>
            </div>
        </section>
    </main>

    <script>
        (() => {
            const localePrefix = @json($localePrefix);
            const texts = {
                loading: @json($t('loading')),
                emptyTitle: @json($t('empty_title')),
                emptyDesc: @json($t('empty_desc')),
                goShop: @json($t('go_shop')),
                viewProduct: @json($t('view_product')),
                remove: @json($t('remove')),
                removing: @json($t('removing')),
                removeFailed: @json($t('remove_failed')),
                loadFailed: @json($t('load_failed')),
                items: @json($t('items')),
            };

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const container = document.getElementById('wishlistContainer');
            const countBadge = document.getElementById('wishlistCount');

            const getItemsUrl = () => `${localePrefix}/item/wishlist/items`;
            const getRemoveUrl = (id) => `${localePrefix}/item/wishlist/${encodeURIComponent(id)}`;

            const setCount = (count) => {
                const safeCount = Math.max(0, Number(count) || 0);
                if (!countBadge) return;
                countBadge.textContent = `${safeCount} ${texts.items}`;
            };

            const escapeHtml = (value) => String(value || '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');

            const renderEmpty = () => {
                container.innerHTML = `
                    <div class="state">
                        <h3>${escapeHtml(texts.emptyTitle)}</h3>
                        <p>${escapeHtml(texts.emptyDesc)}</p>
                        <a class="btn" href="${localePrefix}/shop" style="margin-top:12px;">${escapeHtml(texts.goShop)}</a>
                    </div>
                `;
            };

            const renderItems = (items) => {
                const safeItems = Array.isArray(items) ? items : [];
                if (safeItems.length === 0) {
                    renderEmpty();
                    return;
                }

                container.innerHTML = safeItems.map((item) => {
                    const id = Number(item.id) || 0;
                    const name = escapeHtml(item.name || '');
                    const image = escapeHtml(item.image || '');
                    const url = escapeHtml(item.url || '#');

                    return `
                        <article class="item" data-id="${id}">
                            <img class="thumb" src="${image}" alt="${name}" loading="lazy" />
                            <div>
                                <h3 class="name">${name}</h3>
                            </div>
                            <div class="actions">
                                <a class="btn" href="${url}">${escapeHtml(texts.viewProduct)}</a>
                                <button class="btn btn-danger js-remove" type="button" data-id="${id}">${escapeHtml(texts.remove)}</button>
                            </div>
                        </article>
                    `;
                }).join('');

                bindRemoveActions();
            };

            const loadItems = async () => {
                container.innerHTML = `<div class="state">${escapeHtml(texts.loading)}</div>`;

                try {
                    const response = await fetch(`${getItemsUrl()}?_=${Date.now()}`, {
                        method: 'GET',
                        credentials: 'include',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    const payload = await response.json();
                    if (!response.ok || !payload || payload.success !== true) {
                        throw new Error('wishlist_load_failed');
                    }

                    setCount(payload.count);
                    renderItems(payload.items || []);
                } catch (error) {
                    container.innerHTML = `<div class="state">${escapeHtml(texts.loadFailed)}</div>`;
                }
            };

            const removeItem = async (productId, button) => {
                if (!productId || !button) return;

                const oldText = button.textContent;
                button.disabled = true;
                button.textContent = texts.removing;

                try {
                    const response = await fetch(getRemoveUrl(productId), {
                        method: 'DELETE',
                        credentials: 'include',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                    });

                    const payload = await response.json();
                    if (!response.ok || !payload || payload.success !== true) {
                        throw new Error('wishlist_remove_failed');
                    }

                    setCount(payload.count);
                    await loadItems();
                } catch (error) {
                    button.disabled = false;
                    button.textContent = oldText || texts.remove;
                    alert(texts.removeFailed);
                }
            };

            const bindRemoveActions = () => {
                container.querySelectorAll('.js-remove').forEach((button) => {
                    button.addEventListener('click', () => {
                        const id = Number(button.getAttribute('data-id') || 0);
                        removeItem(id, button);
                    });
                });
            };

            loadItems();
        })();
    </script>
</body>
</html>
