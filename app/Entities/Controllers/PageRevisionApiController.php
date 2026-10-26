<?php

declare(strict_types=1);

namespace BookStack\Entities\Controllers;

use BookStack\Entities\Models\Page;
use BookStack\Entities\Models\PageRevision;
use BookStack\Entities\Queries\PageQueries;
use BookStack\Entities\Tools\PageContent;
use BookStack\Exceptions\NotFoundException;
use BookStack\Http\ApiController;
use BookStack\Permissions\Permission;
use Illuminate\Http\JsonResponse;

/**
 * Page revision details, including the content of individual saved revisions.
 *
 * Only saved page versions are provided, in-progress user drafts are excluded.
 */
class PageRevisionApiController extends ApiController
{
    public function __construct(
        protected PageQueries $pageQueries,
    ) {
        $this->middleware(function ($request, $next) {
            $this->checkPermission(Permission::RevisionViewAll);

            return $next($request);
        });
    }

    /**
     * Get a listing of the saved revisions for the given page.
     *
     * Results are ordered oldest first by default. The name, summary and slugs
     * provided are those captured when the revision was created.
     *
     * Revision content is not included in this listing, use the revision read
     * endpoint to fetch the content of an individual revision.
     *
     * Requires the "revision-view-all" system permission and view access to the page.
     */
    public function listForPage(string $id): JsonResponse
    {
        $page = $this->pageQueries->findVisibleByIdOrFail(intval($id));

        $query = PageRevision::query()
            ->where('page_id', '=', $page->id)
            ->where('type', '=', 'version');

        return $this->apiListingResponse($query, [
            'id', 'page_id', 'name', 'slug', 'book_slug',
            'type', 'summary', 'revision_number',
            'created_at', 'updated_at', 'created_by',
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
     * Requires the "revision-view-all" system permission and view access to the page.
     *
     * @throws NotFoundException
     */
    public function read(string $id): JsonResponse
    {
        $revision = PageRevision::query()
            ->where('id', '=', intval($id))
            ->where('type', '=', 'version')
            ->first();

        if ($revision === null) {
            throw new NotFoundException();
        }

        $page = $this->pageQueries->findVisibleById($revision->page_id);
        if ($page === null) {
            throw new NotFoundException();
        }

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
