<?php

namespace BookStack\Http\Controllers;

use BookStack\Entities\Book;
use BookStack\Entities\Managers\BookContents;
use BookStack\Entities\Repos\BookRepo;
use BookStack\Exceptions\SortOperationException;
use BookStack\Facades\Activity;
use Illuminate\Http\Request;

class BookSortController extends Controller
{

    protected $bookRepo;

    /**
     * BookSortController constructor.
     * @param $bookRepo
     */
    public function __construct(BookRepo $bookRepo)
    {
        $this->bookRepo = $bookRepo;
        parent::__construct();
    }

    /**
     * Shows the view which allows pages to be re-ordered and sorted.
     */
    public function show(string $bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $this->checkOwnablePermission('book-update', $book);

        $bookChildren = (new BookContents($book))->getTree(false);

        $this->setPageTitle(trans('entities.books_sort_named', ['bookName'=>$book->getShortName()]));
        return view('books.sort', ['book' => $book, 'current' => $book, 'bookChildren' => $bookChildren]);
    }

    /**
     * Shows the sort box for a single book.
     * Used via AJAX when loading in extra books to a sort.
     */
    public function showItem(string $bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $bookChildren = (new BookContents($book))->getTree();
        return view('books.sort-box', ['book' => $book, 'bookChildren' => $bookChildren]);
    }

    /**
     * Sorts a book using a given mapping array.
     */
    public function update(Request $request, string $bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $this->checkOwnablePermission('book-update', $book);

        // Return if no map sent
        if (!$request->filled('sort-tree')) {
            return redirect($book->getUrl());
        }

        $sortMap = collect(json_decode($request->get('sort-tree')));
        $bookContents = new BookContents($book);
        $booksInvolved = collect();

        try {
            $booksInvolved = $bookContents->sortUsingMap($sortMap);
        } catch (SortOperationException $exception) {
            $this->showPermissionError();
        }

        // Rebuild permissions and add activity for involved books.
        $booksInvolved->each(function (Book $book) {
            Activity::add($book, 'book_sort', $book->id);
        });

        return redirect($book->getUrl());
    }
}
