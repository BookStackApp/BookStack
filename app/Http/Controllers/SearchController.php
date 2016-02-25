<?php

namespace BookStack\Http\Controllers;

use Illuminate\Http\Request;

use BookStack\Http\Requests;
use BookStack\Http\Controllers\Controller;
use BookStack\Repos\BookRepo;
use BookStack\Repos\ChapterRepo;
use BookStack\Repos\PageRepo;

class SearchController extends Controller
{
    protected $pageRepo;
    protected $bookRepo;
    protected $chapterRepo;

    /**
     * SearchController constructor.
     * @param $pageRepo
     * @param $bookRepo
     * @param $chapterRepo
     */
    public function __construct(PageRepo $pageRepo, BookRepo $bookRepo, ChapterRepo $chapterRepo)
    {
        $this->pageRepo = $pageRepo;
        $this->bookRepo = $bookRepo;
        $this->chapterRepo = $chapterRepo;
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
            'pages' => $pages,
            'books' => $books,
            'chapters' => $chapters,
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
            'entities' => $pages,
            'title' => 'Page Search Results',
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
            'entities' => $chapters,
            'title' => 'Chapter Search Results',
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
            'entities' => $books,
            'title' => 'Book Search Results',
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

}
