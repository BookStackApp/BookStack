<?php

namespace BookStack\Util;

use Illuminate\Http\Request;

/**
 * Handled options commonly used for item lists within the system, providing a standard
 * model for handling and validating sort, order and search options.
 */
class SimpleListOptions
{
    protected string $typeKey;
    protected string $sort;
    protected string $order;
    protected string $search;
    protected array $sortOptions = [];

    public function __construct(string $typeKey, string $sort, string $order, string $search = '')
    {
        $this->typeKey = $typeKey;
        $this->sort = $sort;
        $this->order = $order;
        $this->search = $search;
    }

    /**
     * Create a new instance from the given request.
     * Takes the item type (plural) that's used as a key for storing sort preferences.
     */
    public static function fromRequest(Request $request, string $typeKey, bool $sortDescDefault = false): self
    {
        $search = $request->get('search', '');
        $sort = setting()->getForCurrentUser($typeKey . '_sort', '');
        $order = setting()->getForCurrentUser($typeKey . '_sort_order', $sortDescDefault ? 'desc' : 'asc');

        return new self($typeKey, $sort, $order, $search);
    }

    /**
     * Configure the valid sort options for this set of list options.
     * Provided sort options must be an array, keyed by search properties
     * with values being user-visible option labels.
     * Returns current options for easy fluent usage during creation.
     */
    public function withSortOptions(array $sortOptions): self
    {
        $this->sortOptions = array_merge($this->sortOptions, $sortOptions);

        return $this;
    }

    /**
     * Get the current order option.
     */
    public function getOrder(): string
    {
        return strtolower($this->order) === 'desc' ? 'desc' : 'asc';
    }

    /**
     * Get the current sort option.
     */
    public function getSort(): string
    {
        $default = array_key_first($this->sortOptions) ?? 'name';
        $sort = $this->sort ?: $default;

        if (empty($this->sortOptions) || array_key_exists($sort, $this->sortOptions)) {
            return $sort;
        }

        return $default;
    }

    /**
     * Get the set search term.
     */
    public function getSearch(): string
    {
        return $this->search;
    }

    /**
     * Get the data to append for pagination.
     */
    public function getPaginationAppends(): array
    {
        return ['search' => $this->search];
    }

    /**
     * Get the data required by the sort control view.
     */
    public function getSortControlData(): array
    {
        return [
            'options' => $this->sortOptions,
            'order' => $this->getOrder(),
            'sort' => $this->getSort(),
            'type' => $this->typeKey,
        ];
    }
}
