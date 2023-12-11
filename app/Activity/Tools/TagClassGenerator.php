<?php

namespace BookStack\Activity\Tools;

use BookStack\Activity\Models\Tag;

class TagClassGenerator
{
    protected array $tags;

    /**
     * @param Tag[] $tags
     */
    public function __construct(array $tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return string[]
     */
    public function generate(): array
    {
        $classes = [];

        foreach ($this->tags as $tag) {
            $name = $this->normalizeTagClassString($tag->name);
            $value = $this->normalizeTagClassString($tag->value);
            $classes[] = 'tag-name-' . $name;
            if ($value) {
                $classes[] = 'tag-value-' . $value;
                $classes[] = 'tag-pair-' . $name . '-' . $value;
            }
        }

        return array_unique($classes);
    }

    public function generateAsString(): string
    {
        return implode(' ', $this->generate());
    }

    protected function normalizeTagClassString(string $value): string
    {
        $value = str_replace(' ', '', strtolower($value));
        $value = str_replace('-', '', strtolower($value));

        return $value;
    }
}
