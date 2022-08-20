<?php

namespace BookStack\Http\Controllers;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\References\ReferenceFetcher;

class ReferenceController extends Controller
{
    protected ReferenceFetcher $referenceFetcher;

    public function __construct(ReferenceFetcher $referenceFetcher)
    {
        $this->referenceFetcher = $referenceFetcher;
    }

    /**
     * Display the references to a given page.
     */
    public function page(string $bookSlug, string $pageSlug)
    {
        /** @var Page $page */
        $page = Page::visible()->whereSlugs($bookSlug, $pageSlug)->firstOrFail();
        $references = $this->referenceFetcher->getPageReferencesToEntity($page);

        return view('pages.references', [
            'page' => $page,
            'references' => $references,
        ]);
    }

    /**
     * Display the references to a given chapter.
     */
    public function chapter(string $bookSlug, string $chapterSlug)
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::visible()->whereSlugs($bookSlug, $chapterSlug)->firstOrFail();
        $references = $this->referenceFetcher->getPageReferencesToEntity($chapter);

        return view('chapters.references', [
            'chapter' => $chapter,
            'references' => $references,
        ]);
    }

    /**
     * Display the references to a given book.
     */
    public function book(string $slug)
    {
        $book = Book::visible()->where('slug', '=', $slug)->firstOrFail();
        $references = $this->referenceFetcher->getPageReferencesToEntity($book);

        return view('books.references', [
            'book' => $book,
            'references' => $references,
        ]);
    }

    /**
     * Display the references to a given shelf.
     */
    public function shelf(string $slug)
    {
        $shelf = Bookshelf::visible()->where('slug', '=', $slug)->firstOrFail();
        $references = $this->referenceFetcher->getPageReferencesToEntity($shelf);

        return view('shelves.references', [
            'shelf' => $shelf,
            'references' => $references,
        ]);
    }
}
