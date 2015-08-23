<?php

namespace Oxbow\Http\Controllers;

use Activity;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Oxbow\Http\Requests;
use Oxbow\Repos\BookRepo;
use Oxbow\Repos\ChapterRepo;
use Oxbow\Repos\PageRepo;

class PageController extends Controller
{

    protected $pageRepo;
    protected $bookRepo;
    protected $chapterRepo;

    /**
     * PageController constructor.
     * @param PageRepo $pageRepo
     * @param BookRepo $bookRepo
     * @param ChapterRepo $chapterRepo
     */
    public function __construct(PageRepo $pageRepo, BookRepo $bookRepo, ChapterRepo $chapterRepo)
    {
        $this->pageRepo = $pageRepo;
        $this->bookRepo = $bookRepo;
        $this->chapterRepo = $chapterRepo;
    }

    /**
     * Show the form for creating a new page.
     *
     * @param $bookSlug
     * @param bool $chapterSlug
     * @return Response
     * @internal param bool $pageSlug
     */
    public function create($bookSlug, $chapterSlug = false)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $chapter = $chapterSlug ? $this->chapterRepo->getBySlug($chapterSlug, $book->id) : false;
        return view('pages/create', ['book' => $book, 'chapter' => $chapter]);
    }

    /**
     * Store a newly created page in storage.
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
            'parent' => 'integer|exists:pages,id'
        ]);
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $this->pageRepo->newFromInput($request->all());

        $page->slug = $this->pageRepo->findSuitableSlug($page->name, $book->id);
        $page->priority = $this->bookRepo->getNewPriority($book);

        if($request->has('chapter') && $this->chapterRepo->idExists($request->get('chapter'))) {
            $page->chapter_id = $request->get('chapter');
        }

        $page->book_id = $book->id;
        $page->text = strip_tags($page->html);
        $page->created_by = Auth::user()->id;
        $page->updated_by = Auth::user()->id;
        $page->save();
        $this->pageRepo->saveRevision($page);
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
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $this->pageRepo->getBySlug($pageSlug, $book->id);
        return view('pages/edit', ['page' => $page, 'book' => $book, 'current' => $page]);
    }

    /**
     * Update the specified page in storage.
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
     * Search all available pages, Across all books.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function searchAll(Request $request)
    {
        $searchTerm = $request->get('term');
        if(empty($searchTerm)) return redirect()->back();

        $pages = $this->pageRepo->getBySearch($searchTerm);
        return view('pages/search-results', ['pages' => $pages, 'searchTerm' => $searchTerm]);
    }

    /**
     * Shows the view which allows pages to be re-ordered and sorted.
     * @param $bookSlug
     * @return \Illuminate\View\View
     */
    public function sortPages($bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        return view('pages/sort', ['book' => $book, 'current' => $book]);
    }

    /**
     * Saves an array of sort mapping to pages and chapters.
     *
     * @param $bookSlug
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function savePageSort($bookSlug, Request $request)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        // Return if no map sent
        if(!$request->has('sort-tree')) {
            return redirect($book->getUrl());
        }

        // Sort pages and chapters
        $sortMap = json_decode($request->get('sort-tree'));
        foreach($sortMap as $index => $bookChild) {
            $id = $bookChild->id;
            $isPage = $bookChild->type == 'page';
            $model = $isPage ? $this->pageRepo->getById($id) : $this->chapterRepo->getById($id);
            $model->priority = $index;
            if($isPage) {
                $model->chapter_id = ($bookChild->parentChapter === false) ? 0 : $bookChild->parentChapter;
            }
            $model->save();
        }
        Activity::add($book, 'book_sort', $book->id);
        return redirect($book->getUrl());
    }

    /**
     * Show the deletion page for the specified page.
     * @param $bookSlug
     * @param $pageSlug
     * @return \Illuminate\View\View
     */
    public function showDelete($bookSlug, $pageSlug)
    {
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

    public function restoreRevision($bookSlug, $pageSlug, $revisionId)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $page = $this->pageRepo->getBySlug($pageSlug, $book->id);
        $revision = $this->pageRepo->getRevisionById($revisionId);
        $page = $this->pageRepo->updatePage($page, $book->id, $revision->toArray());
        Activity::add($page, 'page_restore', $book->id);
        return redirect($page->getUrl());
    }
}
