<?php

namespace BookStack\Entities;

/**
 * Class BasicListItem.
 *
 * List Item that doesn't connect to a model
 */
class BasicListItem
{
    private $url;
    private $preview_content;
    private $typeName = 0;

    public $name;
    public $id = 0;
    // public $preview_content = null;

    /**
     * @var Bookshelf
     */
    // public $bookshelf;

    public function __construct(string $url, string $name, ?string $preview_content, string $typeName)
    {
        $this->name = $name;
        $this->url = $url;
        $this->preview_content = $preview_content;
        $this->typeName = $typeName;
    }

    /**
     * Get the url for this bookshelf.
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Get the entity type as a simple lowercase word.
     */
    // public static function getType(): string
    public function getType(): string
    {
        // $className = array_slice(explode('\\', static::class), -1, 1)[0];

        // return strtolower($className);
        return $this->typeName;
    }

    /**
     * Get an excerpt of this entity's descriptive content to the specified length.
     */
    public function getExcerpt(int $length = 100): string
    {
        $text = $this->preview_content ?? '';

        if (mb_strlen($text) > $length) {
            $text = mb_substr($text, 0, $length - 3).'...';
        }

        return trim($text);
    }
}
