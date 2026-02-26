@php
    $pathLocale = request()->segment(1);
    $currentLocale = in_array($pathLocale, ['ar', 'en'], true)
        ? $pathLocale
        : ($currentLocale ?? 'ar');
    $localePrefix = $localePrefix ?? ($currentLocale === 'en' ? '/en' : '/ar');
    $wpmlLocale = $currentLocale === 'en' ? 'en' : 'ar';
    $wpmlLocalePattern = $wpmlLocale . '%';

    $categoryGroups = collect();

    try {
        $hasTerms = \Illuminate\Support\Facades\Schema::hasTable('wp_terms')
            && \Illuminate\Support\Facades\Schema::hasTable('wp_term_taxonomy');

        if ($hasTerms) {
            $baseTermsQuery = \Illuminate\Support\Facades\DB::table('wp_term_taxonomy as tt')
                ->join('wp_terms as t', 't.term_id', '=', 'tt.term_id')
                ->where('tt.taxonomy', 'product_cat')
                ->whereNotIn('t.slug', ['uncategorized'])
                ->where('tt.count', '>', 0)
                ->select('tt.term_taxonomy_id', 'tt.term_id', 'tt.parent', 'tt.count', 't.name', 't.slug');

            $localizedTerms = collect();

            $hasWpml = \Illuminate\Support\Facades\Schema::hasTable('wp_icl_translations');

            if ($hasWpml) {
                $localizedByTaxonomyId = (clone $baseTermsQuery)
                    ->join('wp_icl_translations as tr', function ($join) {
                        $join->on('tr.element_id', '=', 'tt.term_taxonomy_id')
                            ->where('tr.element_type', '=', 'tax_product_cat');
                    })
                    ->where('tr.language_code', $wpmlLocale)
                    ->orderByDesc('tt.count')
                    ->orderBy('t.name')
                    ->get();

                if ($localizedByTaxonomyId->isEmpty()) {
                    $localizedByTaxonomyId = (clone $baseTermsQuery)
                        ->join('wp_icl_translations as tr', function ($join) {
                            $join->on('tr.element_id', '=', 'tt.term_taxonomy_id')
                                ->where('tr.element_type', '=', 'tax_product_cat');
                        })
                        ->where('tr.language_code', 'like', $wpmlLocalePattern)
                        ->orderByDesc('tt.count')
                        ->orderBy('t.name')
                        ->get();
                }

                $localizedByTermId = collect();
                if ($localizedByTaxonomyId->isEmpty()) {
                    $localizedByTermId = (clone $baseTermsQuery)
                        ->join('wp_icl_translations as tr', function ($join) {
                            $join->on('tr.element_id', '=', 'tt.term_id')
                                ->where('tr.element_type', '=', 'tax_product_cat');
                        })
                        ->where('tr.language_code', $wpmlLocale)
                        ->orderByDesc('tt.count')
                        ->orderBy('t.name')
                        ->get();

                    if ($localizedByTermId->isEmpty()) {
                        $localizedByTermId = (clone $baseTermsQuery)
                            ->join('wp_icl_translations as tr', function ($join) {
                                $join->on('tr.element_id', '=', 'tt.term_id')
                                    ->where('tr.element_type', '=', 'tax_product_cat');
                            })
                            ->where('tr.language_code', 'like', $wpmlLocalePattern)
                            ->orderByDesc('tt.count')
                            ->orderBy('t.name')
                            ->get();
                    }
                }

                $localizedTerms = $localizedByTaxonomyId->isNotEmpty()
                    ? $localizedByTaxonomyId
                    : $localizedByTermId;
            }

            if ($localizedTerms->isEmpty()) {
                $localizedTerms = (clone $baseTermsQuery)
                    ->orderByDesc('tt.count')
                    ->orderBy('t.name')
                    ->get();
            }

            if ($localizedTerms->isNotEmpty()) {
                $parents = $localizedTerms->where('parent', 0)->keyBy('term_id');
                $childrenByParent = $localizedTerms
                    ->where('parent', '!=', 0)
                    ->groupBy('parent');

                $categoryGroups = $parents
                    ->map(function ($parent) use ($childrenByParent) {
                        $children = $childrenByParent->get($parent->term_id, collect())->values();
                        $childrenTotal = (int) $children->sum('count');
                        $parentCount = (int) ($parent->count ?? 0);
                        $score = max($parentCount, $childrenTotal);

                        if ($score <= 0) {
                            return null;
                        }

                        return [
                            'parent' => $parent,
                            'children' => $children,
                            'score' => $score,
                        ];
                    })
                    ->filter()
                    ->sortByDesc('score')
                    ->take(10)
                    ->values();
            }
        }
    } catch (\Throwable $e) {
        $categoryGroups = collect();
    }
@endphp

@if($categoryGroups->isNotEmpty())
    <div class="header-categories-strip" aria-label="{{ $currentLocale === 'en' ? 'Product Categories' : 'أقسام المنتجات' }}">
        <div class="container categories-strip-inner">
            @foreach($categoryGroups as $group)
                @php
                    $parent = $group['parent'];
                    $children = $group['children'];
                @endphp
                <span class="category-strip-group">
                    <a class="category-strip-chip category-strip-parent" href="{{ $localePrefix }}/shop?q={{ rawurlencode((string) $parent->name) }}">
                        {{ $parent->name }}
                    </a>
                    @foreach($children->take(6) as $child)
                        <a class="category-strip-chip category-strip-sub" href="{{ $localePrefix }}/shop?q={{ rawurlencode((string) $child->name) }}">
                            {{ $child->name }}
                        </a>
                    @endforeach
                </span>
            @endforeach
        </div>
    </div>
@endif
