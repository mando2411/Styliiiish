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
    $seoLanguageCode = $isEnglishLocale ? 'en' : 'ar';
    $seoOrganizationName = 'Styliiiish';
    $seoOrganizationDescription = $isEnglishLocale
        ? 'Styliiiish is an online fashion platform for evening, bridal, and engagement dresses with trusted support and Egypt-wide delivery.'
        : 'ستايلش منصة أزياء إلكترونية لفساتين السهرة والزفاف والخطوبة مع دعم موثوق وتوصيل داخل مصر.';
    $seoSameAs = [
        'https://www.facebook.com/Styliiish.Egypt/',
        'https://www.instagram.com/styliiiish.egypt/',
        'https://g.page/styliish',
    ];
@endphp
<meta name="keywords" content="{{ $seoKeywordsValue }}">
<meta name="author" content="Styliiiish">
<meta name="publisher" content="Styliiiish">
<meta name="application-name" content="Styliiiish">
<meta name="theme-color" content="#d51522">
<link rel="preconnect" href="https://styliiiish.com" crossorigin>
<link rel="dns-prefetch" href="//styliiiish.com">
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => $seoOrganizationName,
    'url' => $seoBaseUrl,
    'logo' => $seoImageValue,
    'description' => $seoOrganizationDescription,
    'sameAs' => $seoSameAs,
    'areaServed' => [
        ['@type' => 'Country', 'name' => 'Egypt'],
        ['@type' => 'Country', 'name' => 'Switzerland'],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => $seoOrganizationName,
    'url' => $seoBaseUrl,
    'inLanguage' => [$seoLanguageCode, $isEnglishLocale ? 'ar' : 'en'],
    'potentialAction' => [
        '@type' => 'SearchAction',
        'target' => $seoBaseUrl . '/' . $seoLanguageCode . '/shop?q={search_term_string}',
        'query-input' => 'required name=search_term_string',
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => $seoOrganizationName,
        'url' => $seoBaseUrl,
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => $seoTitleValue,
    'description' => $seoDescriptionValue,
    'url' => $seoUrlValue,
    'inLanguage' => $seoLanguageCode,
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => $seoOrganizationName,
        'url' => $seoBaseUrl,
    ],
    'about' => [
        '@type' => 'Organization',
        'name' => $seoOrganizationName,
        'url' => $seoBaseUrl,
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => $seoOrganizationName,
        'url' => $seoBaseUrl,
        'logo' => [
            '@type' => 'ImageObject',
            'url' => $seoImageValue,
        ],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
