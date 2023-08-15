<?php

namespace BookStack\Activity\Notifications\MessageParts;

use Illuminate\Contracts\Support\Htmlable;

/**
 * A line of text with linked text included, intended for use
 * in MailMessages. The line should have a ':link' placeholder for
 * where the link should be inserted within the line.
 */
class LinkedMailMessageLine implements Htmlable
{
    public function __construct(
        protected string $url,
        protected string $line,
        protected string $linkText,
    ) {
    }

    public function toHtml(): string
    {
        $link = '<a href="' . e($this->url) . '">' . e($this->linkText) . '</a>';
        return str_replace(':link', $link, e($this->line));
    }
}
