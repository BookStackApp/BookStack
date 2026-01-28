<?php

namespace BookStack\Entities\Controllers;

use BookStack\Entities\EntityShareLinkService;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Tools\BookContents;
use BookStack\Http\Controller;
use Illuminate\Http\Request;

class SharedContentController extends Controller
{
    public function __construct(
        protected EntityShareLinkService $shareLinkService
    ) {
    }

    /**
     * Display shared content via share token.
     */
    public function show(Request $request, string $token)
    {
        $shareLink = $this->shareLinkService->findByToken($token);

        if (!$shareLink || !$shareLink->entity) {
            abort(404, trans('errors.share_link_not_found'));
        }

        $originalEntity = $shareLink->entity;
        $allowsNavigation = $originalEntity instanceof Book || $originalEntity instanceof Chapter;

        $entityType = $request->get('entity');
        $entityId = $request->get('id');

        if ($entityType && $entityId && $allowsNavigation && in_array($entityType, ['page', 'chapter'], true)) {
            if (!is_numeric($entityId)) {
                abort(404);
            }

            if ($originalEntity instanceof Book) {
                $entity = $this->findEntityInBook($entityType, (int)$entityId, $originalEntity);
            } elseif ($originalEntity instanceof Chapter) {
                $entity = $this->findEntityInChapter($entityType, (int)$entityId, $originalEntity);
            } else {
                $entity = null;
            }

            if ($entity) {
                $request->merge(['share_token' => $token]);
                $this->setPageTitle($entity->name);

                if ($entity instanceof Page) {
                    return $this->showPage($entity, $token, $shareLink);
                }
                if ($entity instanceof Chapter) {
                    return $this->showChapter($entity, $token, $shareLink);
                }
            }
        }

        $entity = $originalEntity;
        $request->merge(['share_token' => $token]);
        $this->setPageTitle($entity->name);

        if ($entity instanceof Page) {
            return $this->showPage($entity, $token, $shareLink);
        }

        if ($entity instanceof Chapter) {
            return $this->showChapter($entity, $token, $shareLink);
        }

        if ($entity instanceof Book) {
            return $this->showBook($entity, $token, $shareLink);
        }

        abort(404);
    }

    protected function findEntityInBook(string $entityType, int $entityId, Book $book): ?\BookStack\Entities\Models\Entity
    {
        if (!in_array($entityType, ['page', 'chapter'], true)) {
            return null;
        }

        if ($entityType === 'page') {
            return $book->pages()
                ->where('id', '=', $entityId)
                ->where('draft', '=', false)
                ->where('book_id', '=', $book->id)
                ->first();
        }

        if ($entityType === 'chapter') {
            return $book->chapters()
                ->where('id', '=', $entityId)
                ->where('book_id', '=', $book->id)
                ->first();
        }

        return null;
    }

    protected function findEntityInChapter(string $entityType, int $entityId, Chapter $chapter): ?\BookStack\Entities\Models\Entity
    {
        if ($entityType !== 'page') {
            return null;
        }

        return $chapter->pages()
            ->where('id', '=', $entityId)
            ->where('draft', '=', false)
            ->where('chapter_id', '=', $chapter->id)
            ->first();
    }

    protected function showPage(Page $page, string $token, $shareLink)
    {
        $book = $page->book;
        $page->load(['attachments', 'createdBy', 'updatedBy', 'ownedBy']);
        $pageContent = new \BookStack\Entities\Tools\PageContent($page);
        $page->html = $pageContent->render();
        $sidebarTree = $this->getBookContentForShare($book);

        return view('shared.page', [
            'page' => $page,
            'book' => $book,
            'sidebarTree' => $sidebarTree,
            'token' => $token,
            'shareLink' => $shareLink,
        ]);
    }

    protected function showChapter(Chapter $chapter, string $token, $shareLink)
    {
        $book = $chapter->book;
        $pages = $chapter->pages()->where('draft', '=', false)->orderBy('priority', 'asc')->get();
        $sidebarTree = $this->getBookContentForShare($book);
        $chapter->load(['createdBy', 'updatedBy', 'ownedBy']);

        return view('shared.chapter', [
            'chapter' => $chapter,
            'book' => $book,
            'pages' => $pages,
            'sidebarTree' => $sidebarTree,
            'token' => $token,
            'shareLink' => $shareLink,
        ]);
    }

    protected function showBook(Book $book, string $token, $shareLink)
    {
        $bookContent = $this->getBookContentForShare($book);
        $book->load(['createdBy', 'updatedBy', 'ownedBy']);

        return view('shared.book', [
            'book' => $book,
            'bookChildren' => $bookContent,
            'token' => $token,
            'shareLink' => $shareLink,
        ]);
    }

    protected function getBookContentForShare(Book $book): \Illuminate\Support\Collection
    {
        $pages = $book->pages()
            ->where('draft', '=', false)
            ->orderBy('priority', 'asc')
            ->get();

        $chapters = $book->chapters()
            ->orderBy('priority', 'asc')
            ->get();

        $chapterMap = $chapters->keyBy('id');
        $lonePages = collect();

        $pages->groupBy('chapter_id')->each(function ($pages, $chapter_id) use ($chapterMap, &$lonePages) {
            $chapter = $chapterMap->get($chapter_id);
            if ($chapter) {
                $chapter->setAttribute('visible_pages', collect($pages)->sortBy('priority'));
            } else {
                $lonePages = $lonePages->concat($pages);
            }
        });

        $chapters->whereNull('visible_pages')->each(function (Chapter $chapter) {
            $chapter->setAttribute('visible_pages', collect([]));
        });

        $all = collect()->concat($chapters)->concat($lonePages);
        $all->each(function ($entity) use ($book) {
            $entity->setRelation('book', $book);
        });

        return collect($chapters)->concat($lonePages)->sortBy(function ($entity) {
            return $entity->getAttribute('priority') ?? 0;
        });
    }
}
