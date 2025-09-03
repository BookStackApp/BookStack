<?php

namespace BookStack\Entities\Models;

use BookStack\Util\HtmlContentFilter;

/**
 * @property string $description
 * @property string $description_html
 */
trait HtmlDescriptionTrait
{
    public function descriptionHtml(bool $raw = false): string
    {
        $html = $this->description_html ?: '<p>' . nl2br(e($this->description)) . '</p>';
        if ($raw) {
            return $html;
        }

        return HtmlContentFilter::removeScriptsFromHtmlString($html);
    }

    public function setDescriptionHtml(string $html, string|null $plaintext = null): void
    {
        $this->description_html = $html;

        if ($plaintext !== null) {
            $this->description = $plaintext;
        }

        if (empty($html) && !empty($plaintext)) {
            $this->description_html = $this->descriptionHtml();
        }
    }
}
