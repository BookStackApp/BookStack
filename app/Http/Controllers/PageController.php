<?php

namespace Oxbow\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Oxbow\Http\Requests;
use Oxbow\Repos\BookRepo;
use Oxbow\Repos\PageRepo;

class PageController extends Controller
{

    protected $pageRepo;
    protected $bookRepo;

    /**
     * PageController constructor.
     * @param $pageRepo
     * @param $bookRepo
     */
    public function __construct(PageRepo $pageRepo, BookRepo $bookRepo)
    {
        $this->pageRepo = $pageRepo;
        $this->bookRepo = $bookRepo;
    }


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $bookSlug
     * @param bool $pageSlug
     * @return Response
     */
    public function create($bookSlug, $pageSlug = false)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $pageSlug ? $this->pageRepo->getBySlug($pageSlug, $book->id) : false;
        return view('pages/create', ['book' => $book, 'parentPage' => $page]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @param $bookSlug
     * @return Response
     */
    public function store(Request $request, $bookSlug)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'html' => 'required|string',
            'priority' => 'integer',
            'parent' => 'integer|exists:pages,id'
        ]);
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $this->pageRepo->newFromInput($request->all());
        $slug = Str::slug($page->name);
        while($this->pageRepo->countBySlug($slug, $book->id) > 0) {
            $slug .= '1';
        }
        $page->slug =$slug;

        if($request->has('parent')) {
            $page->page_id = $request->get('parent');
        }

        $page->book_id = $book->id;
        $page->text = strip_tags($page->html);
        $page->save();
        return redirect($page->getUrl());
    }

    /**
     * Display the specified resource.
     *
     * @param $bookSlug
     * @param $pageSlug
     * @return Response
     */
    public function show($bookSlug, $pageSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $this->pageRepo->getBySlug($pageSlug, $book->id);
        $breadCrumbs = $this->pageRepo->getBreadCrumbs($page);
        return view('pages/show', ['page' => $page, 'breadCrumbs' => $breadCrumbs, 'book' => $book]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $bookSlug
     * @param $pageSlug
     * @return Response
     */
    public function edit($bookSlug, $pageSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $this->pageRepo->getBySlug($pageSlug, $book->id);
        return view('pages/edit', ['page' => $page]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param $bookSlug
     * @param $pageSlug
     * @return Response
     */
    public function update(Request $request, $bookSlug, $pageSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $this->pageRepo->getBySlug($pageSlug, $book->id);
        $page->fill($request->all());
        $slug = Str::slug($page->name);
        while($this->pageRepo->countBySlug($slug, $book->id) > 0 && $slug != $pageSlug) {
            $slug .= '1';
        }
        $page->text = strip_tags($page->html);
        $page->save();
        return redirect($page->getUrl());
    }

    public function redirectFromLink($pageId)
    {
        $page = $this->pageRepo->getById($pageId);
        return redirect($page->getUrl());
    }

    public function searchAll(Request $request)
    {
        $searchTerm = $request->get('term');
        if(empty($searchTerm)) return redirect()->back();

        $pages = $this->pageRepo->getBySearch($searchTerm);
        return view('pages/search-results', ['pages' => $pages, 'searchTerm' => $searchTerm]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
