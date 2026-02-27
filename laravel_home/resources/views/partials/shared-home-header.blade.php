@php
    $currentLocale = strtolower((string) ($currentLocale ?? 'ar'));
    if (!in_array($currentLocale, ['ar', 'en'], true)) {
        $currentLocale = 'ar';
    }

    $isEnglish = ($isEnglish ?? ($currentLocale === 'en')) === true;
    $rawLocalePrefix = (string) ($localePrefix ?? ($isEnglish ? '/en' : '/ar'));
    $rawLocalePrefix = '/' . trim($rawLocalePrefix, '/');
    $localePrefix = preg_match('#^/(ar|en)(?:/.*)?$#i', $rawLocalePrefix, $localeMatch)
        ? ('/' . strtolower((string) $localeMatch[1]))
        : ($isEnglish ? '/en' : '/ar');
    $currentLocale = $localePrefix === '/en' ? 'en' : 'ar';
    $isEnglish = $currentLocale === 'en';
    $wpBaseUrl = rtrim((string) ($wpBaseUrl ?? env('WP_PUBLIC_URL', 'https://styliiiish.com')), '/');
    $wpLogo = $wpLogo ?? 'https://styliiiish.com/wp-content/uploads/2025/11/ChatGPT-Image-Nov-2-2025-03_11_14-AM-e1762046066547.png';

    $wpMyAccountUrl = $wpMyAccountUrl ?? ($wpBaseUrl . '/my-account/');
    $headerSearchUrl = $localePrefix . '/shop';
    $wpLocalizedAccountUrl = $wpLocalizedAccountUrl ?? ($isEnglish
        ? ($wpBaseUrl . '/my-account/')
        : ($wpBaseUrl . '/ar/%d8%ad%d8%b3%d8%a7%d8%a8%d9%8a/'));
    $wpLocalizedMyDressesUrl = $wpLocalizedMyDressesUrl ?? ($isEnglish
        ? ($wpBaseUrl . '/my-dresses/')
        : ($wpBaseUrl . '/ar/%d9%81%d8%b3%d8%a7%d8%aa%d9%8a%d9%86%d9%8a/'));
    $normalizePath = function (string $path): string {
        $trimmed = trim($path);
        if ($trimmed === '' || $trimmed === '/') {
            return '/';
        }

        return '/' . trim($trimmed, '/');
    };
    $currentPath = $normalizePath(request()->getPathInfo());
    $homePath = $normalizePath($localePrefix);
    $isHomeRoute = $currentPath === $homePath;

    $pathWithoutLocale = preg_replace('#^/(ar|en)(?=/|$)#i', '', $currentPath) ?? $currentPath;
    $pathWithoutLocale = $normalizePath($pathWithoutLocale);
    $localePathTail = $pathWithoutLocale === '/' ? '' : $pathWithoutLocale;
    $arSwitchUrl = '/ar' . $localePathTail;
    $enSwitchUrl = '/en' . $localePathTail;

    $decodedCurrentPath = $normalizePath(rawurldecode($currentPath));

    $accountPaths = [
        '/my-account',
        '/en/my-account',
        '/ar/حسابي',
    ];

    if (in_array($currentPath, $accountPaths, true) || in_array($decodedCurrentPath, $accountPaths, true)) {
        $arSwitchUrl = '/ar/حسابي/';
        $enSwitchUrl = '/my-account/';
    }

    $translate = (isset($t) && is_callable($t)) ? $t : fn (string $key) => $key;
    $ht = function (string $key, string $arFallback, string $enFallback) use ($translate, $isEnglish): string {
        $value = (string) $translate($key);
        if ($value === '' || $value === $key) {
            return $isEnglish ? $enFallback : $arFallback;
        }

        return $value;
    };
@endphp

<div class="topbar">
    <div class="container topbar-inner">
        <div class="topbar-right">
            <div class="topbar-desktop-contact">
                <strong>{{ $ht('contact_anytime', 'اتصلي بنا في أي وقت:', 'Call us anytime:') }}</strong>
                <a class="topbar-phone" href="tel:+201050874255" dir="ltr" lang="en">+20 010 5087 4255</a>
            </div>
            <div class="topbar-mobile-icons" aria-label="{{ $ht('contact_anytime', 'اتصلي بنا في أي وقت:', 'Call us anytime:') }}">
                <a class="topbar-mobile-icon" href="tel:+201050874255" aria-label="{{ $ht('direct_call', 'اتصال مباشر', 'Direct Call') }}" title="{{ $ht('direct_call', 'اتصال مباشر', 'Direct Call') }}">📞</a>
                <a class="topbar-mobile-icon" href="https://wa.me/201050874255" target="_blank" rel="noopener" aria-label="WhatsApp" title="WhatsApp">🟢</a>
                <a class="topbar-mobile-icon" href="https://www.facebook.com/Styliiish.Egypt/" target="_blank" rel="noopener" aria-label="Facebook" title="Facebook">f</a>
                <a class="topbar-mobile-icon" href="https://www.instagram.com/styliiiish.egypt/" target="_blank" rel="noopener" aria-label="Instagram" title="Instagram">◎</a>
                <a class="topbar-mobile-icon" href="https://g.page/styliish" target="_blank" rel="noopener" aria-label="Google" title="Google">G</a>
            </div>
        </div>
        <div class="topbar-left">
            <div class="lang-switch {{ $isEnglish ? 'is-en' : 'is-ar' }}" aria-label="Language Switcher">
                <span class="lang-indicator" aria-hidden="true"></span>
                <a class="{{ $currentLocale === 'ar' ? 'active' : '' }}" href="{{ $arSwitchUrl }}">AR</a>
                <a class="{{ $currentLocale === 'en' ? 'active' : '' }}" href="{{ $enSwitchUrl }}">EN</a>
            </div>
            <span class="topbar-note">{{ $ht('daily_deals', '⚡ خصومات يومية', '⚡ Daily Deals') }}</span>
            <a href="https://www.facebook.com/Styliiish.Egypt/" target="_blank" rel="noopener">{{ $ht('facebook', 'فيسبوك', 'Facebook') }}</a>
            <a href="https://www.instagram.com/styliiiish.egypt/" target="_blank" rel="noopener">{{ $ht('instagram', 'إنستجرام', 'Instagram') }}</a>
            <a href="https://g.page/styliish" target="_blank" rel="noopener">{{ $ht('google', 'جوجل', 'Google') }}</a>
        </div>
    </div>
</div>

<header class="main-header">
    <div class="container main-header-inner">
        <a class="brand" href="{{ $localePrefix }}">
            <img class="brand-logo" src="{{ $wpLogo }}" alt="Styliiiish" onerror="this.onerror=null;this.src='/brand/logo.png';">
            <span class="brand-tag">{{ $ht('brand_tag', 'لأن كل امرأة تستحق أن تتألق', 'Because every woman deserves to shine') }}</span>
        </a>

        @include('partials.shared-header-nav', ['navClass' => 'main-nav', 'navId' => 'headerMainNav'])

        <div class="header-actions">
            <button class="icon-btn nav-toggle action-nav-toggle" id="headerNavToggle" type="button" aria-label="{{ $ht('menu', 'القائمة', 'Menu') }}" title="{{ $ht('menu', 'القائمة', 'Menu') }}" aria-controls="headerMainNav" aria-expanded="false">
                <span class="icon" aria-hidden="true">☰</span>
            </button>
            <form class="search-form" action="{{ $headerSearchUrl }}" method="get">
                <input class="search-input" type="search" name="q" required placeholder="{{ $ht('search_placeholder', 'ابحثي عن فستانك...', 'Search for your dress...') }}" aria-label="{{ $ht('search_placeholder', 'ابحثي عن فستانك...', 'Search for your dress...') }}">
                <button class="search-btn" type="submit">{{ $ht('search_btn', 'بحث', 'Search') }}</button>
            </form>
            <span class="account-trigger-wrap action-account">
                <a class="icon-btn account-trigger" id="accountLoginTrigger" href="{{ $wpLocalizedAccountUrl }}" target="_blank" rel="noopener" aria-label="{{ $ht('account', 'حسابي', 'My Account') }}" title="{{ $ht('account', 'حسابي', 'My Account') }}"><span class="icon" aria-hidden="true">👤</span></a>
            </span>
            <span class="wishlist-trigger-wrap action-wishlist">
                <button class="icon-btn wishlist-trigger" id="wishlistTrigger" type="button" aria-label="{{ $ht('wishlist', 'قائمة الأمنيات', 'Wishlist') }}" title="{{ $ht('wishlist', 'قائمة الأمنيات', 'Wishlist') }}" aria-expanded="false" aria-controls="wishlistDropdown"><span class="icon" aria-hidden="true">❤</span>
                    <span class="wishlist-count" id="wishlistCountBadge">0</span>
                </button>
                <span class="wishlist-plus-one" id="wishlistPlusOne">+1</span>
                <div class="wishlist-dropdown" id="wishlistDropdown" role="dialog" aria-label="{{ $ht('wishlist', 'قائمة الأمنيات', 'Wishlist') }}" aria-hidden="true">
                    <div class="wishlist-dropdown-list" id="wishlistDropdownList">
                        <p class="wishlist-dropdown-empty" id="wishlistDropdownLoading">{{ $ht('wishlist_loading', 'جاري تحميل المفضلة…', 'Loading wishlist…') }}</p>
                    </div>
                    <div class="wishlist-dropdown-footer">
                        <a class="wishlist-dropdown-all" href="{{ $localePrefix }}/wishlist">{{ $ht('view_all_wishlist', 'عرض كل المفضلة', 'View full wishlist') }}</a>
                    </div>
                </div>
            </span>
            <span class="cart-trigger-wrap action-cart">
                <button class="icon-btn cart-trigger" id="miniCartTrigger" type="button" aria-label="{{ $ht('cart', 'السلة', 'Cart') }}" title="{{ $ht('cart', 'السلة', 'Cart') }}"><span class="icon" aria-hidden="true">🛒</span>
                    <span class="cart-count" id="cartCountBadge">0</span>
                </button>
                <span class="cart-plus-one" id="cartPlusOne">+1</span>
            </span>
            <a class="btn btn-primary header-cta action-sell" href="{{ $wpLocalizedMyDressesUrl }}" target="_blank" rel="noopener">{{ $ht('start_selling', 'ابدئي البيع', 'Start Selling') }}</a>
        </div>
    </div>
</header>

<script>
    (() => {
        const navToggle = document.getElementById('headerNavToggle');
        const nav = document.getElementById('headerMainNav');

        if (!navToggle || !nav) return;

        const closeNav = () => {
            nav.classList.remove('is-open');
            navToggle.setAttribute('aria-expanded', 'false');
        };

        navToggle.addEventListener('click', () => {
            const willOpen = !nav.classList.contains('is-open');
            nav.classList.toggle('is-open', willOpen);
            navToggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        });

        nav.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', closeNav);
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > 640) closeNav();
        });
    })();
</script>

<div class="promo">{{ $ht('promo_line', 'لأن كل امرأة تستحق أن تتألق • خصومات تصل إلى 50% • توصيل داخل مصر خلال 2–10 أيام عمل', 'Because every woman deserves to shine • Up to 50% OFF • Delivery across Egypt in 2–10 business days') }}</div>

@include('partials.shared-header-categories-strip')

@unless($isHomeRoute)
    @include('partials.shared-home-header-interactions', ['ht' => $ht])
@endunless
