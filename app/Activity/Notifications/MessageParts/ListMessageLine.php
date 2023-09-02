<?php

namespace BookStack\Activity\Notifications\MessageParts;

use Illuminate\Contracts\Support\Htmlable;
use Stringable;

/**
 * A bullet point list of content, where the keys of the given list array
 * are bolded header elements, and the values follow.
 */
class ListMessageLine implements Htmlable, Stringable
{
    public function __construct(
        protected array $list
    ) {
    }

    public function toHtml(): string
    {
        $list = [];
        foreach ($this->list as $header => $content) {
            $list[] = '<strong>' . e($header) . '</strong> ' . e($content);
        }
        return implode("<br>\n", $list);
    }

    public function __toString(): string
    {
        $list = [];
        foreach ($this->list as $header => $content) {
            $list[] = $header . ' ' . $content;
        }
        return implode("\n", $list);
    }
}
