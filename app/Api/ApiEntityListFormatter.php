<?php

namespace BookStack\Api;

use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;

class ApiEntityListFormatter
{
    /**
     * The list to be formatted.
     * @var Entity[]
     */
    protected array $list = [];

    /**
     * Whether to include related titles in the response.
     */
    protected bool $includeRelatedTitles = false;

    /**
     * The fields to show in the formatted data.
     * Can be a plain string array item for a direct model field (If existing on model).
     * If the key is a string, with a callable value, the return value of the callable
     * will be used for the resultant value. A null return value will omit the property.
     * @var array<string|int, string|callable>
     */
    protected array $fields = [
        'id',
        'name',
        'slug',
        'book_id',
        'chapter_id',
        'draft',
        'template',
        'priority',
        'created_at',
        'updated_at',
    ];

    public function __construct(array $list)
    {
        $this->list = $list;

        // Default dynamic fields
        $this->withField('url', fn(Entity $entity) => $entity->getUrl());
    }

    /**
     * Add a field to be used in the formatter, with the property using the given
     * name and value being the return type of the given callback.
     */
    public function withField(string $property, callable $callback): self
    {
        $this->fields[$property] = $callback;
        return $this;
    }

    /**
     * Show the 'type' property in the response reflecting the entity type.
     * EG: page, chapter, bookshelf, book
     * To be included in results with non-pre-determined types.
     */
    public function withType(): self
    {
        $this->withField('type', fn(Entity $entity) => $entity->getType());
        return $this;
    }

    /**
     * Include tags in the formatted data.
     */
    public function withTags(): self
    {
        $this->withField('tags', fn(Entity $entity) => $entity->tags);
        return $this;
    }

    /**
     * Enable the inclusion of related book and chapter titles in the response.
     */
    public function withRelatedTitles(): self
    {
        $this->includeRelatedTitles = true;

        $this->withField('book_title', function (Entity $entity) {
            if (method_exists($entity, 'book')) {
                return $entity->book?->name;
            }
            return null;
        });

        $this->withField('chapter_title', function (Entity $entity) {
            if ($entity instanceof Page && $entity->chapter_id) {
                return optional($entity->getAttribute('chapter'))->name;
            }
            return null;
        });

        return $this;
    }

    /**
     * Format the data and return an array of formatted content.
     * @return array[]
     */
    public function format(): array
    {
        if ($this->includeRelatedTitles) {
            $this->loadRelatedTitles();
        }

        $results = [];

        foreach ($this->list as $item) {
            $results[] = $this->formatSingle($item);
        }

        return $results;
    }

    /**
     * Eager load the related book and chapter data when needed.
     */
    protected function loadRelatedTitles(): void
    {
        $pages = collect($this->list)->filter(fn($item) => $item instanceof Page);

        foreach ($this->list as $entity) {
            if (method_exists($entity, 'book')) {
                $entity->load('book');
            }
            if ($entity instanceof Page && $entity->chapter_id) {
                $entity->load('chapter');
            }
        }
    }

    /**
     * Format a single entity item to a plain array.
     */
    protected function formatSingle(Entity $entity): array
    {
        $result = [];
        $values = (clone $entity)->toArray();

        foreach ($this->fields as $field => $callback) {
            if (is_string($callback)) {
                $field = $callback;
                if (!isset($values[$field])) {
                    continue;
                }
                $value = $values[$field];
            } else {
                $value = $callback($entity);
                if (is_null($value)) {
                    continue;
                }
            }

            $result[$field] = $value;
        }

        return $result;
    }
}
