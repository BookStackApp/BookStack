<?php

namespace BookStack\Entities\Controllers;

use BookStack\Entities\Queries\PageQueries;
use BookStack\Entities\Repos\PageRepo;
use BookStack\Exceptions\NotFoundException;
use BookStack\Http\Controller;
use Illuminate\Http\Request;

class PageTemplateController extends Controller
{
    public function __construct(
        protected PageRepo $pageRepo,
        protected PageQueries $pageQueries,
    ) {
    }

    /**
     * Fetch a list of templates from the system.
     */
    public function list(Request $request)
    {
        $page = $request->get('page', 1);
        $search = $request->get('search', '');
        $count = 10;

        $query = $this->pageQueries->visibleTemplates()
            ->orderBy('name', 'asc')
            ->skip(($page - 1) * $count)
            ->take($count);

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $templates = $query->paginate($count, ['*'], 'page', $page);
        $templates->withPath('/templates');

        if ($search) {
            $templates->appends(['search' => $search]);
        }

        return view('pages.parts.template-manager-list', [
            'templates' => $templates,
        ]);
    }

    /**
     * Get the content of a template.
     *
     * @throws NotFoundException
     */
    public function get(int $templateId)
    {
        $page = $this->pageQueries->findVisibleByIdOrFail($templateId);

        if (!$page->template) {
            throw new NotFoundException();
        }

        return response()->json([
            'html'     => $page->html,
            'markdown' => $page->markdown,
        ]);
    }
}
