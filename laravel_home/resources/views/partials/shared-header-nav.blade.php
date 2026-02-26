@php
    $navClass = $navClass ?? 'main-nav';
@endphp
<nav class="{{ $navClass }}" aria-label="Main Navigation">
    <a href="{{ $localePrefix }}">{{ $t('nav_home') }}</a>
    <a href="{{ $localePrefix }}/shop">{{ $t('nav_shop') }}</a>
    <a href="{{ $localePrefix }}/blog">{{ $t('nav_blog') }}</a>
    <a href="{{ $localePrefix }}/about-us">{{ $t('about_us') }}</a>
    <a href="{{ $localePrefix }}/contact-us">{{ $t('nav_contact') }}</a>
</nav>
