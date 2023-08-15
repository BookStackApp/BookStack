<?php

namespace BookStack\Activity\Notifications\MessageParts;

use Illuminate\Contracts\Support\Htmlable;

/**
 * A bullet point list of content, where the keys of the given list array
 * are bolded header elements, and the values follow.
 */
class ListMessageLine implements Htmlable
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
}
