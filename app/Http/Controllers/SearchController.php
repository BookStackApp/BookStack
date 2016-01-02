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
        $pages = $this->pageRepo->getBySearch($searchTerm);
        $books = $this->bookRepo->getBySearch($searchTerm);
        $chapters = $this->chapterRepo->getBySearch($searchTerm);
        $this->setPageTitle('Search For ' . $searchTerm);
        return view('search/all', ['pages' => $pages, 'books' => $books, 'chapters' => $chapters, 'searchTerm' => $searchTerm]);
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
