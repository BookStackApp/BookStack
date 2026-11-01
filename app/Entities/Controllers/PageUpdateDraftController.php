<?php

declare(strict_types=1);

namespace BookStack\Entities\Controllers;

use BookStack\Entities\Queries\PageQueries;
use BookStack\Entities\Repos\PageUpdateDraftRepo;
use BookStack\Http\Controller;
use Illuminate\Http\Response;

class PageUpdateDraftController extends Controller
{
    public function __construct(
        protected PageQueries $pageQueries,
        protected PageUpdateDraftRepo $draftRepo,
    ) {
    }

    /**
     * Destroys existing drafts, belonging to the current user, for the given page.
     */
    public function destroy(string $pageId): Response
    {
        $page = $this->pageQueries->findVisibleByIdOrFail(intval($pageId));
        $this->draftRepo->deleteForCurrentUser($page);

        return response('', 200);
    }
}
