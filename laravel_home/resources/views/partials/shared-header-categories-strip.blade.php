@php
    $pathLocale = strtolower((string) request()->segment(1));
    $currentLocale = in_array($pathLocale, ['ar', 'en'], true) ? $pathLocale : ($currentLocale ?? 'ar');
    $localePrefix = $localePrefix ?? ($currentLocale === 'en' ? '/en' : '/ar');

    $categoryGroups = collect();

    try {
        $hasTerms = \Illuminate\Support\Facades\Schema::hasTable('wp_terms')
            && \Illuminate\Support\Facades\Schema::hasTable('wp_term_taxonomy');

        if ($hasTerms) {
            $baseTermsQuery = \Illuminate\Support\Facades\DB::table('wp_term_taxonomy as tt')
                ->join('wp_terms as t', 't.term_id', '=', 'tt.term_id')
                ->where('tt.taxonomy', 'product_cat')
                ->whereNotIn('t.slug', ['uncategorized'])
                ->select('tt.term_id', 'tt.parent', 'tt.count', 't.name', 't.slug');
            $localizedTerms = $baseTermsQuery
                ->orderBy('t.name')
                ->get();

            if ($localizedTerms->isNotEmpty()) {
                $targetPrefix = $currentLocale === 'en' ? 'en' : 'ar';
                $trpSettingsRaw = \Illuminate\Support\Facades\Schema::hasTable('wp_options')
                    ? \Illuminate\Support\Facades\DB::table('wp_options')->where('option_name', 'trp_settings')->value('option_value')
                    : null;

                $trpSettings = @unserialize((string) $trpSettingsRaw);
                $trpSettings = is_array($trpSettings) ? $trpSettings : [];

                $defaultLanguage = strtolower(trim((string) ($trpSettings['default-language'] ?? '')));
                $translationLanguages = collect($trpSettings['translation-languages'] ?? [])
                    ->filter(fn ($value) => is_string($value) && trim($value) !== '')
                    ->map(fn ($value) => strtolower(trim((string) $value)))
                    ->values();

                $targetLanguage = (string) ($translationLanguages->first(function ($languageCode) use ($targetPrefix) {
                    return str_starts_with((string) $languageCode, $targetPrefix);
                }) ?? '');

                if ($defaultLanguage !== '' && $targetLanguage !== '' && $defaultLanguage !== $targetLanguage) {
                    $dictionaryTable = 'wp_trp_dictionary_' . $defaultLanguage . '_' . $targetLanguage;
                    if (\Illuminate\Support\Facades\Schema::hasTable($dictionaryTable)) {
                        $lookupNames = $localizedTerms
                            ->pluck('name')
                            ->map(fn ($value) => trim((string) $value))
                            ->filter(fn ($value) => $value !== '')
                            ->unique()
                            ->values();

                        if ($lookupNames->isNotEmpty()) {
                            $dictionaryRows = \Illuminate\Support\Facades\DB::table($dictionaryTable)
                                ->whereIn('original', $lookupNames->all())
                                ->where('status', '!=', 0)
                                ->whereNotNull('translated')
                                ->where('translated', '!=', '')
                                ->select('original', 'translated')
                                ->get();

                            if ($dictionaryRows->isNotEmpty()) {
                                $translationMap = [];
                                foreach ($dictionaryRows as $row) {
                                    $original = trim((string) ($row->original ?? ''));
                                    $translated = trim((string) ($row->translated ?? ''));
                                    if ($original !== '' && $translated !== '' && !array_key_exists($original, $translationMap)) {
                                        $translationMap[$original] = $translated;
                                    }
                                }

                                if (!empty($translationMap)) {
                                    $localizedTerms = $localizedTerms->map(function ($term) use ($translationMap) {
                                        $originalName = trim((string) ($term->name ?? ''));
                                        if ($originalName !== '' && isset($translationMap[$originalName])) {
                                            $term->name = (string) $translationMap[$originalName];
                                        }

                                        return $term;
                                    });
                                }
                            }
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
                    <a class="category-strip-chip category-strip-parent" href="{{ $localePrefix }}/shop?category={{ rawurlencode((string) $parent->slug) }}">
                        {{ $parent->name }}
                    </a>
                    @foreach($children->take(6) as $child)
                        <a class="category-strip-chip category-strip-sub" href="{{ $localePrefix }}/shop?category={{ rawurlencode((string) $child->slug) }}">
                            {{ $child->name }}
                        </a>
                    @endforeach
                </span>
            @endforeach
        </div>
    </div>
@endif
