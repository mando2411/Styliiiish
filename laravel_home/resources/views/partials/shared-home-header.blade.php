@php
    $currentLocale = $currentLocale ?? 'ar';
    $isEnglish = ($isEnglish ?? ($currentLocale === 'en')) === true;
    $localePrefix = $localePrefix ?? ($isEnglish ? '/en' : '/ar');
    $wpBaseUrl = rtrim((string) ($wpBaseUrl ?? env('WP_PUBLIC_URL', 'https://styliiiish.com')), '/');
    $wpLogo = $wpLogo ?? 'https://styliiiish.com/wp-content/uploads/2025/11/ChatGPT-Image-Nov-2-2025-03_11_14-AM-e1762046066547.png';

    $wpMyAccountUrl = $wpMyAccountUrl ?? ($wpBaseUrl . '/my-account/');
    $wpLocalizedAccountUrl = $wpLocalizedAccountUrl ?? ($isEnglish
        ? ($wpBaseUrl . '/my-account/')
        : ($wpBaseUrl . '/ar/%d8%ad%d8%b3%d8%a7%d8%a8%d9%8a/'));
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
            <strong>{{ $ht('contact_anytime', 'اتصلي بنا في أي وقت:', 'Call us anytime:') }}</strong>
            <a href="tel:+201050874255">+20 010 5087 4255</a>
        </div>
        <div class="topbar-left">
            <div class="lang-switch {{ $isEnglish ? 'is-en' : 'is-ar' }}" aria-label="Language Switcher">
                <span class="lang-indicator" aria-hidden="true"></span>
                <a class="{{ $currentLocale === 'ar' ? 'active' : '' }}" href="/ar">AR</a>
                <a class="{{ $currentLocale === 'en' ? 'active' : '' }}" href="/en">EN</a>
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

        @include('partials.shared-header-nav', ['navClass' => 'main-nav'])

        <div class="header-actions">
            <form class="search-form" action="https://styliiiish.com/" method="get" target="_blank">
                <input class="search-input" type="search" name="s" placeholder="{{ $ht('search_placeholder', 'ابحثي عن فستانك...', 'Search for your dress...') }}" aria-label="{{ $ht('search_placeholder', 'ابحثي عن فستانك...', 'Search for your dress...') }}">
                <button class="search-btn" type="submit">{{ $ht('search_btn', 'بحث', 'Search') }}</button>
            </form>
            <span class="account-trigger-wrap action-account">
                <button class="icon-btn account-trigger" id="accountLoginTrigger" type="button" aria-label="{{ $ht('account', 'حسابي', 'My Account') }}" title="{{ $ht('account', 'حسابي', 'My Account') }}" aria-expanded="false"><span class="icon" aria-hidden="true">👤</span></button>
                <div class="account-mini-menu" id="accountMenu" aria-hidden="true">
                    <div class="account-mini-head">
                        <strong class="account-mini-name" id="accountMenuName">{{ $ht('account_loading', 'جاري تحميل بيانات الحساب…', 'Loading account details…') }}</strong>
                        <span class="account-mini-meta" id="accountMenuMeta">{{ $ht('account_logged_in', 'مستخدم مسجل دخول', 'Logged-in user') }}</span>
                    </div>
                    <div class="account-mini-actions">
                        <a class="account-manage-link" id="accountMenuManage" href="{{ $wpLocalizedAccountUrl }}">{{ $ht('manage_account', 'إدارة حسابك', 'Manage your account') }}</a>
                        <a class="account-logout-link" id="accountMenuLogout" href="{{ $wpMyAccountUrl }}">{{ $ht('logout', 'تسجيل خروج', 'Log out') }}</a>
                    </div>
                </div>
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
            <a class="btn btn-primary header-cta action-sell" href="https://styliiiish.com/my-dresses/" target="_blank" rel="noopener">{{ $ht('start_selling', 'ابدئي البيع', 'Start Selling') }}</a>
        </div>
    </div>
</header>

<div class="promo">{{ $ht('promo_line', 'لأن كل امرأة تستحق أن تتألق • خصومات تصل إلى 50% • توصيل داخل مصر خلال 2–10 أيام عمل', 'Because every woman deserves to shine • Up to 50% OFF • Delivery across Egypt in 2–10 business days') }}</div>

@unless($isHomeRoute)
    @include('partials.shared-home-header-interactions', ['ht' => $ht])
@endunless
