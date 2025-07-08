<?php

namespace BookStack\Entities\Repos;

use BookStack\Activity\ActivityType;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Models\PageRevision;
use BookStack\Entities\Models\Record;
use BookStack\Entities\Models\RecordChapter;
use BookStack\Entities\Models\RecordPage;
use BookStack\Entities\Queries\EntityQueries;
use BookStack\Entities\Tools\BookContents;
use BookStack\Entities\Tools\PageContent;
use BookStack\Entities\Tools\PageEditorType;
use BookStack\Entities\Tools\RecordContents;
use BookStack\Entities\Tools\RecordPageContent;
use BookStack\Entities\Tools\RecordPageEditorType;
use BookStack\Entities\Tools\TrashCan;
use BookStack\Exceptions\MoveOperationException;
use BookStack\Exceptions\PermissionsException;
use BookStack\Facades\Activity;
use BookStack\References\ReferenceStore;
use BookStack\References\ReferenceUpdater;
use Exception;

class RecordPageRepo
{
    public function __construct(
        protected BaseRepo $baseRepo,
        protected RecordRevisionRepo $revisionRepo,
        protected EntityQueries $entityQueries,
        protected ReferenceStore $referenceStore,
        protected ReferenceUpdater $referenceUpdater,
        protected TrashCan $trashCan,
    ) {}

    /**
     * Get a new draft page belonging to the given parent entity.
     */
    public function getNewDraftPage(Entity $parent)
    {
        $page = (new RecordPage())->forceFill([
            'name'       => trans('entities.pages_initial_name'),
            'created_by' => user()->id,
            'owned_by'   => user()->id,
            'updated_by' => user()->id,
            'draft'      => true,
            'editor'     => PageEditorType::getSystemDefault()->value,
        ]);

        if ($parent instanceof RecordChapter) {
            // dd($parent->toArray());
            $page->chapter_id = $parent->id;
            $page->record_id = $parent->record_id;
        } else {
            $page->record_id = $parent->id;
        }

        $defaultTemplate = $page->chapter->defaultTemplate ?? $page->record->defaultTemplate;
        // dd($page->toArray());
        if ($defaultTemplate && userCan('view', $defaultTemplate)) {
            $page->forceFill([
                'html'  => $defaultTemplate->html,
                'markdown' => $defaultTemplate->markdown,
            ]);
        }
        if($page['chapter_id']){
            $page['record_chapter_id'] = $page['chapter_id'];
            unset($page['chapter_id']);
        }
        // dd($page->toArray());
        
        $page->save();
        // dd($page->toArray());
        $page->refresh()->rebuildPermissions();
        // dd($page->toArray());

        return $page;
    }

    /**
     * Publish a draft page to make it a live, non-draft page.
     */
    public function publishDraft(RecordPage $draft, array $input): RecordPage
    {
        $draft->draft = false;
        $draft->revision_count = 1;
        $draft->priority = $this->getNewPriority($draft);
        $this->updateTemplateStatusAndContentFromInput($draft, $input);
        $this->baseRepo->update($draft, $input);

        $summary = trim($input['summary'] ?? '') ?: trans('entities.pages_initial_revision');
        $this->revisionRepo->storeNewForPage($draft, $summary);
        $draft->refresh();

        // dd($draft->toArray());
        // Activity::add(ActivityType::PAGE_CREATE, $draft);
        // dd($draft);
        $this->baseRepo->sortParent($draft);

        return $draft;
    }

    /**
     * Directly update the content for the given page from the provided input.
     * Used for direct content access in a way that performs required changes
     * (Search index & reference regen) without performing an official update.
     */
    public function setContentFromInput(RecordPage $page, array $input): void
    {
        $this->updateTemplateStatusAndContentFromInput($page, $input);
        $this->baseRepo->update($page, []);
    }

    /**
     * Update a page in the system.
     */
    public function update(RecordPage $page, array $input): RecordPage
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
        // dd($oldHtml, $oldName, $oldMarkdown);

        // Save a revision after updating
        $summary = trim($input['summary'] ?? '');
        $htmlChanged = isset($input['html']) && $input['html'] !== $oldHtml;
        $nameChanged = isset($input['name']) && $input['name'] !== $oldName;
        $markdownChanged = isset($input['markdown']) && $input['markdown'] !== $oldMarkdown;
        if ($htmlChanged || $nameChanged || $markdownChanged || $summary) {
            $this->revisionRepo->storeNewForPage($page, $summary);
        }

        // Activity::add(ActivityType::PAGE_UPDATE, $page);
        $this->baseRepo->sortParent($page);

        return $page;
    }

    protected function updateTemplateStatusAndContentFromInput(RecordPage $page, array $input): void
    {
        if (isset($input['template']) && userCan('templates-manage')) {
            $page->template = ($input['template'] === 'true');
        }

        $pageContent = new RecordPageContent($page);
        $defaultEditor = RecordPageEditorType::getSystemDefault();
        $currentEditor = RecordPageEditorType::forPage($page) ?: $defaultEditor;
        $inputEditor = RecordPageEditorType::fromRequestValue($input['editor'] ?? '') ?? $currentEditor;
        $newEditor = $currentEditor;

        $haveInput = isset($input['markdown']) || isset($input['html']);
        $inputEmpty = empty($input['markdown']) && empty($input['html']);

        if ($haveInput && $inputEmpty) {
            $pageContent->setNewHTML('', user());
        } elseif (!empty($input['markdown']) && is_string($input['markdown'])) {
            $newEditor = RecordPageEditorType::Markdown;
            $pageContent->setNewMarkdown($input['markdown'], user());
        } elseif (isset($input['html'])) {
            $newEditor = ($inputEditor->isHtmlBased() ? $inputEditor : null) ?? ($defaultEditor->isHtmlBased() ? $defaultEditor : null) ?? PageEditorType::WysiwygTinymce;
            $pageContent->setNewHTML($input['html'], user());
        }

        if (($newEditor !== $currentEditor || empty($page->editor)) && userCan('editor-change')) {
            $page->editor = $newEditor->value;
        } elseif (empty($page->editor)) {
            $page->editor = $defaultEditor->value;
        }
    }

    /**
     * Save a page update draft.
     */
    public function updatePageDraft(RecordPage $page, array $input)
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
    public function destroy(RecordPage $page)
    {
        $this->trashCan->softDestroyRecordPage($page);
        Activity::add(ActivityType::PAGE_DELETE, $page);
        $this->trashCan->autoClearOld();
    }

    /**
     * Restores a revision's content back into a page.
     */
    public function restoreRevision(RecordPage $page, int $revisionId): RecordPage
    {
        $oldUrl = $page->getUrl();
        $page->revision_count++;

        /** @var RecordPageRevision $revision */
        $revision = $page->revisions()->where('id', '=', $revisionId)->first();

        $page->fill($revision->toArray());
        $content = new RecordPageContent($page);

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

        $this->baseRepo->sortParent($page);

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
    public function move(RecordPage $page, string $parentIdentifier): Entity
    {
        $parent = $this->entityQueries->findVisibleByStringIdentifier($parentIdentifier);
        if (!$parent instanceof RecordChapter && !$parent instanceof Record) {
            throw new MoveOperationException('Record or chapter to move page into not found');
        }

        if (!userCan('page-create', $parent)) {
            throw new PermissionsException('User does not have permission to create a page within the new parent');
        }

        $page->chapter_id = ($parent instanceof RecordChapter) ? $parent->id : null;
        $newBookId = ($parent instanceof RecordChapter) ? $parent->book->id : $parent->id;
        $page->changeBook($newBookId);
        $page->rebuildPermissions();

        Activity::add(ActivityType::PAGE_MOVE, $page);

        $this->baseRepo->sortParent($page);

        return $parent;
    }

    /**
     * Get a new priority for a page.
     */
    protected function getNewPriority(RecordPage $page): int
    {
        // dd($page->toArray());
        $parent = RecordChapter::find($page->record_chapter_id);
        // dd($parent);
        if ($parent instanceof RecordChapter) {
            /** @var ?RecordPage $lastPage */
            $lastPage = $parent->pages('desc')->first();

            return $lastPage ? $lastPage->priority + 1 : 0;
        }

        return (new RecordContents($page->record))->getLastPriority() + 1;
    }
}
