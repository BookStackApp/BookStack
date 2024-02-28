<?php

namespace BookStack\Entities\Repos;

use BookStack\Activity\ActivityType;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Models\PageRevision;
use BookStack\Entities\Queries\EntityQueries;
use BookStack\Entities\Tools\BookContents;
use BookStack\Entities\Tools\PageContent;
use BookStack\Entities\Tools\PageEditorData;
use BookStack\Entities\Tools\TrashCan;
use BookStack\Exceptions\MoveOperationException;
use BookStack\Exceptions\PermissionsException;
use BookStack\Facades\Activity;
use BookStack\References\ReferenceStore;
use BookStack\References\ReferenceUpdater;
use Exception;

class PageRepo
{
    public function __construct(
        protected BaseRepo $baseRepo,
        protected RevisionRepo $revisionRepo,
        protected EntityQueries $entityQueries,
        protected ReferenceStore $referenceStore,
        protected ReferenceUpdater $referenceUpdater,
        protected TrashCan $trashCan,
    ) {
    }

    /**
     * Get a new draft page belonging to the given parent entity.
     */
    public function getNewDraftPage(Entity $parent)
    {
        $page = (new Page())->forceFill([
            'name'       => trans('entities.pages_initial_name'),
            'created_by' => user()->id,
            'owned_by'   => user()->id,
            'updated_by' => user()->id,
            'draft'      => true,
        ]);

        if ($parent instanceof Chapter) {
            $page->chapter_id = $parent->id;
            $page->book_id = $parent->book_id;
        } else {
            $page->book_id = $parent->id;
        }

        $defaultTemplate = $page->chapter->defaultTemplate ?? $page->book->defaultTemplate;
        if ($defaultTemplate && userCan('view', $defaultTemplate)) {
            $page->forceFill([
                'html'  => $defaultTemplate->html,
                'markdown' => $defaultTemplate->markdown,
            ]);
        }

        $page->save();
        $page->refresh()->rebuildPermissions();

        return $page;
    }

    /**
     * Publish a draft page to make it a live, non-draft page.
     */
    public function publishDraft(Page $draft, array $input): Page
    {
        $draft->draft = false;
        $draft->revision_count = 1;
        $draft->priority = $this->getNewPriority($draft);
        $this->updateTemplateStatusAndContentFromInput($draft, $input);
        $this->baseRepo->update($draft, $input);

        $this->revisionRepo->storeNewForPage($draft, trans('entities.pages_initial_revision'));
        $draft->refresh();

        Activity::add(ActivityType::PAGE_CREATE, $draft);

        return $draft;
    }

    /**
     * Update a page in the system.
     */
    public function update(Page $page, array $input): Page
    {
        // Hold the old details to compare later
        $oldHtml = $page->html;
        $oldName = $page->name;
        $oldMarkdown = $page->markdown;

        $this->updateTemplateStatusAndContentFromInput($page, $input);
        $this->baseRepo->update($page, $input);

        // Update with new details
        $page->revision_count++;
        $page->save();

        // Remove all update drafts for this user & page.
        $this->revisionRepo->deleteDraftsForCurrentUser($page);

        // Save a revision after updating
        $summary = trim($input['summary'] ?? '');
        $htmlChanged = isset($input['html']) && $input['html'] !== $oldHtml;
        $nameChanged = isset($input['name']) && $input['name'] !== $oldName;
        $markdownChanged = isset($input['markdown']) && $input['markdown'] !== $oldMarkdown;
        if ($htmlChanged || $nameChanged || $markdownChanged || $summary) {
            $this->revisionRepo->storeNewForPage($page, $summary);
        }

        Activity::add(ActivityType::PAGE_UPDATE, $page);

        return $page;
    }

    protected function updateTemplateStatusAndContentFromInput(Page $page, array $input)
    {
        if (isset($input['template']) && userCan('templates-manage')) {
            $page->template = ($input['template'] === 'true');
        }

        $pageContent = new PageContent($page);
        $currentEditor = $page->editor ?: PageEditorData::getSystemDefaultEditor();
        $newEditor = $currentEditor;

        $haveInput = isset($input['markdown']) || isset($input['html']);
        $inputEmpty = empty($input['markdown']) && empty($input['html']);

        if ($haveInput && $inputEmpty) {
            $pageContent->setNewHTML('', user());
        } elseif (!empty($input['markdown']) && is_string($input['markdown'])) {
            $newEditor = 'markdown';
            $pageContent->setNewMarkdown($input['markdown'], user());
        } elseif (isset($input['html'])) {
            $newEditor = 'wysiwyg';
            $pageContent->setNewHTML($input['html'], user());
        }

        if ($newEditor !== $currentEditor && userCan('editor-change')) {
            $page->editor = $newEditor;
        }
    }

    /**
     * Save a page update draft.
     */
    public function updatePageDraft(Page $page, array $input)
    {
        // If the page itself is a draft simply update that
        if ($page->draft) {
            $this->updateTemplateStatusAndContentFromInput($page, $input);
            $page->fill($input);
            $page->save();

            return $page;
        }

        // Otherwise, save the data to a revision
        $draft = $this->revisionRepo->getNewDraftForCurrentUser($page);
        $draft->fill($input);

        if (!empty($input['markdown'])) {
            $draft->markdown = $input['markdown'];
            $draft->html = '';
        } else {
            $draft->html = $input['html'];
            $draft->markdown = '';
        }

        $draft->save();

        return $draft;
    }

    /**
     * Destroy a page from the system.
     *
     * @throws Exception
     */
    public function destroy(Page $page)
    {
        $this->trashCan->softDestroyPage($page);
        Activity::add(ActivityType::PAGE_DELETE, $page);
        $this->trashCan->autoClearOld();
    }

    /**
     * Restores a revision's content back into a page.
     */
    public function restoreRevision(Page $page, int $revisionId): Page
    {
        $oldUrl = $page->getUrl();
        $page->revision_count++;

        /** @var PageRevision $revision */
        $revision = $page->revisions()->where('id', '=', $revisionId)->first();

        $page->fill($revision->toArray());
        $content = new PageContent($page);

        if (!empty($revision->markdown)) {
            $content->setNewMarkdown($revision->markdown, user());
        } else {
            $content->setNewHTML($revision->html, user());
        }

        $page->updated_by = user()->id;
        $page->refreshSlug();
        $page->save();
        $page->indexForSearch();
        $this->referenceStore->updateForEntity($page);

        $summary = trans('entities.pages_revision_restored_from', ['id' => strval($revisionId), 'summary' => $revision->summary]);
        $this->revisionRepo->storeNewForPage($page, $summary);

        if ($oldUrl !== $page->getUrl()) {
            $this->referenceUpdater->updateEntityReferences($page, $oldUrl);
        }

        Activity::add(ActivityType::PAGE_RESTORE, $page);
        Activity::add(ActivityType::REVISION_RESTORE, $revision);

        return $page;
    }

    /**
     * Move the given page into a new parent book or chapter.
     * The $parentIdentifier must be a string of the following format:
     * 'book:<id>' (book:5).
     *
     * @throws MoveOperationException
     * @throws PermissionsException
     */
    public function move(Page $page, string $parentIdentifier): Entity
    {
        $parent = $this->entityQueries->findVisibleByStringIdentifier($parentIdentifier);
        if (!$parent instanceof Chapter && !$parent instanceof Book) {
            throw new MoveOperationException('Book or chapter to move page into not found');
        }

        if (!userCan('page-create', $parent)) {
            throw new PermissionsException('User does not have permission to create a page within the new parent');
        }

        $page->chapter_id = ($parent instanceof Chapter) ? $parent->id : null;
        $newBookId = ($parent instanceof Chapter) ? $parent->book->id : $parent->id;
        $page->changeBook($newBookId);
        $page->rebuildPermissions();

        Activity::add(ActivityType::PAGE_MOVE, $page);

        return $parent;
    }

    /**
     * Get a new priority for a page.
     */
    protected function getNewPriority(Page $page): int
    {
        $parent = $page->getParent();
        if ($parent instanceof Chapter) {
            /** @var ?Page $lastPage */
            $lastPage = $parent->pages('desc')->first();

            return $lastPage ? $lastPage->priority + 1 : 0;
        }

        return (new BookContents($page->book))->getLastPriority() + 1;
    }
}
