<?php

declare(strict_types=1);

namespace BookStack\Entities\Controllers;

use BookStack\Entities\Models\Page;
use BookStack\Entities\Models\PageRevision;
use BookStack\Entities\Queries\PageQueries;
use BookStack\Entities\Queries\PageRevisionQueries;
use BookStack\Entities\Tools\PageContent;
use BookStack\Exceptions\NotFoundException;
use BookStack\Http\ApiController;
use BookStack\Permissions\Permission;
use Illuminate\Http\JsonResponse;

/**
 * List and view details of page revisions.
 * The 'revision_number' property on revisions is the page-local incrementing revision number.
 * The 'name' and 'slug' represents the page name & URL slug at the time of revision creation,
 * with the 'book_slug' being that for the parent book at that time.
 */
class PageRevisionApiController extends ApiController
{
    public function __construct(
        protected PageQueries $pageQueries,
        protected PageRevisionQueries $revisionQueries,
    ) {
        $this->middleware(function ($request, $next) {
            $this->checkPermission(Permission::RevisionViewAll);

            return $next($request);
        });
    }

    /**
     * Get a listing of all visible page revisions across all pages.
     * Filter on 'page_id' to find revisions for a specific page.
     */
    public function list(): JsonResponse
    {
        $query = $this->revisionQueries->visible();

        return $this->apiListingResponse($query, [
            'id', 'page_id', 'name', 'slug', 'book_slug',
            'summary', 'revision_number',
            'created_at', 'created_by',
        ]);
    }

    /**
     * View the details of a single saved page revision.
     *
     * The 'html' property is the revision content rendered as it would be shown
     * when previewing the revision, with page includes handled.
     * The 'raw_html' property is the direct database stored HTML content.
     * The 'markdown' property will be populated when the revision was created
     * while the Markdown editor was in use.
     *
     * The 'raw_html' and 'markdown' properties are provided as stored, and are
     * not sanitised for display. See the "Content Security" section of these docs.
     *
     * Requires permission to view revisions and to view the related page.
     *
     * @throws NotFoundException
     */
    public function read(string $id): JsonResponse
    {
        /** @var PageRevision $revision */
        $revision = $this->revisionQueries->visible()->findOrFail($id);
        $page = $this->pageQueries->findVisibleByIdOrFail($revision->page_id);

        $revision->load('createdBy');
        $revision->makeVisible(['html', 'markdown']);
        $revision->setAttribute('raw_html', $revision->html);
        $revision->setAttribute('html', $this->renderRevisionHtml($page, $revision));

        return response()->json($revision);
    }

    /**
     * Render the given revision HTML within the context of its page.
     */
    protected function renderRevisionHtml(Page $page, PageRevision $revision): string
    {
        $renderPage = clone $page;
        $renderPage->name = $revision->name;
        $renderPage->html = $revision->html;

        return (new PageContent($renderPage))->render();
    }
}
