@php
    $navClass = $navClass ?? 'main-nav';
    $normalizePath = function (string $path): string {
        $trimmed = trim($path);
        if ($trimmed === '' || $trimmed === '/') {
            return '/';
        }

        return '/' . trim($trimmed, '/');
    };

    $currentPath = $normalizePath(request()->getPathInfo());
    $homePath = $normalizePath($localePrefix ?? '/');
    $shopPath = $normalizePath(($localePrefix ?? '/') . '/shop');
    $blogPath = $normalizePath(($localePrefix ?? '/') . '/blog');
    $aboutPath = $normalizePath(($localePrefix ?? '/') . '/about-us');
    $contactPath = $normalizePath(($localePrefix ?? '/') . '/contact-us');

    $isActive = fn (string $targetPath) => $currentPath === $targetPath;
@endphp
<nav class="{{ $navClass }}" aria-label="Main Navigation">
    <a class="{{ $isActive($homePath) ? 'active' : '' }}" href="{{ $localePrefix }}">{{ $t('nav_home') }}</a>
    <a class="{{ $isActive($shopPath) ? 'active' : '' }}" href="{{ $localePrefix }}/shop">{{ $t('nav_shop') }}</a>
    <a class="{{ $isActive($blogPath) ? 'active' : '' }}" href="{{ $localePrefix }}/blog">{{ $t('nav_blog') }}</a>
    <a class="{{ $isActive($aboutPath) ? 'active' : '' }}" href="{{ $localePrefix }}/about-us">{{ $t('about_us') }}</a>
    <a class="{{ $isActive($contactPath) ? 'active' : '' }}" href="{{ $localePrefix }}/contact-us">{{ $t('nav_contact') }}</a>
</nav>
