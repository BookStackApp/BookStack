<?php

namespace BookStack\Http\Controllers;

use BookStack\Auth\Permissions\PermissionApplicator;
use BookStack\Entities\Models\Page;
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

        $baseQuery = $page->referencesTo()
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

        return view('pages.references', [
            'page' => $page,
            'references' => $references,
        ]);
    }
}
