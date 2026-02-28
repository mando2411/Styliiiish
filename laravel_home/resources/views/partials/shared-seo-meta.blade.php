@php
    $resolveTranslation = function (string $key): string {
        if (!isset($t) || !is_callable($t)) {
            return '';
        }

        $value = trim((string) $t($key));
        return $value === $key ? '' : $value;
    };

    $detectedLocale = strtolower((string) ($currentLocale ?? ($locale ?? app()->getLocale() ?? 'ar')));
    $isEnglishLocale = $detectedLocale === 'en';

    $seoTitleValue = trim((string) (
        $metaTitle
        ?? $seoTitle
        ?? $resolveTranslation('meta_title')
        ?? $resolveTranslation('page_title')
        ?? $resolveTranslation('title')
        ?? 'Styliiiish'
    ));

    if ($seoTitleValue === '') {
        $seoTitleValue = 'Styliiiish';
    }

    $seoDescriptionValue = trim((string) (
        $metaDescription
        ?? $seoDescription
        ?? $metaDesc
        ?? $resolveTranslation('meta_desc')
        ?? 'Styliiiish - Evening, bridal, and engagement dresses in Egypt with fast delivery and trusted service.'
    ));

    if ($seoDescriptionValue === '') {
        $seoDescriptionValue = 'Styliiiish - Evening, bridal, and engagement dresses in Egypt with fast delivery and trusted service.';
    }

    $seoKeywordsValue = trim((string) (
        $metaKeywords
        ?? $resolveTranslation('meta_keywords')
        ?? 'styliiiish, فساتين سهرة, فساتين زفاف, فساتين خطوبة, evening dresses egypt, bridal dresses egypt, engagement dresses'
    ));

    $seoBaseUrl = rtrim((string) ($wpBaseUrl ?? env('WP_PUBLIC_URL', request()->getSchemeAndHttpHost())), '/');
    $seoCanonicalPath = (string) ($canonicalPath ?? request()->getPathInfo());
    if ($seoCanonicalPath === '' || $seoCanonicalPath[0] !== '/') {
        $seoCanonicalPath = '/' . ltrim($seoCanonicalPath, '/');
    }

    $seoUrlValue = (string) ($metaUrl ?? $seoUrl ?? ($seoBaseUrl . $seoCanonicalPath));
    $seoImageValue = (string) ($metaImage ?? $seoImage ?? $wpIcon ?? $wpLogo ?? ($seoBaseUrl . '/favicon.ico'));

    $seoLocaleCode = $isEnglishLocale ? 'en_US' : 'ar_EG';
    $seoAltLocaleCode = $isEnglishLocale ? 'ar_EG' : 'en_US';
@endphp
<meta name="keywords" content="{{ $seoKeywordsValue }}">
<meta name="author" content="Styliiiish">
<meta name="publisher" content="Styliiiish">
<meta name="application-name" content="Styliiiish">
<meta name="theme-color" content="#d51522">
<meta name="googlebot" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<meta name="bingbot" content="index, follow">
<meta property="og:locale" content="{{ $seoLocaleCode }}">
<meta property="og:locale:alternate" content="{{ $seoAltLocaleCode }}">
<meta property="og:image" content="{{ $seoImageValue }}">
<meta property="og:url" content="{{ $seoUrlValue }}">
<meta property="og:site_name" content="Styliiiish">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitleValue }}">
<meta name="twitter:description" content="{{ $seoDescriptionValue }}">
<meta name="twitter:image" content="{{ $seoImageValue }}">
<link rel="preconnect" href="https://styliiiish.com" crossorigin>
<link rel="dns-prefetch" href="//styliiiish.com">
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => $seoTitleValue,
    'description' => $seoDescriptionValue,
    'url' => $seoUrlValue,
    'inLanguage' => $isEnglishLocale ? 'en' : 'ar',
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'Styliiiish',
        'url' => $seoBaseUrl,
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
