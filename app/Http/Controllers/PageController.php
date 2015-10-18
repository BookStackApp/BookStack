<?php

namespace BookStack\Http\Controllers;

use Activity;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use BookStack\Http\Requests;
use BookStack\Repos\BookRepo;
use BookStack\Repos\ChapterRepo;
use BookStack\Repos\PageRepo;

class PageController extends Controller
{

    protected $pageRepo;
    protected $bookRepo;
    protected $chapterRepo;

    /**
     * PageController constructor.
     * @param PageRepo    $pageRepo
     * @param BookRepo    $bookRepo
     * @param ChapterRepo $chapterRepo
     */
    public function __construct(PageRepo $pageRepo, BookRepo $bookRepo, ChapterRepo $chapterRepo)
    {
        $this->pageRepo = $pageRepo;
        $this->bookRepo = $bookRepo;
        $this->chapterRepo = $chapterRepo;
        parent::__construct();
    }

    /**
     * Show the form for creating a new page.
     *
     * @param      $bookSlug
     * @param bool $chapterSlug
     * @return Response
     * @internal param bool $pageSlug
     */
    public function create($bookSlug, $chapterSlug = false)
    {
        $this->checkPermission('page-create');
        $book = $this->bookRepo->getBySlug($bookSlug);
        $chapter = $chapterSlug ? $this->chapterRepo->getBySlug($chapterSlug, $book->id) : false;
        return view('pages/create', ['book' => $book, 'chapter' => $chapter]);
    }

    /**
     * Store a newly created page in storage.
     *
     * @param  Request $request
     * @param          $bookSlug
     * @return Response
     */
    public function store(Request $request, $bookSlug)
    {
        $this->checkPermission('page-create');
        $this->validate($request, [
            'name'   => 'required|string|max:255',
            'html'   => 'required|string',
            'parent' => 'integer|exists:pages,id'
        ]);

        $input = $request->all();
        $book = $this->bookRepo->getBySlug($bookSlug);
        $chapterId = ($request->has('chapter') && $this->chapterRepo->idExists($request->get('chapter'))) ? $request->get('chapter') : null;
        $input['priority'] = $this->bookRepo->getNewPriority($book);

        $page = $this->pageRepo->saveNew($input, $book, $chapterId);

        Activity::add($page, 'page_create', $book->id);
        return redirect($page->getUrl());
    }

    /**
     * Display the specified page.
     *
     * @param $bookSlug
     * @param $pageSlug
     * @return Response
     */
    public function show($bookSlug, $pageSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $this->pageRepo->getBySlug($pageSlug, $book->id);
        return view('pages/show', ['page' => $page, 'book' => $book, 'current' => $page]);
    }

    /**
     * Show the form for editing the specified page.
     *
     * @param $bookSlug
     * @param $pageSlug
     * @return Response
     */
    public function edit($bookSlug, $pageSlug)
    {
        $this->checkPermission('page-update');
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $this->pageRepo->getBySlug($pageSlug, $book->id);
        return view('pages/edit', ['page' => $page, 'book' => $book, 'current' => $page]);
    }

    /**
     * Update the specified page in storage.
     *
     * @param  Request $request
     * @param          $bookSlug
     * @param          $pageSlug
     * @return Response
     */
    public function update(Request $request, $bookSlug, $pageSlug)
    {
        $this->checkPermission('page-update');
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $this->pageRepo->getBySlug($pageSlug, $book->id);
        $this->pageRepo->updatePage($page, $book->id, $request->all());
        Activity::add($page, 'page_update', $book->id);
        return redirect($page->getUrl());
    }

    /**
     * Redirect from a special link url which
     * uses the page id rather than the name.
     * @param $pageId
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function redirectFromLink($pageId)
    {
        $page = $this->pageRepo->getById($pageId);
        return redirect($page->getUrl());
    }

    /**
     * Show the deletion page for the specified page.
     * @param $bookSlug
     * @param $pageSlug
     * @return \Illuminate\View\View
     */
    public function showDelete($bookSlug, $pageSlug)
    {
        $this->checkPermission('page-delete');
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $this->pageRepo->getBySlug($pageSlug, $book->id);
        return view('pages/delete', ['book' => $book, 'page' => $page, 'current' => $page]);
    }

    /**
     * Remove the specified page from storage.
     *
     * @param $bookSlug
     * @param $pageSlug
     * @return Response
     * @internal param int $id
     */
    public function destroy($bookSlug, $pageSlug)
    {
        $this->checkPermission('page-delete');
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $this->pageRepo->getBySlug($pageSlug, $book->id);
        Activity::addMessage('page_delete', $book->id, $page->name);
        Activity::removeEntity($page);
        $page->delete();
        return redirect($book->getUrl());
    }

    /**
     * Shows the last revisions for this page.
     * @param $bookSlug
     * @param $pageSlug
     * @return \Illuminate\View\View
     */
    public function showRevisions($bookSlug, $pageSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $this->pageRepo->getBySlug($pageSlug, $book->id);
        return view('pages/revisions', ['page' => $page, 'book' => $book, 'current' => $page]);
    }

    /**
     * Shows a preview of a single revision
     * @param $bookSlug
     * @param $pageSlug
     * @param $revisionId
     * @return \Illuminate\View\View
     */
    public function showRevision($bookSlug, $pageSlug, $revisionId)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $this->pageRepo->getBySlug($pageSlug, $book->id);
        $revision = $this->pageRepo->getRevisionById($revisionId);
        $page->fill($revision->toArray());
        return view('pages/revision', ['page' => $page, 'book' => $book]);
    }

    /**
     * Restores a page using the content of the specified revision.
     * @param $bookSlug
     * @param $pageSlug
     * @param $revisionId
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function restoreRevision($bookSlug, $pageSlug, $revisionId)
    {
        $this->checkPermission('page-update');
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $this->pageRepo->getBySlug($pageSlug, $book->id);
        $page = $this->pageRepo->restoreRevision($page, $book, $revisionId);
        Activity::add($page, 'page_restore', $book->id);
        return redirect($page->getUrl());
    }
}
