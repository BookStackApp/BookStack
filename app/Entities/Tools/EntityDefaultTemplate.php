<?php

namespace BookStack\Entities\Tools;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Queries\PageQueries;

class EntityDefaultTemplate
{
    public function __construct(
        protected Book|Chapter $entity,
    ) {
    }

    /**
     * Set the default template ID for this entity.
     */
    public function setFromId(int $templateId): void
    {
        $changing = $templateId !== intval($this->entity->default_template_id);
        if (!$changing) {
            return;
        }

        if ($templateId === 0) {
            $this->entity->default_template_id = null;
            return;
        }

        $pageQueries = app()->make(PageQueries::class);
        $templateExists = $pageQueries->visibleTemplates()
            ->where('id', '=', $templateId)
            ->exists();

        $this->entity->default_template_id = $templateExists ? $templateId : null;
    }

    /**
     * Get the default template for this entity (if visible).
     */
    public function get(): Page|null
    {
        if (!$this->entity->default_template_id) {
            return null;
        }

        $pageQueries = app()->make(PageQueries::class);
        $page = $pageQueries->visibleTemplates(true)
            ->where('id', '=', $this->entity->default_template_id)
            ->first();

        if ($page instanceof Page) {
            return $page;
        }

        return null;
    }
}
