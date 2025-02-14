<?php

namespace BookStack\Search;

use BookStack\Search\Options\ExactSearchOption;
use BookStack\Search\Options\FilterSearchOption;
use BookStack\Search\Options\SearchOption;
use BookStack\Search\Options\TagSearchOption;
use BookStack\Search\Options\TermSearchOption;
use Illuminate\Http\Request;

class SearchOptions
{
    /** @var SearchOptionSet<TermSearchOption> */
    public SearchOptionSet $searches;
    /** @var SearchOptionSet<ExactSearchOption> */
    public SearchOptionSet $exacts;
    /** @var SearchOptionSet<TagSearchOption> */
    public SearchOptionSet $tags;
    /** @var SearchOptionSet<FilterSearchOption> */
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
        $inputs = $request->only(['search', 'types', 'filters', 'exact', 'tags', 'extras']);

        $parsedStandardTerms = static::parseStandardTermString($inputs['search'] ?? '');
        $inputExacts = array_filter($inputs['exact'] ?? []);
        $instance->searches = SearchOptionSet::fromValueArray(array_filter($parsedStandardTerms['terms']), TermSearchOption::class);
        $instance->exacts = SearchOptionSet::fromValueArray(array_filter($parsedStandardTerms['exacts']), ExactSearchOption::class);
        $instance->exacts = $instance->exacts->merge(SearchOptionSet::fromValueArray($inputExacts, ExactSearchOption::class));
        $instance->tags = SearchOptionSet::fromValueArray(array_filter($inputs['tags'] ?? []), TagSearchOption::class);

        $cleanedFilters = [];
        foreach (($inputs['filters'] ?? []) as $filterKey => $filterVal) {
            if (empty($filterVal)) {
                continue;
            }
            $cleanedFilterVal = $filterVal === 'true' ? '' : $filterVal;
            $cleanedFilters[] = new FilterSearchOption($cleanedFilterVal, $filterKey);
        }

        if (isset($inputs['types']) && count($inputs['types']) < 4) {
            $cleanedFilters[] = new FilterSearchOption(implode('|', $inputs['types']), 'type');
        }

        $instance->filters = new SearchOptionSet($cleanedFilters);

        // Parse and merge in extras if provided
        if (!empty($inputs['extras'])) {
            $extras = static::fromString($inputs['extras']);
            $instance->searches = $instance->searches->merge($extras->searches);
            $instance->exacts = $instance->exacts->merge($extras->exacts);
            $instance->tags = $instance->tags->merge($extras->tags);
            $instance->filters = $instance->filters->merge($extras->filters);
        }

        return $instance;
    }

    /**
     * Decode a search string and add its contents to this instance.
     */
    protected function addOptionsFromString(string $searchString): void
    {
        /** @var array<string, SearchOption[]> $terms */
        $terms = [
            'exacts'   => [],
            'tags'     => [],
            'filters'  => [],
        ];

        $patterns = [
            'exacts'  => '/-?"((?:\\\\.|[^"\\\\])*)"/',
            'tags'    => '/-?\[(.*?)\]/',
            'filters' => '/-?\{(.*?)\}/',
        ];

        $constructors = [
            'exacts'   => fn(string $value, bool $negated) => new ExactSearchOption($value, $negated),
            'tags'     => fn(string $value, bool $negated) => new TagSearchOption($value, $negated),
            'filters'  => fn(string $value, bool $negated) => FilterSearchOption::fromContentString($value, $negated),
        ];

        // Parse special terms
        foreach ($patterns as $termType => $pattern) {
            $matches = [];
            preg_match_all($pattern, $searchString, $matches);
            if (count($matches) > 0) {
                foreach ($matches[1] as $index => $value) {
                    $negated = str_starts_with($matches[0][$index], '-');
                    $terms[$termType][] = $constructors[$termType]($value, $negated);
                }
                $searchString = preg_replace($pattern, '', $searchString);
            }
        }

        // Unescape exacts and backslash escapes
        foreach ($terms['exacts'] as $exact) {
            $exact->value = static::decodeEscapes($exact->value);
        }

        // Parse standard terms
        $parsedStandardTerms = static::parseStandardTermString($searchString);
        $this->searches = $this->searches
            ->merge(SearchOptionSet::fromValueArray($parsedStandardTerms['terms'], TermSearchOption::class))
            ->filterEmpty();
        $this->exacts = $this->exacts
            ->merge(new SearchOptionSet($terms['exacts']))
            ->merge(SearchOptionSet::fromValueArray($parsedStandardTerms['exacts'], ExactSearchOption::class))
            ->filterEmpty();

        // Add tags & filters
        $this->tags = $this->tags->merge(new SearchOptionSet($terms['tags']));
        $this->filters = $this->filters->merge(new SearchOptionSet($terms['filters']));
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
        $indexDelimiters = implode('', array_diff(str_split(SearchIndex::$delimiters), str_split(SearchIndex::$softDelimiters)));
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
            new SearchOptionSet([new FilterSearchOption($filterValue, $filterName)])
        );
    }

    /**
     * Encode this instance to a search string.
     */
    public function toString(): string
    {
        $options = [
            ...$this->searches->all(),
            ...$this->exacts->all(),
            ...$this->tags->all(),
            ...$this->filters->all(),
        ];

        $parts = array_map(fn(SearchOption $o) => $o->toString(), $options);

        return implode(' ', $parts);
    }

    /**
     * Get the search options that don't have UI controls provided for.
     * Provided back as a key => value array with the keys being expected
     * input names for a search form, and values being the option value.
     */
    public function getAdditionalOptionsString(): string
    {
        $options = [];

        // Handle filters without UI support
        $userFilters = ['updated_by', 'created_by', 'owned_by'];
        $unsupportedFilters = ['is_template', 'sort_by'];
        foreach ($this->filters->all() as $filter) {
            if (in_array($filter->getKey(), $userFilters, true) && $filter->value !== null && $filter->value !== 'me') {
                $options[] = $filter;
            } else if (in_array($filter->getKey(), $unsupportedFilters, true)) {
                $options[] = $filter;
            }
        }

        // Negated items
        array_push($options, ...$this->exacts->negated()->all());
        array_push($options, ...$this->tags->negated()->all());
        array_push($options, ...$this->filters->negated()->all());

        return implode(' ', array_map(fn(SearchOption $o) => $o->toString(), $options));
    }
}
