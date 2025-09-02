<?php

namespace BookStack\Entities\Models;

interface HtmlDescriptionInterface
{
    /**
     * Get the HTML-based description for this item.
     * By default, the content should be sanitised unless raw is set to true.
     */
    public function descriptionHtml(bool $raw = false): string;

    /**
     * Set the HTML-based description for this item.
     */
    public function setDescriptionHtml(string $html, string|null $plaintext = null): void;
}
