<?php

namespace BookStack\References;

use BookStack\Entities\Queries\EntityQueries;
use BookStack\Http\Controller;

class ReferenceController extends Controller
{
    public function __construct(
        protected ReferenceFetcher $referenceFetcher,
        protected EntityQueries $queries,
    ) {
    }

    /**
     * Display the references to a given page.
     */
    public function page(string $bookSlug, string $pageSlug)
    {
        $page = $this->queries->pages->findVisibleBySlugsOrFail($bookSlug, $pageSlug);
        $references = $this->referenceFetcher->getReferencesToEntity($page);

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
        $chapter = $this->queries->chapters->findVisibleBySlugsOrFail($bookSlug, $chapterSlug);
        $references = $this->referenceFetcher->getReferencesToEntity($chapter);

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
        $book = $this->queries->books->findVisibleBySlugOrFail($slug);
        $references = $this->referenceFetcher->getReferencesToEntity($book);

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
        $shelf = $this->queries->shelves->findVisibleBySlugOrFail($slug);
        $references = $this->referenceFetcher->getReferencesToEntity($shelf);

        return view('shelves.references', [
            'shelf'      => $shelf,
            'references' => $references,
        ]);
    }
}
