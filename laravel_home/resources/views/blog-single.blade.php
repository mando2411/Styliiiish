<!DOCTYPE html>
@php
    $currentLocale = $currentLocale ?? 'ar';
    $localePrefix = $localePrefix ?? '/ar';
    $isEnglish = $currentLocale === 'en';
    $wpBaseUrl = rtrim((string) ($wpBaseUrl ?? env('WP_PUBLIC_URL', request()->getSchemeAndHttpHost())), '/');

    $translations = [
        'ar' => [
            'page_title_suffix' => 'المدونة | ستايلش',
            'meta_desc_fallback' => 'اقرئي أحدث مقالات ستايلش عن الموضة والفساتين ونصائح اختيار الإطلالة المناسبة.',
            'published_on' => 'نُشر بتاريخ',
            'back_to_blog' => 'العودة إلى المدونة',
            'related_articles' => 'مقالات ذات صلة',
            'read_more' => 'قراءة المزيد',
            'no_related' => 'لا توجد مقالات إضافية حالياً.',
        ],
        'en' => [
            'page_title_suffix' => 'Blog | Styliiiish',
            'meta_desc_fallback' => 'Read the latest Styliiiish blog posts about dresses, fashion trends, and practical styling tips.',
            'published_on' => 'Published on',
            'back_to_blog' => 'Back to Blog',
            'related_articles' => 'Related Articles',
            'read_more' => 'Read More',
            'no_related' => 'No additional articles available right now.',
        ],
    ];

    $normalizeBrandText = fn (string $value) => $currentLocale === 'en'
        ? (preg_replace('/ستايلش/iu', 'Styliiiish', $value) ?? $value)
        : (preg_replace('/styliiiish/iu', 'ستايلش', $value) ?? $value);
    $t = fn (string $key) => $normalizeBrandText((string) ($translations[$currentLocale][$key] ?? $translations['ar'][$key] ?? $key));

    $title = trim((string) ($post->post_title ?? ''));
    $excerptSource = trim((string) ($post->post_excerpt ?: strip_tags((string) ($post->post_content ?? ''))));
    $metaDesc = $excerptSource !== ''
        ? (mb_strlen($excerptSource) > 170 ? mb_substr($excerptSource, 0, 170) . '…' : $excerptSource)
        : $t('meta_desc_fallback');

    $canonicalPath = $localePrefix . '/blog/' . rawurlencode(rawurldecode((string) ($post->post_name ?? '')));
    $articleImage = $post->image ?: ($wpBaseUrl . '/wp-content/uploads/woocommerce-placeholder.png');

    $contentHtml = (string) ($post->post_content ?? '');
    if (trim($contentHtml) === '') {
        $contentHtml = '<p>' . e($metaDesc) . '</p>';
    }
@endphp
<html lang="{{ $isEnglish ? 'en' : 'ar' }}" dir="{{ $isEnglish ? 'ltr' : 'rtl' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $metaDesc }}">
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $title !== '' ? $title : $t('page_title_suffix') }}">
    <meta property="og:description" content="{{ $metaDesc }}">
    <meta property="og:url" content="{{ $wpBaseUrl }}{{ $canonicalPath }}">
    <meta property="og:image" content="{{ $articleImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title !== '' ? $title : $t('page_title_suffix') }}">
    <meta name="twitter:description" content="{{ $metaDesc }}">
    <meta name="twitter:image" content="{{ $articleImage }}">
    <link rel="canonical" href="{{ $wpBaseUrl }}{{ $canonicalPath }}">
    <link rel="alternate" hreflang="ar" href="{{ $wpBaseUrl }}/ar/blog/{{ rawurlencode(rawurldecode((string) ($post->post_name ?? ''))) }}">
    <link rel="alternate" hreflang="en" href="{{ $wpBaseUrl }}/en/blog/{{ rawurlencode(rawurldecode((string) ($post->post_name ?? ''))) }}">
    <title>{{ $title !== '' ? $title . ' | Styliiiish' : $t('page_title_suffix') }}</title>
    <style>
        :root { --wf-main-rgb: 213, 21, 34; --wf-main-color: rgb(var(--wf-main-rgb)); --wf-secondary-color: #17273B; --bg:#f6f7fb; --card:#fff; --line:rgba(189,189,189,.35); --text:#17273B; --muted:#5a6678; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: "Segoe UI", Tahoma, Arial, sans-serif; background: var(--bg); color: var(--text); }
        a { color: inherit; text-decoration: none; }
        .container { width: min(1080px, 92%); margin: 0 auto; }
        .article-wrap { padding: 24px 0 18px; }
        .article-card { background: var(--card); border: 1px solid var(--line); border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(23,39,59,.06); }
        .article-cover { width: 100%; max-height: 420px; object-fit: cover; display: block; background: #edf1f7; }
        .article-body { padding: 20px; }
        .article-title { margin: 0 0 10px; font-size: clamp(24px, 3.2vw, 38px); line-height: 1.25; color: var(--wf-secondary-color); }
        .article-meta { display: inline-flex; align-items: center; gap: 8px; margin-bottom: 14px; font-size: 13px; color: var(--muted); border: 1px dashed rgba(23,39,59,.22); border-radius: 999px; padding: 6px 12px; }
        .article-content { color: #243247; font-size: 16px; line-height: 1.9; }
        .article-content h2, .article-content h3, .article-content h4 { color: var(--wf-secondary-color); margin-top: 22px; margin-bottom: 10px; }
        .article-content p { margin: 0 0 14px; }
        .article-content ul, .article-content ol { margin: 0 0 14px; padding-inline-start: 22px; }
        .article-content img { max-width: 100%; border-radius: 12px; height: auto; }
        .article-actions { margin-top: 18px; display: flex; gap: 10px; flex-wrap: wrap; }
        .btn { display: inline-flex; align-items: center; justify-content: center; min-height: 40px; padding: 0 14px; border-radius: 10px; font-size: 14px; font-weight: 700; border: 1px solid var(--line); }
        .btn-primary { background: var(--wf-main-color); color: #fff; border-color: transparent; }
        .btn-light { background: #fff; color: var(--wf-secondary-color); }
        .related { margin-top: 18px; background: #fff; border: 1px solid var(--line); border-radius: 14px; padding: 16px; }
        .related h3 { margin: 0 0 12px; font-size: 20px; }
        .related-grid { display: grid; grid-template-columns: repeat(3,minmax(0,1fr)); gap: 10px; }
        .related-card { border: 1px solid var(--line); border-radius: 12px; overflow: hidden; background: #fff; }
        .related-card img { width: 100%; aspect-ratio: 16/10; object-fit: cover; background: #edf1f7; }
        .related-card .inner { padding: 10px; display: grid; gap: 6px; }
        .related-card h4 { margin: 0; font-size: 14px; line-height: 1.45; color: var(--wf-secondary-color); }
        .related-card .meta { font-size: 12px; color: var(--muted); }
        @media (max-width: 900px) { .related-grid { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 620px) { .article-body { padding: 14px; } .related-grid { grid-template-columns: 1fr; } }
    </style>
    @include('partials.shared-home-header-styles')
</head>
<body>
@include('partials.shared-home-header')

<main class="container article-wrap">
    <article class="article-card">
        <img class="article-cover" src="{{ $articleImage }}" alt="{{ $title }}" loading="eager" onerror="this.onerror=null;this.src='{{ $wpBaseUrl }}/wp-content/uploads/woocommerce-placeholder.png';">
        <div class="article-body">
            <h1 class="article-title">{{ $title }}</h1>
            <span class="article-meta">{{ $t('published_on') }} {{ \Carbon\Carbon::parse((string) ($post->post_date ?? now()))->format('Y-m-d') }}</span>

            <div class="article-content">
                {!! $contentHtml !!}
            </div>

            <div class="article-actions">
                <a class="btn btn-primary" href="{{ $localePrefix }}/blog">{{ $t('back_to_blog') }}</a>
            </div>
        </div>
    </article>

    <section class="related">
        <h3>{{ $t('related_articles') }}</h3>
        @if(($relatedPosts ?? collect())->isNotEmpty())
            <div class="related-grid">
                @foreach($relatedPosts as $related)
                    @php
                        $relatedSlug = rawurlencode(rawurldecode((string) ($related->post_name ?? '')));
                        $relatedUrl = $localePrefix . '/blog/' . $relatedSlug;
                        $relatedImage = $related->image ?: ($wpBaseUrl . '/wp-content/uploads/woocommerce-placeholder.png');
                    @endphp
                    <article class="related-card">
                        <a href="{{ $relatedUrl }}">
                            <img src="{{ $relatedImage }}" alt="{{ $related->post_title }}" loading="lazy" onerror="this.onerror=null;this.src='{{ $wpBaseUrl }}/wp-content/uploads/woocommerce-placeholder.png';">
                        </a>
                        <div class="inner">
                            <h4><a href="{{ $relatedUrl }}">{{ $related->post_title }}</a></h4>
                            <span class="meta">{{ \Carbon\Carbon::parse((string) ($related->post_date ?? now()))->format('Y-m-d') }}</span>
                            <a class="btn btn-light" href="{{ $relatedUrl }}">{{ $t('read_more') }}</a>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <p>{{ $t('no_related') }}</p>
        @endif
    </section>
</main>

@include('partials.shared-home-footer')
</body>
</html>
