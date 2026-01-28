<?php

namespace BookStack\Entities\Controllers;

use BookStack\Entities\EntityShareLinkService;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\EntityShareLink;
use BookStack\Entities\Queries\EntityQueries;
use BookStack\Exceptions\PermissionsException;
use BookStack\Http\Controller;
use BookStack\Permissions\Permission;
use Illuminate\Http\Request;

class EntityShareLinkController extends Controller
{
    public function __construct(
        protected EntityShareLinkService $shareLinkService,
        protected EntityQueries $queries
    ) {
    }

    /**
     * Display the share links for a specific book.
     */
    public function indexForBook(string $bookSlug)
    {
        $book = $this->queries->books->findVisibleBySlugOrFail($bookSlug);
        return $this->index($book);
    }

    /**
     * Display the share links for a specific chapter.
     */
    public function indexForChapter(string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->queries->chapters->findVisibleBySlugsOrFail($bookSlug, $chapterSlug);
        return $this->index($chapter);
    }

    /**
     * Display the share links for a specific page.
     */
    public function indexForPage(string $bookSlug, string $pageSlug)
    {
        $page = $this->queries->pages->findVisibleBySlugsOrFail($bookSlug, $pageSlug);
        return $this->index($page);
    }

    /**
     * Display the share links for the given entity.
     */
    protected function index(Entity $entity)
    {
        $this->checkPermission(Permission::ContentShareManage);

        $shareLinks = $this->shareLinkService->getShareLinksForEntity($entity);

        return view('entities.share-links.index', [
            'entity' => $entity,
            'shareLinks' => $shareLinks,
        ]);
    }

    /**
     * Store a new share link for a book.
     */
    public function storeForBook(Request $request, string $bookSlug)
    {
        $book = $this->queries->books->findVisibleBySlugOrFail($bookSlug);
        return $this->store($request, $book);
    }

    /**
     * Store a new share link for a chapter.
     */
    public function storeForChapter(Request $request, string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->queries->chapters->findVisibleBySlugsOrFail($bookSlug, $chapterSlug);
        return $this->store($request, $chapter);
    }

    /**
     * Store a new share link for a page.
     */
    public function storeForPage(Request $request, string $bookSlug, string $pageSlug)
    {
        $page = $this->queries->pages->findVisibleBySlugsOrFail($bookSlug, $pageSlug);
        return $this->store($request, $page);
    }

    /**
     * Store a new share link for the given entity.
     */
    protected function store(Request $request, Entity $entity)
    {
        $this->checkPermission(Permission::ContentShareManage);

        $this->validate($request, [
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $this->shareLinkService->createShareLink($entity, $request->get('name'));
        $this->showSuccessNotification(trans('entities.share_link_created'));

        return redirect($entity->getUrl('/share-links'));
    }

    /**
     * Delete a share link.
     */
    public function destroy(int $id)
    {
        $shareLink = EntityShareLink::query()->findOrFail($id);

        try {
            $this->shareLinkService->deleteShareLink($shareLink);
            $this->showSuccessNotification(trans('entities.share_link_deleted'));
        } catch (PermissionsException $e) {
            $this->showErrorNotification($e->getMessage());
        }

        return redirect()->back();
    }
}
