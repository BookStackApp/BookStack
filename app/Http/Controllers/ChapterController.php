<?php

namespace Oxbow\Http\Controllers;

use Activity;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Oxbow\Http\Requests;
use Oxbow\Http\Controllers\Controller;
use Oxbow\Repos\BookRepo;
use Oxbow\Repos\ChapterRepo;

class ChapterController extends Controller
{

    protected $bookRepo;
    protected $chapterRepo;

    /**
     * ChapterController constructor.
     * @param $bookRepo
     * @param $chapterRepo
     */
    public function __construct(BookRepo $bookRepo,ChapterRepo $chapterRepo)
    {
        $this->bookRepo = $bookRepo;
        $this->chapterRepo = $chapterRepo;
    }
    

    /**
     * Show the form for creating a new chapter.
     *
     * @param $bookSlug
     * @return Response
     */
    public function create($bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        return view('chapters/create', ['book' => $book, 'current' => $book]);
    }

    /**
     * Store a newly created chapter in storage.
     *
     * @param $bookSlug
     * @param  Request $request
     * @return Response
     */
    public function store($bookSlug, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255'
        ]);

        $book = $this->bookRepo->getBySlug($bookSlug);
        $chapter = $this->chapterRepo->newFromInput($request->all());
        $chapter->slug = $this->chapterRepo->findSuitableSlug($chapter->name, $book->id);
        $chapter->priority = $this->bookRepo->getNewPriority($book);
        $chapter->created_by = Auth::user()->id;
        $chapter->updated_by = Auth::user()->id;
        $book->chapters()->save($chapter);
        Activity::add($chapter, 'chapter_create', $book->id);
        return redirect($book->getUrl());
    }

    /**
     * Display the specified chapter.
     *
     * @param $bookSlug
     * @param $chapterSlug
     * @return Response
     */
    public function show($bookSlug, $chapterSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $chapter = $this->chapterRepo->getBySlug($chapterSlug, $book->id);
        return view('chapters/show', ['book' => $book, 'chapter' => $chapter, 'current' => $chapter]);
    }

    /**
     * Show the form for editing the specified chapter.
     *
     * @param $bookSlug
     * @param $chapterSlug
     * @return Response
     */
    public function edit($bookSlug, $chapterSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $chapter = $this->chapterRepo->getBySlug($chapterSlug, $book->id);
        return view('chapters/edit', ['book' => $book, 'chapter' => $chapter, 'current' => $chapter]);
    }

    /**
     * Update the specified chapter in storage.
     *
     * @param  Request $request
     * @param $bookSlug
     * @param $chapterSlug
     * @return Response
     */
    public function update(Request $request, $bookSlug, $chapterSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $chapter = $this->chapterRepo->getBySlug($chapterSlug, $book->id);
        $chapter->fill($request->all());
        $chapter->slug = $this->chapterRepo->findSuitableSlug($chapter->name, $book->id, $chapter->id);
        $chapter->updated_by = Auth::user()->id;
        $chapter->save();
        Activity::add($chapter, 'chapter_update', $book->id);
        return redirect($chapter->getUrl());
    }

    /**
     * Shows the page to confirm deletion of this chapter.
     * @param $bookSlug
     * @param $chapterSlug
     * @return \Illuminate\View\View
     */
    public function showDelete($bookSlug, $chapterSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $chapter = $this->chapterRepo->getBySlug($chapterSlug, $book->id);
        return view('chapters/delete', ['book' => $book, 'chapter' => $chapter, 'current' => $chapter]);
    }

    /**
     * Remove the specified chapter from storage.
     *
     * @param $bookSlug
     * @param $chapterSlug
     * @return Response
     */
    public function destroy($bookSlug, $chapterSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $chapter = $this->chapterRepo->getBySlug($chapterSlug, $book->id);
        $chapterName = $chapter->name;
        if(count($chapter->pages) > 0) {
            foreach($chapter->pages as $page) {
                $page->chapter_id = 0;
                $page->save();
            }
        }
        $chapter->delete();
        Activity::addMessage('chapter_delete', $book->id, $chapterName);
        return redirect($book->getUrl());
    }
}
