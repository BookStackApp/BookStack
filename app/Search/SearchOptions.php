<?php

namespace BookStack\Search;

use Illuminate\Http\Request;

class SearchOptions
{
    public SearchOptionSet $searches;
    public SearchOptionSet $exacts;
    public SearchOptionSet $tags;
    public SearchOptionSet $filters;

    public function __construct()
    {
        $this->searches = new SearchOptionSet();
        $this->exacts = new SearchOptionSet();
        $this->tags = new SearchOptionSet();
        $this->filters = new SearchOptionSet();
    }

    /**
     * Create a new instance from a search string.
     */
    public static function fromString(string $search): self
    {
        $instance = new self();
        $instance->addOptionsFromString($search);
        return $instance;
    }

    /**
     * Create a new instance from a request.
     * Will look for a classic string term and use that
     * Otherwise we'll use the details from an advanced search form.
     */
    public static function fromRequest(Request $request): self
    {
        if (!$request->has('search') && !$request->has('term')) {
            return static::fromString('');
        }

        if ($request->has('term')) {
            return static::fromString($request->get('term'));
        }

        $instance = new SearchOptions();
        $inputs = $request->only(['search', 'types', 'filters', 'exact', 'tags']);

        $parsedStandardTerms = static::parseStandardTermString($inputs['search'] ?? '');
        $inputExacts = array_filter($inputs['exact'] ?? []);
        $instance->searches = SearchOptionSet::fromValueArray(array_filter($parsedStandardTerms['terms']));
        $instance->exacts = SearchOptionSet::fromValueArray(array_filter($parsedStandardTerms['exacts']));
        $instance->exacts = $instance->exacts->merge(SearchOptionSet::fromValueArray($inputExacts));
        $instance->tags = SearchOptionSet::fromValueArray(array_filter($inputs['tags'] ?? []));

        $keyedFilters = [];
        foreach (($inputs['filters'] ?? []) as $filterKey => $filterVal) {
            if (empty($filterVal)) {
                continue;
            }
            $cleanedFilterVal = $filterVal === 'true' ? '' : $filterVal;
            $keyedFilters[$filterKey] = new SearchOption($cleanedFilterVal);
        }

        if (isset($inputs['types']) && count($inputs['types']) < 4) {
            $keyedFilters['type'] = new SearchOption(implode('|', $inputs['types']));
        }

        $instance->filters = new SearchOptionSet($keyedFilters);

        return $instance;
    }

    /**
     * Decode a search string and add its contents to this instance.
     */
    protected function addOptionsFromString(string $searchString): void
    {
        /** @var array<string, string[]> $terms */
        $terms = [
            'exacts'   => [],
            'tags'     => [],
            'filters'  => [],
        ];

        $patterns = [
            'exacts'  => '/"((?:\\\\.|[^"\\\\])*)"/',
            'tags'    => '/\[(.*?)\]/',
            'filters' => '/\{(.*?)\}/',
        ];

        // Parse special terms
        foreach ($patterns as $termType => $pattern) {
            $matches = [];
            preg_match_all($pattern, $searchString, $matches);
            if (count($matches) > 0) {
                $terms[$termType] = $matches[1];
                $searchString = preg_replace($pattern, '', $searchString);
            }
        }

        // Unescape exacts and backslash escapes
        $escapedExacts = array_map(fn(string $term) => static::decodeEscapes($term), $terms['exacts']);

        // Parse standard terms
        $parsedStandardTerms = static::parseStandardTermString($searchString);
        $this->searches = $this->searches
            ->merge(SearchOptionSet::fromValueArray($parsedStandardTerms['terms']))
            ->filterEmpty();
        $this->exacts = $this->exacts
            ->merge(SearchOptionSet::fromValueArray($escapedExacts))
            ->merge(SearchOptionSet::fromValueArray($parsedStandardTerms['exacts']))
            ->filterEmpty();

        // Add tags
        $this->tags = $this->tags->merge(SearchOptionSet::fromValueArray($terms['tags']));

        // Split filter values out
        /** @var array<string, SearchOption> $splitFilters */
        $splitFilters = [];
        foreach ($terms['filters'] as $filter) {
            $explodedFilter = explode(':', $filter, 2);
            $filterValue = (count($explodedFilter) > 1) ? $explodedFilter[1] : '';
            $splitFilters[$explodedFilter[0]] = new SearchOption($filterValue);
        }
        $this->filters = $this->filters->merge(new SearchOptionSet($splitFilters));
    }

    /**
     * Decode backslash escaping within the input string.
     */
    protected static function decodeEscapes(string $input): string
    {
        $decoded = "";
        $escaping = false;

        foreach (str_split($input) as $char) {
            if ($escaping) {
                $decoded .= $char;
                $escaping = false;
            } else if ($char === '\\') {
                $escaping = true;
            } else {
                $decoded .= $char;
            }
        }

        return $decoded;
    }

    /**
     * Parse a standard search term string into individual search terms and
     * convert any required terms to exact matches. This is done since some
     * characters will never be in the standard index, since we use them as
     * delimiters, and therefore we convert a term to be exact if it
     * contains one of those delimiter characters.
     *
     * @return array{terms: array<string>, exacts: array<string>}
     */
    protected static function parseStandardTermString(string $termString): array
    {
        $terms = explode(' ', $termString);
        $indexDelimiters = SearchIndex::$delimiters;
        $parsed = [
            'terms'  => [],
            'exacts' => [],
        ];

        foreach ($terms as $searchTerm) {
            if ($searchTerm === '') {
                continue;
            }

            $becomeExact = (strpbrk($searchTerm, $indexDelimiters) !== false);
            $parsed[$becomeExact ? 'exacts' : 'terms'][] = $searchTerm;
        }

        return $parsed;
    }

    /**
     * Set the value of a specific filter in the search options.
     */
    public function setFilter(string $filterName, string $filterValue = ''): void
    {
        $this->filters = $this->filters->merge(
            new SearchOptionSet([$filterName => new SearchOption($filterValue)])
        );
    }

    /**
     * Encode this instance to a search string.
     */
    public function toString(): string
    {
        $parts = $this->searches->toValueArray();

        foreach ($this->exacts->toValueArray() as $term) {
            $escaped = str_replace('\\', '\\\\', $term);
            $escaped = str_replace('"', '\"', $escaped);
            $parts[] = '"' . $escaped . '"';
        }

        foreach ($this->tags->toValueArray() as $term) {
            $parts[] = "[{$term}]";
        }

        foreach ($this->filters->toValueMap() as $filterName => $filterVal) {
            $parts[] = '{' . $filterName . ($filterVal ? ':' . $filterVal : '') . '}';
        }

        return implode(' ', $parts);
    }

    /**
     * Get the search options that don't have UI controls provided for.
     * Provided back as a key => value array with the keys being expected
     * input names for a search form, and values being the option value.
     *
     * @return array<string, string>
     */
    public function getHiddenInputValuesByFieldName(): array
    {
        $options = [];

        // Non-[created/updated]-by-me options
        $filterMap = $this->filters->toValueMap();
        foreach (['updated_by', 'created_by', 'owned_by'] as $filter) {
            $value = $filterMap[$filter] ?? null;
            if ($value !== null && $value !== 'me') {
                $options["filters[$filter]"] = $value;
            }
        }

        // TODO - Negated

        return $options;
    }
}
