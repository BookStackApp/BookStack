<?php

namespace BookStack\Entities\Tools;

use BookStack\Activity\Tools\CommentTree;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Queries\EntityQueries;
use BookStack\Entities\Tools\Markdown\HtmlToMarkdown;
use BookStack\Entities\Tools\Markdown\MarkdownToHtml;

class PageEditorData
{
    protected array $viewData;
    protected array $warnings;

    public function __construct(
        protected Page $page,
        protected EntityQueries $queries,
        protected string $requestedEditor
    ) {
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
        $templates = $this->queries->pages->visibleTemplates()
            ->orderBy('name', 'asc')
            ->take(10)
            ->paginate()
            ->withPath('/templates');

        $draftsEnabled = auth()->check();

        $isDraftRevision = false;
        $this->warnings = [];
        $editActivity = new PageEditActivity($page);

        if ($editActivity->hasActiveEditing()) {
            $this->warnings[] = $editActivity->activeEditingMessage();
        }

        // Check for a current draft version for this user
        $userDraft = $this->queries->revisions->findLatestCurrentUserDraftsForPageId($page->id);
        if (!is_null($userDraft)) {
            $page->forceFill($userDraft->only(['name', 'html', 'markdown']));
            $isDraftRevision = true;
            $this->warnings[] = $editActivity->getEditingActiveDraftMessage($userDraft);
        }

        $editorType = $this->getEditorType($page);
        $this->updateContentForEditor($page, $editorType);

        return [
            'page'            => $page,
            'book'            => $page->book,
            'isDraft'         => $isDraft,
            'isDraftRevision' => $isDraftRevision,
            'draftsEnabled'   => $draftsEnabled,
            'templates'       => $templates,
            'editor'          => $editorType,
            'comments'        => new CommentTree($page),
        ];
    }

    protected function updateContentForEditor(Page $page, PageEditorType $editorType): void
    {
        $isHtml = !empty($page->html) && empty($page->markdown);

        // HTML to markdown-clean conversion
        if ($editorType === PageEditorType::Markdown && $isHtml && $this->requestedEditor === 'markdown-clean') {
            $page->markdown = (new HtmlToMarkdown($page->html))->convert();
        }

        // Markdown to HTML conversion if we don't have HTML
        if ($editorType->isHtmlBased() && !$isHtml) {
            $page->html = (new MarkdownToHtml($page->markdown))->convert();
        }
    }

    /**
     * Get the type of editor to show for editing the given page.
     * Defaults based upon the current content of the page otherwise will fall back
     * to system default but will take a requested type (if provided) if permissions allow.
     */
    protected function getEditorType(Page $page): PageEditorType
    {
        $editorType = PageEditorType::forPage($page) ?: PageEditorType::getSystemDefault();

        // Use requested editor if valid and if we have permission
        $requestedType = PageEditorType::fromRequestValue($this->requestedEditor);
        if ($requestedType && userCan('editor-change')) {
            $editorType = $requestedType;
        }

        return $editorType;
    }
}
