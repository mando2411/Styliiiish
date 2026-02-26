@php
    $currentLocale = $currentLocale ?? 'ar';
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
                ->select('tt.term_taxonomy_id', 'tt.term_id', 'tt.parent', 'tt.count', 't.name', 't.slug');

            $localizedTerms = $baseTermsQuery
                ->orderBy('t.name')
                ->get();

            $hasWpml = \Illuminate\Support\Facades\Schema::hasTable('wp_icl_translations');

            if ($hasWpml && $localizedTerms->isNotEmpty()) {
                $sourceTaxonomyIds = $localizedTerms
                    ->pluck('term_taxonomy_id')
                    ->filter(fn ($id) => !is_null($id))
                    ->map(fn ($id) => (int) $id)
                    ->unique()
                    ->values();

                if ($sourceTaxonomyIds->isNotEmpty()) {
                    $translationPairs = \Illuminate\Support\Facades\DB::table('wp_icl_translations as source')
                        ->join('wp_icl_translations as localized', function ($join) use ($wpmlLocalePattern) {
                            $join->on('source.trid', '=', 'localized.trid')
                                ->where('localized.element_type', '=', 'tax_product_cat')
                                ->where('localized.language_code', 'like', $wpmlLocalePattern);
                        })
                        ->where('source.element_type', 'tax_product_cat')
                        ->whereIn('source.element_id', $sourceTaxonomyIds->all())
                        ->select('source.element_id as source_taxonomy_id', 'localized.element_id as localized_taxonomy_id')
                        ->get();

                    if ($translationPairs->isNotEmpty()) {
                        $localizedTaxonomyIds = $translationPairs
                            ->pluck('localized_taxonomy_id')
                            ->filter(fn ($id) => !is_null($id))
                            ->map(fn ($id) => (int) $id)
                            ->unique()
                            ->values();

                        if ($localizedTaxonomyIds->isNotEmpty()) {
                            $localizedNamesByTaxonomyId = \Illuminate\Support\Facades\DB::table('wp_term_taxonomy as tt')
                                ->join('wp_terms as t', 't.term_id', '=', 'tt.term_id')
                                ->whereIn('tt.term_taxonomy_id', $localizedTaxonomyIds->all())
                                ->select('tt.term_taxonomy_id', 't.name', 't.slug')
                                ->get()
                                ->keyBy(fn ($row) => (int) $row->term_taxonomy_id);

                            $sourceToLocalizedTaxonomyId = $translationPairs
                                ->mapWithKeys(function ($row) {
                                    return [(int) $row->source_taxonomy_id => (int) $row->localized_taxonomy_id];
                                });

                            $localizedTerms = $localizedTerms->map(function ($term) use ($sourceToLocalizedTaxonomyId, $localizedNamesByTaxonomyId) {
                                $sourceId = (int) ($term->term_taxonomy_id ?? 0);
                                $localizedId = (int) ($sourceToLocalizedTaxonomyId->get($sourceId, 0));
                                if ($localizedId > 0 && $localizedNamesByTaxonomyId->has($localizedId)) {
                                    $localizedTerm = $localizedNamesByTaxonomyId->get($localizedId);
                                    $term->name = (string) ($localizedTerm->name ?? $term->name);
                                    $term->slug = (string) ($localizedTerm->slug ?? $term->slug);
                                }

                                return $term;
                            });
                        }
                    }
                }
            }

            if ($localizedTerms->isNotEmpty()) {
                $parents = $localizedTerms->where('parent', 0)->keyBy('term_id');
                $childrenByParent = $localizedTerms
                    ->where('parent', '!=', 0)
                    ->where('count', '>', 0)
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
