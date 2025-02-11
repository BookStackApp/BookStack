<?php

namespace BookStack\Sorting;

/**
 * Generate a URL with multiple parameters for sorting purposes.
 * Works out the logic to set the correct sorting direction
 * Discards empty parameters and allows overriding.
 */
class SortUrl
{
    public function __construct(
        protected string $path,
        protected array $data,
        protected array $overrideData = []
    ) {
    }

    public function withOverrideData(array $overrideData = []): self
    {
        return new self($this->path, $this->data, $overrideData);
    }

    public function build(): string
    {
        $queryStringSections = [];
        $queryData = array_merge($this->data, $this->overrideData);

        // Change sorting direction if already sorted on current attribute
        if (isset($this->overrideData['sort']) && $this->overrideData['sort'] === $this->data['sort']) {
            $queryData['order'] = ($this->data['order'] === 'asc') ? 'desc' : 'asc';
        } elseif (isset($this->overrideData['sort'])) {
            $queryData['order'] = 'asc';
        }

        foreach ($queryData as $name => $value) {
            $trimmedVal = trim($value);
            if ($trimmedVal !== '') {
                $queryStringSections[] = urlencode($name) . '=' . urlencode($trimmedVal);
            }
        }

        if (count($queryStringSections) === 0) {
            return url($this->path);
        }

        return url($this->path . '?' . implode('&', $queryStringSections));
    }
}
