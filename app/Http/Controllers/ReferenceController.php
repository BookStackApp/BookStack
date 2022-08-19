<?php

namespace BookStack\Http\Controllers;

use BookStack\Auth\Permissions\PermissionApplicator;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;

class ReferenceController extends Controller
{

    protected PermissionApplicator $permissions;

    public function __construct(PermissionApplicator $permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * Display the references to a given page.
     */
    public function page(string $bookSlug, string $pageSlug)
    {
        /** @var Page $page */
        $page = Page::visible()->whereSlugs($bookSlug, $pageSlug)->firstOrFail();
        $references = $this->getEntityReferences($page);

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
        $references = $this->getEntityReferences($chapter);

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
        $references = $this->getEntityReferences($book);

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
        $references = $this->getEntityReferences($shelf);

        return view('shelves.references', [
            'shelf' => $shelf,
            'references' => $references,
        ]);
    }

    /**
     * Query the references for the given entities.
     * Loads the commonly required relations while taking permissions into account.
     */
    protected function getEntityReferences(Entity $entity): Collection
    {
        $baseQuery = $entity->referencesTo()
            ->where('from_type', '=', (new Page())->getMorphClass())
            ->with([
                'from' => fn(Relation $query) => $query->select(Page::$listAttributes),
                'from.book' => fn(Relation $query) => $query->scopes('visible'),
                'from.chapter' => fn(Relation $query) => $query->scopes('visible')
            ]);

        $references = $this->permissions->restrictEntityRelationQuery(
            $baseQuery,
            'references',
            'from_id',
            'from_type'
        )->get();

        return $references;
    }
}
