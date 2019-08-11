<?php

namespace BookStack\Http\Controllers;

use BookStack\Entities\Repos\PageRepo;
use BookStack\Exceptions\NotFoundException;
use Illuminate\Http\Request;

class PageTemplateController extends Controller
{
    protected $pageRepo;

    /**
     * PageTemplateController constructor.
     * @param $pageRepo
     */
    public function __construct(PageRepo $pageRepo)
    {
        $this->pageRepo = $pageRepo;
        parent::__construct();
    }

    /**
     * Fetch a list of templates from the system.
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list(Request $request)
    {
        $page = $request->get('page', 1);
        $search = $request->get('search', '');
        $templates = $this->pageRepo->getPageTemplates(10, $page, $search);

        if ($search) {
            $templates->appends(['search' => $search]);
        }

        return view('pages.template-manager-list', [
            'templates' => $templates
        ]);
    }

    /**
     * Get the content of a template.
     * @param $templateId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws NotFoundException
     */
    public function get($templateId)
    {
        $page = $this->pageRepo->getById('page', $templateId);

        if (!$page->template) {
            throw new NotFoundException();
        }

        return response()->json([
            'html' => $page->html,
            'markdown' => $page->markdown,
        ]);
    }

}
