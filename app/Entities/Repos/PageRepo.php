<?php

namespace BookStack\Entities\Repos;

use BookStack\Actions\ActivityType;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Models\PageRevision;
use BookStack\Entities\Tools\BookContents;
use BookStack\Entities\Tools\PageContent;
use BookStack\Entities\Tools\TrashCan;
use BookStack\Exceptions\MoveOperationException;
use BookStack\Exceptions\NotFoundException;
use BookStack\Exceptions\PermissionsException;
use BookStack\Facades\Activity;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class PageRepo
{
    protected $baseRepo;

    /**
     * PageRepo constructor.
     */
    public function __construct(BaseRepo $baseRepo)
    {
        $this->baseRepo = $baseRepo;
    }

    /**
     * Get a page by ID.
     *
     * @throws NotFoundException
     */
    public function getById(int $id, array $relations = ['book']): Page
    {
        $page = Page::visible()->with($relations)->find($id);

        if (!$page) {
            throw new NotFoundException(trans('errors.page_not_found'));
        }

        return $page;
    }

    /**
     * Get a page its book and own slug.
     *
     * @throws NotFoundException
     */
    public function getBySlug(string $bookSlug, string $pageSlug): Page
    {
        $page = Page::visible()->whereSlugs($bookSlug, $pageSlug)->first();

        if (!$page) {
            throw new NotFoundException(trans('errors.page_not_found'));
        }

        return $page;
    }

    /**
     * Get a page by its old slug but checking the revisions table
     * for the last revision that matched the given page and book slug.
     */
    public function getByOldSlug(string $bookSlug, string $pageSlug): ?Page
    {
        /** @var ?PageRevision $revision */
        $revision = PageRevision::query()
            ->whereHas('page', function (Builder $query) {
                $query->scopes('visible');
            })
            ->where('slug', '=', $pageSlug)
            ->where('type', '=', 'version')
            ->where('book_slug', '=', $bookSlug)
            ->orderBy('created_at', 'desc')
            ->with('page')
            ->first();

        return $revision->page ?? null;
    }

    /**
     * Get pages that have been marked as a template.
     */
    public function getTemplates(int $count = 10, int $page = 1, string $search = ''): LengthAwarePaginator
    {
        $query = Page::visible()
            ->where('template', '=', true)
            ->orderBy('name', 'asc')
            ->skip(($page - 1) * $count)
            ->take($count);

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $paginator = $query->paginate($count, ['*'], 'page', $page);
        $paginator->withPath('/templates');

        return $paginator;
    }

    /**
     * Get a parent item via slugs.
     */
    public function getParentFromSlugs(string $bookSlug, string $chapterSlug = null): Entity
    {
        if ($chapterSlug !== null) {
            return $chapter = Chapter::visible()->whereSlugs($bookSlug, $chapterSlug)->firstOrFail();
        }

        return Book::visible()->where('slug', '=', $bookSlug)->firstOrFail();
    }

    /**
     * Get the draft copy of the given page for the current user.
     */
    public function getUserDraft(Page $page): ?PageRevision
    {
        $revision = $this->getUserDraftQuery($page)->first();

        return $revision;
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

        $page->save();
        $page->refresh()->rebuildPermissions();

        return $page;
    }

    /**
     * Publish a draft page to make it a live, non-draft page.
     */
    public function publishDraft(Page $draft, array $input): Page
    {
        $this->updateTemplateStatusAndContentFromInput($draft, $input);
        $this->baseRepo->update($draft, $input);

        $draft->draft = false;
        $draft->revision_count = 1;
        $draft->priority = $this->getNewPriority($draft);
        $draft->refreshSlug();
        $draft->save();

        $this->savePageRevision($draft, trans('entities.pages_initial_revision'));
        $draft->indexForSearch();
        $draft->refresh();

        Activity::addForEntity($draft, ActivityType::PAGE_CREATE);

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
        $this->getUserDraftQuery($page)->delete();

        // Save a revision after updating
        $summary = trim($input['summary'] ?? '');
        $htmlChanged = isset($input['html']) && $input['html'] !== $oldHtml;
        $nameChanged = isset($input['name']) && $input['name'] !== $oldName;
        $markdownChanged = isset($input['markdown']) && $input['markdown'] !== $oldMarkdown;
        if ($htmlChanged || $nameChanged || $markdownChanged || $summary) {
            $this->savePageRevision($page, $summary);
        }

        Activity::addForEntity($page, ActivityType::PAGE_UPDATE);

        return $page;
    }

    protected function updateTemplateStatusAndContentFromInput(Page $page, array $input)
    {
        if (isset($input['template']) && userCan('templates-manage')) {
            $page->template = ($input['template'] === 'true');
        }

        $pageContent = new PageContent($page);
        if (!empty($input['markdown'] ?? '')) {
            $pageContent->setNewMarkdown($input['markdown']);
        } elseif (isset($input['html'])) {
            $pageContent->setNewHTML($input['html']);
        }
    }

    /**
     * Saves a page revision into the system.
     */
    protected function savePageRevision(Page $page, string $summary = null): PageRevision
    {
        $revision = new PageRevision($page->getAttributes());

        $revision->page_id = $page->id;
        $revision->slug = $page->slug;
        $revision->book_slug = $page->book->slug;
        $revision->created_by = user()->id;
        $revision->created_at = $page->updated_at;
        $revision->type = 'version';
        $revision->summary = $summary;
        $revision->revision_number = $page->revision_count;
        $revision->save();

        $this->deleteOldRevisions($page);

        return $revision;
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

        // Otherwise save the data to a revision
        $draft = $this->getPageRevisionToUpdate($page);
        $draft->fill($input);
        if (setting('app-editor') !== 'markdown') {
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
        $trashCan = new TrashCan();
        $trashCan->softDestroyPage($page);
        Activity::addForEntity($page, ActivityType::PAGE_DELETE);
        $trashCan->autoClearOld();
    }

    /**
     * Restores a revision's content back into a page.
     */
    public function restoreRevision(Page $page, int $revisionId): Page
    {
        $page->revision_count++;

        /** @var PageRevision $revision */
        $revision = $page->revisions()->where('id', '=', $revisionId)->first();

        $page->fill($revision->toArray());
        $content = new PageContent($page);

        if (!empty($revision->markdown)) {
            $content->setNewMarkdown($revision->markdown);
        } else {
            $content->setNewHTML($revision->html);
        }

        $page->updated_by = user()->id;
        $page->refreshSlug();
        $page->save();
        $page->indexForSearch();

        $summary = trans('entities.pages_revision_restored_from', ['id' => strval($revisionId), 'summary' => $revision->summary]);
        $this->savePageRevision($page, $summary);

        Activity::addForEntity($page, ActivityType::PAGE_RESTORE);

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
        $parent = $this->findParentByIdentifier($parentIdentifier);
        if ($parent === null) {
            throw new MoveOperationException('Book or chapter to move page into not found');
        }

        if (!userCan('page-create', $parent)) {
            throw new PermissionsException('User does not have permission to create a page within the new parent');
        }

        $page->chapter_id = ($parent instanceof Chapter) ? $parent->id : null;
        $newBookId = ($parent instanceof Chapter) ? $parent->book->id : $parent->id;
        $page->changeBook($newBookId);
        $page->rebuildPermissions();

        Activity::addForEntity($page, ActivityType::PAGE_MOVE);

        return $parent;
    }

    /**
     * Copy an existing page in the system.
     * Optionally providing a new parent via string identifier and a new name.
     *
     * @throws MoveOperationException
     * @throws PermissionsException
     */
    public function copy(Page $page, string $parentIdentifier = null, string $newName = null): Page
    {
        $parent = $parentIdentifier ? $this->findParentByIdentifier($parentIdentifier) : $page->getParent();
        if ($parent === null) {
            throw new MoveOperationException('Book or chapter to move page into not found');
        }

        if (!userCan('page-create', $parent)) {
            throw new PermissionsException('User does not have permission to create a page within the new parent');
        }

        $copyPage = $this->getNewDraftPage($parent);
        $pageData = $page->getAttributes();

        // Update name
        if (!empty($newName)) {
            $pageData['name'] = $newName;
        }

        // Copy tags from previous page if set
        if ($page->tags) {
            $pageData['tags'] = [];
            foreach ($page->tags as $tag) {
                $pageData['tags'][] = ['name' => $tag->name, 'value' => $tag->value];
            }
        }

        return $this->publishDraft($copyPage, $pageData);
    }

    /**
     * Find a page parent entity via a identifier string in the format:
     * {type}:{id}
     * Example: (book:5).
     *
     * @throws MoveOperationException
     */
    protected function findParentByIdentifier(string $identifier): ?Entity
    {
        $stringExploded = explode(':', $identifier);
        $entityType = $stringExploded[0];
        $entityId = intval($stringExploded[1]);

        if ($entityType !== 'book' && $entityType !== 'chapter') {
            throw new MoveOperationException('Pages can only be in books or chapters');
        }

        $parentClass = $entityType === 'book' ? Book::class : Chapter::class;

        return $parentClass::visible()->where('id', '=', $entityId)->first();
    }

    /**
     * Change the page's parent to the given entity.
     */
    protected function changeParent(Page $page, Entity $parent)
    {
        $book = ($parent instanceof Chapter) ? $parent->book : $parent;
        $page->chapter_id = ($parent instanceof Chapter) ? $parent->id : 0;
        $page->save();

        if ($page->book->id !== $book->id) {
            $page->changeBook($book->id);
        }

        $page->load('book');
        $book->rebuildPermissions();
    }

    /**
     * Get a page revision to update for the given page.
     * Checks for an existing revisions before providing a fresh one.
     */
    protected function getPageRevisionToUpdate(Page $page): PageRevision
    {
        $drafts = $this->getUserDraftQuery($page)->get();
        if ($drafts->count() > 0) {
            return $drafts->first();
        }

        $draft = new PageRevision();
        $draft->page_id = $page->id;
        $draft->slug = $page->slug;
        $draft->book_slug = $page->book->slug;
        $draft->created_by = user()->id;
        $draft->type = 'update_draft';

        return $draft;
    }

    /**
     * Delete old revisions, for the given page, from the system.
     */
    protected function deleteOldRevisions(Page $page)
    {
        $revisionLimit = config('app.revision_limit');
        if ($revisionLimit === false) {
            return;
        }

        $revisionsToDelete = PageRevision::query()
            ->where('page_id', '=', $page->id)
            ->orderBy('created_at', 'desc')
            ->skip(intval($revisionLimit))
            ->take(10)
            ->get(['id']);
        if ($revisionsToDelete->count() > 0) {
            PageRevision::query()->whereIn('id', $revisionsToDelete->pluck('id'))->delete();
        }
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

    /**
     * Get the query to find the user's draft copies of the given page.
     */
    protected function getUserDraftQuery(Page $page)
    {
        return PageRevision::query()->where('created_by', '=', user()->id)
            ->where('type', 'update_draft')
            ->where('page_id', '=', $page->id)
            ->orderBy('created_at', 'desc');
    }
}
