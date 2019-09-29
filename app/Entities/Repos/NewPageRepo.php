<?php namespace BookStack\Entities\Repos;

use BookStack\Entities\Book;
use BookStack\Entities\Chapter;
use BookStack\Entities\Entity;
use BookStack\Entities\Managers\BookContents;
use BookStack\Entities\Managers\PageContent;
use BookStack\Entities\Page;
use BookStack\Entities\PageRevision;
use BookStack\Exceptions\NotFoundException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class NewPageRepo
{

    protected $baseRepo;

    /**
     * NewPageRepo constructor.
     */
    public function __construct(BaseRepo $baseRepo)
    {
        $this->baseRepo = $baseRepo;
    }

    /**
     * Get a page by ID.
     * @throws NotFoundException
     */
    public function getById(int $id): Page
    {
        $page = Page::visible()->with(['book', 'parent'])->find($id);

        if (!$page) {
            throw new NotFoundException(trans('errors.page_not_found'));
        }

        return $page;
    }

    /**
     * Get a page its book and own slug.
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
        $revision = PageRevision::query()
            ->whereHas('page', function(Builder $query) {
                $query->visible();
            })
            ->where('slug', '=', $pageSlug)
            ->where('type', '=', 'version')
            ->where('book_slug', '=', $bookSlug)
            ->orderBy('created_at', 'desc')
            ->with('page')
            ->first();
        return $revision ? $revision->page : null;
    }

    /**
     * Get pages that have been marked as a template.
     */
    public function getTemplates(int $count = 10, int $page = 1, string $search = ''): LengthAwarePaginator
    {
        $query = $this->entityQuery('page')
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
        PageRevision::query()->where('created_by', '=', user()->id)
            ->where('type', 'update_draft')
            ->where('page_id', '=', $page->id)
            ->orderBy('created_at', 'desc')->first();
    }

    /**
     * Get a new draft page belonging to the given parent entity.
     */
    public function getNewDraftPage(Entity $parent)
    {
        $page = (new Page())->forceFill([
            'name' => trans('entities.pages_initial_name'),
            'created_by' => user()->id,
            'updated_by' => user()->id,
            'draft' => true,
        ]);

        if ($parent instanceof Chapter) {
            $page->chapter_id = $parent->id;
            $page->book_id = $parent->book_id;
        } else {
            $page->book_id = $parent->id;
        }

        $page->refresh()->rebuildPermissions();
        return $page;
    }

    /**
     * Publish a draft page to make it a live, non-draft page.
     */
    public function publishDraft(Page $draft, array $input): Page
    {
        $this->baseRepo->update($draft, $input);
        if (isset($input['template']) && userCan('templates-manage')) {
            $draft->template = ($input['template'] === 'true');
        }

        $pageContent = new PageContent($draft);
        $pageContent->setNewHTML($input['html']);
        $draft->draft = false;
        $draft->revision_count = 1;
        $draft->priority = $this->getNewPriority($draft);
        $draft->refreshSlug();
        $draft->save();

        $this->savePageRevision($draft, trans('entities.pages_initial_revision'));
        $draft->indexForSearch();
        return $draft->refresh();
    }

    /**
     * Get a new priority for a page
     */
    protected function getNewPriority(Page $page): int
    {
        if ($page->parent instanceof Chapter) {
            $lastPage = $page->parent->pages('desc')->first();
            return $lastPage ? $lastPage->priority + 1 : 0;
        }

        return (new BookContents($book))->getLastPriority() + 1;
    }

}