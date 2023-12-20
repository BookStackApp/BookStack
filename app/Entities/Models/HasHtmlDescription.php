<?php

namespace BookStack\Entities\Models;

use BookStack\Util\HtmlContentFilter;

/**
 * @property string $description
 * @property string $description_html
 */
trait HasHtmlDescription
{
    /**
     * Get the HTML description for this book.
     */
    public function descriptionHtml(): string
    {
        $html = $this->description_html ?: '<p>' . nl2br(e($this->description)) . '</p>';
        return HtmlContentFilter::removeScriptsFromHtmlString($html);
    }
}
