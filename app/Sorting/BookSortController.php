<?php

namespace BookStack\Sorting;

use BookStack\Activity\ActivityType;
use BookStack\Entities\Queries\BookQueries;
use BookStack\Entities\Tools\BookContents;
use BookStack\Facades\Activity;
use BookStack\Http\Controller;
use BookStack\Util\DatabaseTransaction;
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
     * Update the sort options of a book, setting the auto-sort and/or updating
     * child order via mapping.
     */
    public function update(Request $request, BookSorter $sorter, string $bookSlug)
    {
        $book = $this->queries->findVisibleBySlugOrFail($bookSlug);
        $this->checkOwnablePermission('book-update', $book);
        $loggedActivityForBook = false;

        // Sort via map
        if ($request->filled('sort-tree')) {
            (new DatabaseTransaction(function () use ($book, $request, $sorter, &$loggedActivityForBook) {
                $sortMap = BookSortMap::fromJson($request->get('sort-tree'));
                $booksInvolved = $sorter->sortUsingMap($sortMap);

                // Add activity for involved books.
                foreach ($booksInvolved as $bookInvolved) {
                    Activity::add(ActivityType::BOOK_SORT, $bookInvolved);
                    if ($bookInvolved->id === $book->id) {
                        $loggedActivityForBook = true;
                    }
                }
            }))->run();
        }

        if ($request->filled('auto-sort')) {
            $sortSetId = intval($request->get('auto-sort')) ?: null;
            if ($sortSetId && SortRule::query()->find($sortSetId) === null) {
                $sortSetId = null;
            }
            $book->sort_rule_id = $sortSetId;
            $book->save();
            $sorter->runBookAutoSort($book);
            if (!$loggedActivityForBook) {
                Activity::add(ActivityType::BOOK_SORT, $book);
            }
        }

        return redirect($book->getUrl());
    }
}
