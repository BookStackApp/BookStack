<?php

namespace BookStack\Search;

use Illuminate\Http\Request;

class SearchOptions
{
    public array $searches = [];
    public array $exacts = [];
    public array $tags = [];
    public array $filters = [];

    /**
     * Create a new instance from a search string.
     */
    public static function fromString(string $search): self
    {
        $decoded = static::decode($search);
        $instance = new SearchOptions();
        foreach ($decoded as $type => $value) {
            $instance->$type = $value;
        }

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
        $instance->searches = array_filter($parsedStandardTerms['terms']);
        $instance->exacts = array_filter($parsedStandardTerms['exacts']);

        array_push($instance->exacts, ...array_filter($inputs['exact'] ?? []));

        $instance->tags = array_filter($inputs['tags'] ?? []);

        foreach (($inputs['filters'] ?? []) as $filterKey => $filterVal) {
            if (empty($filterVal)) {
                continue;
            }
            $instance->filters[$filterKey] = $filterVal === 'true' ? '' : $filterVal;
        }

        if (isset($inputs['types']) && count($inputs['types']) < 4) {
            $instance->filters['type'] = implode('|', $inputs['types']);
        }

        return $instance;
    }

    /**
     * Decode a search string into an array of terms.
     */
    protected static function decode(string $searchString): array
    {
        $terms = [
            'searches' => [],
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
        foreach ($terms['exacts'] as $index => $exact) {
            $terms['exacts'][$index] = static::decodeEscapes($exact);
        }

        // Parse standard terms
        $parsedStandardTerms = static::parseStandardTermString($searchString);
        array_push($terms['searches'], ...$parsedStandardTerms['terms']);
        array_push($terms['exacts'], ...$parsedStandardTerms['exacts']);

        // Split filter values out
        $splitFilters = [];
        foreach ($terms['filters'] as $filter) {
            $explodedFilter = explode(':', $filter, 2);
            $splitFilters[$explodedFilter[0]] = (count($explodedFilter) > 1) ? $explodedFilter[1] : '';
        }
        $terms['filters'] = $splitFilters;

        // Filter down terms where required
        $terms['exacts'] = array_filter($terms['exacts']);
        $terms['searches'] = array_filter($terms['searches']);

        return $terms;
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
     * Encode this instance to a search string.
     */
    public function toString(): string
    {
        $parts = $this->searches;

        foreach ($this->exacts as $term) {
            $escaped = str_replace('\\', '\\\\', $term);
            $escaped = str_replace('"', '\"', $escaped);
            $parts[] = '"' . $escaped . '"';
        }

        foreach ($this->tags as $term) {
            $parts[] = "[{$term}]";
        }

        foreach ($this->filters as $filterName => $filterVal) {
            $parts[] = '{' . $filterName . ($filterVal ? ':' . $filterVal : '') . '}';
        }

        return implode(' ', $parts);
    }
}
