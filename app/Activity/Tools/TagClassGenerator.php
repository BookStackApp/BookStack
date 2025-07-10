<?php

namespace BookStack\Activity\Tools;

use BookStack\Activity\Models\Tag;
use BookStack\Entities\Models\BookChild;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;

class TagClassGenerator
{
    public function __construct(
        protected Entity $entity
    ) {
    }

    /**
     * @return string[]
     */
    public function generate(): array
    {
        $classes = [];
        $tags = $this->entity->tags->all();

        foreach ($tags as $tag) {
             array_push($classes, ...$this->generateClassesForTag($tag));
        }

        if ($this->entity instanceof BookChild && userCan('view', $this->entity->book)) {
            $bookTags = $this->entity->book->tags;
            foreach ($bookTags as $bookTag) {
                 array_push($classes, ...$this->generateClassesForTag($bookTag, 'book-'));
            }
        }

        if ($this->entity instanceof Page && $this->entity->chapter && userCan('view', $this->entity->chapter)) {
            $chapterTags = $this->entity->chapter->tags;
            foreach ($chapterTags as $chapterTag) {
                 array_push($classes, ...$this->generateClassesForTag($chapterTag, 'chapter-'));
            }
        }

        return array_unique($classes);
    }

    public function generateAsString(): string
    {
        return implode(' ', $this->generate());
    }

    /**
     * @return string[]
     */
    protected function generateClassesForTag(Tag $tag, string $prefix = ''): array
    {
        $classes = [];
        $name = $this->normalizeTagClassString($tag->name);
        $value = $this->normalizeTagClassString($tag->value);
        $classes[] = "{$prefix}tag-name-{$name}";
        if ($value) {
            $classes[] = "{$prefix}tag-value-{$value}";
            $classes[] = "{$prefix}tag-pair-{$name}-{$value}";
        }
        return $classes;
    }

    protected function normalizeTagClassString(string $value): string
    {
        $value = str_replace(' ', '', strtolower($value));
        $value = str_replace('-', '', strtolower($value));

        return $value;
    }
}
