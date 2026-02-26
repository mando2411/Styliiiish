@php
    $currentLocale = $currentLocale ?? 'ar';
    $localePrefix = $localePrefix ?? ($currentLocale === 'en' ? '/en' : '/ar');
    $wpmlLocale = $currentLocale === 'en' ? 'en' : 'ar';
    $wpmlLocalePattern = $wpmlLocale . '%';

    $categoryGroups = collect();

    $toArabicCategoryLabel = function (string $name, ?string $slug = null) use ($currentLocale): string {
        if ($currentLocale !== 'ar') {
            return $name;
        }

        $trimmed = trim($name);
        if ($trimmed === '' || preg_match('/[\x{0600}-\x{06FF}]/u', $trimmed)) {
            return $trimmed;
        }

        $normalize = function (string $value): string {
            $value = mb_strtolower(trim($value));
            $value = str_replace(['_', '-', '&'], [' ', ' ', ' and '], $value);
            $value = preg_replace('/\s+/u', ' ', $value) ?? $value;
            return trim($value);
        };

        $fullMap = [
            'wedding dresses' => 'فساتين زفاف',
            'wedding dress' => 'فستان زفاف',
            'evening dresses' => 'فساتين سهرة',
            'evening dress' => 'فستان سهرة',
            'engagement dresses' => 'فساتين خطوبة',
            'engagement dress' => 'فستان خطوبة',
            'party dresses' => 'فساتين حفلات',
            'party dress' => 'فستان حفلة',
            'formal dresses' => 'فساتين رسمية',
            'formal dress' => 'فستان رسمي',
            'cocktail dresses' => 'فساتين كوكتيل',
            'cocktail dress' => 'فستان كوكتيل',
            'maxi dresses' => 'فساتين ماكسي',
            'midi dresses' => 'فساتين ميدي',
            'mini dresses' => 'فساتين قصيرة',
            'maternity dresses' => 'فساتين حوامل',
            'plus size dresses' => 'فساتين مقاسات كبيرة',
            'bridal' => 'عرائس',
            'bags' => 'شنط',
            'bag' => 'شنطة',
            'shoes' => 'أحذية',
            'accessories' => 'إكسسوارات',
            'jumpsuits' => 'جمبسوت',
            'abaya' => 'عبايات',
            'abayas' => 'عبايات',
        ];

        $wordMap = [
            'women' => 'نسائي',
            'woman' => 'نسائي',
            'girls' => 'بناتي',
            'girl' => 'بناتي',
            'dress' => 'فستان',
            'dresses' => 'فساتين',
            'wedding' => 'زفاف',
            'bridal' => 'عرائس',
            'engagement' => 'خطوبة',
            'evening' => 'سهرة',
            'party' => 'حفلات',
            'formal' => 'رسمي',
            'casual' => 'كاجوال',
            'bags' => 'شنط',
            'bag' => 'شنطة',
            'shoes' => 'أحذية',
            'accessories' => 'إكسسوارات',
            'plus' => 'مقاسات',
            'size' => 'كبيرة',
            'maxi' => 'ماكسي',
            'midi' => 'ميدي',
            'mini' => 'قصير',
            'new' => 'جديد',
            'used' => 'مستعمل',
        ];

        $normalizedName = $normalize($trimmed);
        $normalizedSlug = $normalize((string) $slug);

        if ($normalizedName !== '' && isset($fullMap[$normalizedName])) {
            return $fullMap[$normalizedName];
        }

        if ($normalizedSlug !== '' && isset($fullMap[$normalizedSlug])) {
            return $fullMap[$normalizedSlug];
        }

        $parts = preg_split('/\s+/u', $normalizedName) ?: [];
        $translatedParts = [];
        $translatedCount = 0;
        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }
            if (isset($wordMap[$part])) {
                $translatedParts[] = $wordMap[$part];
                $translatedCount++;
            } else {
                $translatedParts[] = $part;
            }
        }

        if ($translatedCount > 0) {
            return trim(implode(' ', $translatedParts));
        }

        return $trimmed;
    };

    try {
        $hasTerms = \Illuminate\Support\Facades\Schema::hasTable('wp_terms')
            && \Illuminate\Support\Facades\Schema::hasTable('wp_term_taxonomy');

        if ($hasTerms) {
            $baseTermsQuery = \Illuminate\Support\Facades\DB::table('wp_term_taxonomy as tt')
                ->join('wp_terms as t', 't.term_id', '=', 'tt.term_id')
                ->where('tt.taxonomy', 'product_cat')
                ->whereNotIn('t.slug', ['uncategorized'])
                ->select('tt.term_id', 'tt.parent', 'tt.count', 't.name', 't.slug');

            $localizedTerms = collect();

            $hasWpml = \Illuminate\Support\Facades\Schema::hasTable('wp_icl_translations');

            if ($hasWpml) {
                $localizedByTaxonomyId = (clone $baseTermsQuery)
                    ->join('wp_icl_translations as tr', function ($join) {
                        $join->on('tr.element_id', '=', 'tt.term_taxonomy_id')
                            ->where('tr.element_type', '=', 'tax_product_cat');
                    })
                    ->where('tr.language_code', 'like', $wpmlLocalePattern)
                    ->orderBy('t.name')
                    ->get();

                $localizedByTermId = (clone $baseTermsQuery)
                    ->join('wp_icl_translations as tr', function ($join) {
                        $join->on('tr.element_id', '=', 'tt.term_id')
                            ->where('tr.element_type', '=', 'tax_product_cat');
                    })
                    ->where('tr.language_code', 'like', $wpmlLocalePattern)
                    ->orderBy('t.name')
                    ->get();

                $localizedTerms = $localizedByTermId->count() > $localizedByTaxonomyId->count()
                    ? $localizedByTermId
                    : $localizedByTaxonomyId;
            }

            if (!$hasWpml && $localizedTerms->isEmpty()) {
                $localizedTerms = $baseTermsQuery
                    ->orderBy('t.name')
                    ->get();
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
                    $parentDisplayName = $toArabicCategoryLabel((string) $parent->name, (string) ($parent->slug ?? ''));
                @endphp
                <span class="category-strip-group">
                    <a class="category-strip-chip category-strip-parent" href="{{ $localePrefix }}/shop?q={{ rawurlencode((string) $parent->name) }}">
                        {{ $parentDisplayName }}
                    </a>
                    @foreach($children->take(6) as $child)
                        @php
                            $childDisplayName = $toArabicCategoryLabel((string) $child->name, (string) ($child->slug ?? ''));
                        @endphp
                        <a class="category-strip-chip category-strip-sub" href="{{ $localePrefix }}/shop?q={{ rawurlencode((string) $child->name) }}">
                            {{ $childDisplayName }}
                        </a>
                    @endforeach
                </span>
            @endforeach
        </div>
    </div>
@endif
