@php
    $requestPath = trim((string) request()->path(), '/');
    $segments = $requestPath === '' ? [] : explode('/', $requestPath);
    $detectedLocale = in_array($segments[0] ?? '', ['ar', 'en'], true) ? $segments[0] : null;

    $currentLocale = $currentLocale ?? $detectedLocale ?? 'ar';
    $localePrefix = $localePrefix ?? ('/' . $currentLocale);
    $isEnglish = $currentLocale === 'en';

    $wpBaseUrl = rtrim((string) ($wpBaseUrl ?? env('WP_PUBLIC_URL', 'https://styliiiish.com')), '/');
    $wpLogoUrl = $wpLogo ?? ($wpBaseUrl . '/wp-content/uploads/2023/10/styliiiish-logo.png');

    $pathWithoutLocale = $requestPath;
    if ($detectedLocale !== null) {
        $pathWithoutLocale = ltrim((string) substr($requestPath, strlen($detectedLocale)), '/');
    }

    $isHome = $pathWithoutLocale === '';
    $pathIs = static function (string $slug) use ($pathWithoutLocale): bool {
        return $pathWithoutLocale === $slug || str_starts_with($pathWithoutLocale, $slug . '/');
    };

    $arUrl = '/ar' . ($pathWithoutLocale !== '' ? '/' . $pathWithoutLocale : '');
    $enUrl = '/en' . ($pathWithoutLocale !== '' ? '/' . $pathWithoutLocale : '');

    $labels = $isEnglish
        ? [
            'brand_tag' => 'Because every woman deserves to shine',
            'home' => 'Home',
            'shop' => 'Shop',
            'about' => 'About Us',
            'marketplace' => 'Marketplace',
            'sell' => 'Sell Your Dress',
            'blog' => 'Blog',
            'contact' => 'Contact Us',
            'account' => 'Account',
            'wishlist' => 'Wishlist',
            'cart' => 'Cart',
        ]
        : [
            'brand_tag' => 'لأن كل امرأة تستحق أن تتألق',
            'home' => 'الرئيسية',
            'shop' => 'المتجر',
            'about' => 'من نحن',
            'marketplace' => 'الماركت بليس',
            'sell' => 'بيعي فستانك',
            'blog' => 'المدونة',
            'contact' => 'تواصل معنا',
            'account' => 'حسابي',
            'wishlist' => 'المفضلة',
            'cart' => 'السلة',
        ];
@endphp

<header class="main-header header">
    <div class="container main-header-inner header-inner">
        <a class="brand" href="{{ $localePrefix }}">
            <img class="brand-logo" src="{{ $wpLogoUrl }}" alt="Styliiiish" onerror="this.onerror=null;this.src='/brand/logo.png';">
            <span class="brand-tag brand-sub">{{ $labels['brand_tag'] }}</span>
        </a>

        <nav class="main-nav nav" aria-label="Main Navigation">
            <a class="{{ $isHome ? 'active' : '' }}" href="{{ $localePrefix }}">{{ $labels['home'] }}</a>
            <a class="{{ $pathIs('shop') ? 'active' : '' }}" href="{{ $localePrefix }}/shop">{{ $labels['shop'] }}</a>
            <a class="{{ $pathIs('about-us') ? 'active' : '' }}" href="{{ $localePrefix }}/about-us">{{ $labels['about'] }}</a>
            <a class="{{ $pathIs('marketplace') ? 'active' : '' }}" href="{{ $localePrefix }}/marketplace">{{ $labels['marketplace'] }}</a>
            <a href="{{ $wpBaseUrl }}/my-dresses/" target="_blank" rel="noopener">{{ $labels['sell'] }}</a>
            <a class="{{ $pathIs('blog') ? 'active' : '' }}" href="{{ $localePrefix }}/blog">{{ $labels['blog'] }}</a>
            <a class="{{ $pathIs('contact-us') ? 'active' : '' }}" href="{{ $localePrefix }}/contact-us">{{ $labels['contact'] }}</a>
        </nav>

        <div class="header-tools header-actions">
            <div class="lang-switch {{ $isEnglish ? 'is-en' : 'is-ar' }}" aria-label="Language switch">
                <span class="lang-indicator" aria-hidden="true"></span>
                <a class="{{ $currentLocale === 'ar' ? 'active' : '' }}" href="{{ $arUrl }}">AR</a>
                <a class="{{ $currentLocale === 'en' ? 'active' : '' }}" href="{{ $enUrl }}">EN</a>
            </div>
            <a class="head-btn icon-btn" href="{{ $wpBaseUrl }}/my-account/" target="_blank" rel="noopener" title="{{ $labels['account'] }}" aria-label="{{ $labels['account'] }}">👤</a>
            <a class="head-btn icon-btn {{ $pathIs('wishlist') ? 'active' : '' }}" href="{{ $localePrefix }}/wishlist" title="{{ $labels['wishlist'] }}" aria-label="{{ $labels['wishlist'] }}">❤</a>
            <a class="head-btn icon-btn {{ $pathIs('cart') ? 'active' : '' }}" href="{{ $localePrefix }}/cart" title="{{ $labels['cart'] }}" aria-label="{{ $labels['cart'] }}">🛒</a>
        </div>
    </div>
</header>
