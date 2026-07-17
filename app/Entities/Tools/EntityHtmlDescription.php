<?php

namespace BookStack\Entities\Tools;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Util\HtmlContentFilter;
use BookStack\Util\HtmlContentFilterConfig;

class EntityHtmlDescription
{
    protected string $html = '';
    protected string $plain = '';

    public function __construct(
        protected Book|Chapter|Bookshelf $entity,
    ) {
        $this->html = $this->entity->description_html ?? '';
        $this->plain = $this->entity->description ?? '';
    }

    /**
     * Update the description from HTML code.
     * Optionally takes plaintext to use for the model also.
     */
    public function set(string $html, string|null $plaintext = null): void
    {
        $this->html = $html;
        $this->entity->description_html = $this->html;

        if ($plaintext !== null) {
            $this->plain = $plaintext;
            $this->entity->description = $this->plain;
        }

        if (empty($html) && !empty($plaintext)) {
            $this->html = $this->getHtml();
            $this->entity->description_html = $this->html;
        }
    }

    /**
     * Get the description as HTML.
     * Optionally returns the raw HTML if requested.
     */
    public function getHtml(bool $raw = false): string
    {
        $html = $this->html ?: '<p>' . nl2br(e($this->plain)) . '</p>';
        if ($raw) {
            return $html;
        }

        $isEmpty = empty(trim(strip_tags($html)));
        if ($isEmpty) {
            return '<p></p>';
        }

        $filter = new HtmlContentFilter(new HtmlContentFilterConfig());
        return $filter->filterString($html);
    }

    public function getPlain(): string
    {
        return $this->plain;
    }
}
