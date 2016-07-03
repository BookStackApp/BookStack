<?php

namespace BookStack\Http\Controllers;

use BookStack\Services\ViewService;
use Illuminate\Http\Request;

use BookStack\Http\Requests;
use BookStack\Repos\BookRepo;
use BookStack\Repos\ChapterRepo;
use BookStack\Repos\PageRepo;

class SearchController extends Controller
{
    protected $pageRepo;
    protected $bookRepo;
    protected $chapterRepo;
    protected $viewService;

    /**
     * SearchController constructor.
     * @param PageRepo $pageRepo
     * @param BookRepo $bookRepo
     * @param ChapterRepo $chapterRepo
     * @param ViewService $viewService
     */
    public function __construct(PageRepo $pageRepo, BookRepo $bookRepo, ChapterRepo $chapterRepo, ViewService $viewService)
    {
        $this->pageRepo = $pageRepo;
        $this->bookRepo = $bookRepo;
        $this->chapterRepo = $chapterRepo;
        $this->viewService = $viewService;
        parent::__construct();
    }

    /**
     * Searches all entities.
     * @param Request $request
     * @return \Illuminate\View\View
     * @internal param string $searchTerm
     */
    public function searchAll(Request $request)
    {
        if (!$request->has('term')) {
            return redirect()->back();
        }
        $searchTerm = $request->get('term');
        $paginationAppends = $request->only('term');
        $pages = $this->pageRepo->getBySearch($searchTerm, [], 20, $paginationAppends);
        $books = $this->bookRepo->getBySearch($searchTerm, 10, $paginationAppends);
        $chapters = $this->chapterRepo->getBySearch($searchTerm, [], 10, $paginationAppends);
        $this->setPageTitle('Search For ' . $searchTerm);
        return view('search/all', [
            'pages'      => $pages,
            'books'      => $books,
            'chapters'   => $chapters,
            'searchTerm' => $searchTerm
        ]);
    }

    /**
     * Search only the pages in the system.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function searchPages(Request $request)
    {
        if (!$request->has('term')) return redirect()->back();

        $searchTerm = $request->get('term');
        $paginationAppends = $request->only('term');
        $pages = $this->pageRepo->getBySearch($searchTerm, [], 20, $paginationAppends);
        $this->setPageTitle('Page Search For ' . $searchTerm);
        return view('search/entity-search-list', [
            'entities'   => $pages,
            'title'      => 'Page Search Results',
            'searchTerm' => $searchTerm
        ]);
    }

    /**
     * Search only the chapters in the system.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function searchChapters(Request $request)
    {
        if (!$request->has('term')) return redirect()->back();

        $searchTerm = $request->get('term');
        $paginationAppends = $request->only('term');
        $chapters = $this->chapterRepo->getBySearch($searchTerm, [], 20, $paginationAppends);
        $this->setPageTitle('Chapter Search For ' . $searchTerm);
        return view('search/entity-search-list', [
            'entities'   => $chapters,
            'title'      => 'Chapter Search Results',
            'searchTerm' => $searchTerm
        ]);
    }

    /**
     * Search only the books in the system.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function searchBooks(Request $request)
    {
        if (!$request->has('term')) return redirect()->back();

        $searchTerm = $request->get('term');
        $paginationAppends = $request->only('term');
        $books = $this->bookRepo->getBySearch($searchTerm, 20, $paginationAppends);
        $this->setPageTitle('Book Search For ' . $searchTerm);
        return view('search/entity-search-list', [
            'entities'   => $books,
            'title'      => 'Book Search Results',
            'searchTerm' => $searchTerm
        ]);
    }

    /**
     * Searches all entities within a book.
     * @param Request $request
     * @param integer $bookId
     * @return \Illuminate\View\View
     * @internal param string $searchTerm
     */
    public function searchBook(Request $request, $bookId)
    {
        if (!$request->has('term')) {
            return redirect()->back();
        }
        $searchTerm = $request->get('term');
        $searchWhereTerms = [['book_id', '=', $bookId]];
        $pages = $this->pageRepo->getBySearch($searchTerm, $searchWhereTerms);
        $chapters = $this->chapterRepo->getBySearch($searchTerm, $searchWhereTerms);
        return view('search/book', ['pages' => $pages, 'chapters' => $chapters, 'searchTerm' => $searchTerm]);
    }


    /**
     * Search for a list of entities and return a partial HTML response of matching entities.
     * Returns the most popular entities if no search is provided.
     * @param Request $request
     * @return mixed
     */
    public function searchEntitiesAjax(Request $request)
    {
        $entities = collect();
        $entityTypes = $request->has('types') ? collect(explode(',', $request->get('types'))) : collect(['page', 'chapter', 'book']);
        $searchTerm = ($request->has('term') && trim($request->get('term')) !== '') ? $request->get('term') : false;

        // Search for entities otherwise show most popular
        if ($searchTerm !== false) {
            if ($entityTypes->contains('page')) $entities = $entities->merge($this->pageRepo->getBySearch($searchTerm)->items());
            if ($entityTypes->contains('chapter')) $entities = $entities->merge($this->chapterRepo->getBySearch($searchTerm)->items());
            if ($entityTypes->contains('book')) $entities = $entities->merge($this->bookRepo->getBySearch($searchTerm)->items());
            $entities = $entities->sortByDesc('title_relevance');
        } else {
            $entityNames = $entityTypes->map(function ($type) {
                return 'BookStack\\' . ucfirst($type);
            })->toArray();
            $entities = $this->viewService->getPopular(20, 0, $entityNames);
        }

        return view('search/entity-ajax-list', ['entities' => $entities]);
    }

}


