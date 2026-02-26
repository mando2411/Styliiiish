@php
    $currentLocale = $currentLocale ?? 'ar';
    $localePrefix = $localePrefix ?? ($currentLocale === 'en' ? '/en' : '/ar');
    $wpmlLocale = $currentLocale === 'en' ? 'en' : 'ar';

    $categories = collect();

    try {
        $hasTerms = \Illuminate\Support\Facades\Schema::hasTable('wp_terms')
            && \Illuminate\Support\Facades\Schema::hasTable('wp_term_taxonomy');

        if ($hasTerms) {
            $baseQuery = \Illuminate\Support\Facades\DB::table('wp_term_taxonomy as tt')
                ->join('wp_terms as t', 't.term_id', '=', 'tt.term_id')
                ->where('tt.taxonomy', 'product_cat')
                ->where('tt.parent', 0)
                ->where('tt.count', '>', 0)
                ->whereNotIn('t.slug', ['uncategorized'])
                ->select('t.name', 't.slug', 'tt.count');

            if (\Illuminate\Support\Facades\Schema::hasTable('wp_icl_translations')) {
                $categories = $baseQuery
                    ->join('wp_icl_translations as tr', function ($join) {
                        $join->on('tr.element_id', '=', 'tt.term_taxonomy_id')
                            ->where('tr.element_type', '=', 'tax_product_cat');
                    })
                    ->where('tr.language_code', $wpmlLocale)
                    ->orderByDesc('tt.count')
                    ->orderBy('t.name')
                    ->limit(14)
                    ->get();
            }

            if ($categories->isEmpty()) {
                $categories = $baseQuery
                    ->orderByDesc('tt.count')
                    ->orderBy('t.name')
                    ->limit(14)
                    ->get();
            }
        }
    } catch (\Throwable $e) {
        $categories = collect();
    }
@endphp

@if($categories->isNotEmpty())
    <div class="header-categories-strip" aria-label="{{ $currentLocale === 'en' ? 'Product Categories' : 'أقسام المنتجات' }}">
        <div class="container categories-strip-inner">
            @foreach($categories as $category)
                <a class="category-strip-chip" href="{{ $localePrefix }}/shop?q={{ rawurlencode((string) $category->name) }}">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>
@endif
