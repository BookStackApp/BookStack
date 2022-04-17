<?php

namespace BookStack\Entities\Tools;

use BookStack\Entities\Models\Page;
use BookStack\Entities\Repos\PageRepo;

class PageEditorData
{
    protected Page $page;
    protected PageRepo $pageRepo;

    protected array $viewData;
    protected array $warnings;

    public function __construct(Page $page, PageRepo $pageRepo)
    {
        $this->page = $page;
        $this->pageRepo = $pageRepo;
        $this->viewData = $this->build();
    }

    public function getViewData(): array
    {
        return $this->viewData;
    }

    public function getWarnings(): array
    {
        return $this->warnings;
    }

    protected function build(): array
    {
        $page = clone $this->page;
        $isDraft = boolval($this->page->draft);
        $templates = $this->pageRepo->getTemplates(10);
        $draftsEnabled = auth()->check();

        $isDraftRevision = false;
        $this->warnings = [];
        $editActivity = new PageEditActivity($page);

        if ($editActivity->hasActiveEditing()) {
            $this->warnings[] = $editActivity->activeEditingMessage();
        }

        // Check for a current draft version for this user
        $userDraft = $this->pageRepo->getUserDraft($page);
        if ($userDraft !== null) {
            $page->forceFill($userDraft->only(['name', 'html', 'markdown']));
            $isDraftRevision = true;
            $this->warnings[] = $editActivity->getEditingActiveDraftMessage($userDraft);
        }

        return [
            'page'            => $page,
            'book'            => $page->book,
            'isDraft'         => $isDraft,
            'isDraftRevision' => $isDraftRevision,
            'draftsEnabled'   => $draftsEnabled,
            'templates'       => $templates,
            'editor'          =>  setting('app-editor') === 'wysiwyg' ? 'wysiwyg' : 'markdown',
        ];
    }

}