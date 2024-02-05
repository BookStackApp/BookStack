<?php

namespace BookStack\Entities\Controllers;

use BookStack\Activity\ActivityType;
use BookStack\Entities\Queries\BookQueries;
use BookStack\Entities\Tools\BookContents;
use BookStack\Entities\Tools\BookSortMap;
use BookStack\Facades\Activity;
use BookStack\Http\Controller;
use Illuminate\Http\Request;

class BookSortController extends Controller
{
    public function __construct(
        protected BookQueries $queries,
    ) {
    }

    /**
     * Shows the view which allows pages to be re-ordered and sorted.
     */
    public function show(string $bookSlug)
    {
        $book = $this->queries->findVisibleBySlugOrFail($bookSlug);
        $this->checkOwnablePermission('book-update', $book);

        $bookChildren = (new BookContents($book))->getTree(false);

        $this->setPageTitle(trans('entities.books_sort_named', ['bookName' => $book->getShortName()]));

        return view('books.sort', ['book' => $book, 'current' => $book, 'bookChildren' => $bookChildren]);
    }

    /**
     * Shows the sort box for a single book.
     * Used via AJAX when loading in extra books to a sort.
     */
    public function showItem(string $bookSlug)
    {
        $book = $this->queries->findVisibleBySlugOrFail($bookSlug);
        $bookChildren = (new BookContents($book))->getTree();

        return view('books.parts.sort-box', ['book' => $book, 'bookChildren' => $bookChildren]);
    }

    /**
     * Sorts a book using a given mapping array.
     */
    public function update(Request $request, string $bookSlug)
    {
        $book = $this->queries->findVisibleBySlugOrFail($bookSlug);
        $this->checkOwnablePermission('book-update', $book);

        // Return if no map sent
        if (!$request->filled('sort-tree')) {
            return redirect($book->getUrl());
        }

        $sortMap = BookSortMap::fromJson($request->get('sort-tree'));
        $bookContents = new BookContents($book);
        $booksInvolved = $bookContents->sortUsingMap($sortMap);

        // Rebuild permissions and add activity for involved books.
        foreach ($booksInvolved as $bookInvolved) {
            Activity::add(ActivityType::BOOK_SORT, $bookInvolved);
        }

        return redirect($book->getUrl());
    }
}
