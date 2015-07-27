<?php

namespace Oxbow\Http\Controllers;

use Illuminate\Http\Request;

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
     * @return Response
     */
    public function create($bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        return view('chapters/create', ['book' => $book]);
    }

    /**
     * Store a newly created resource in storage.
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
        $book->chapters()->save($chapter);
        return redirect($book->getUrl());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
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
