<?php

namespace BookStack\References;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Http\Controller;

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
        $page = Page::getBySlugs($bookSlug, $pageSlug);
        $references = $this->referenceFetcher->getPageReferencesToEntity($page);

        return view('pages.references', [
            'page'       => $page,
            'references' => $references,
        ]);
    }

    /**
     * Display the references to a given chapter.
     */
    public function chapter(string $bookSlug, string $chapterSlug)
    {
        $chapter = Chapter::getBySlugs($bookSlug, $chapterSlug);
        $references = $this->referenceFetcher->getPageReferencesToEntity($chapter);

        return view('chapters.references', [
            'chapter'    => $chapter,
            'references' => $references,
        ]);
    }

    /**
     * Display the references to a given book.
     */
    public function book(string $slug)
    {
        $book = Book::getBySlug($slug);
        $references = $this->referenceFetcher->getPageReferencesToEntity($book);

        return view('books.references', [
            'book'       => $book,
            'references' => $references,
        ]);
    }

    /**
     * Display the references to a given shelf.
     */
    public function shelf(string $slug)
    {
        $shelf = Bookshelf::getBySlug($slug);
        $references = $this->referenceFetcher->getPageReferencesToEntity($shelf);

        return view('shelves.references', [
            'shelf'      => $shelf,
            'references' => $references,
        ]);
    }
}
