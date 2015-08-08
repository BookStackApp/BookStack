<?php

namespace Oxbow\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Oxbow\Http\Requests;
use Oxbow\Repos\BookRepo;
use Oxbow\Repos\PageRepo;

class BookController extends Controller
{

    protected $bookRepo;
    protected $pageRepo;

    /**
     * BookController constructor.
     * @param BookRepo $bookRepo
     * @param PageRepo $pageRepo
     */
    public function __construct(BookRepo $bookRepo, PageRepo $pageRepo)
    {
        $this->bookRepo = $bookRepo;
        $this->pageRepo = $pageRepo;
    }

    /**
     * Display a listing of the book.
     *
     * @return Response
     */
    public function index()
    {
        $books = $this->bookRepo->getAll();
        return view('books/index', ['books' => $books]);
    }

    /**
     * Show the form for creating a new book.
     *
     * @return Response
     */
    public function create()
    {
        return view('books/create');
    }

    /**
     * Store a newly created book in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'description' => 'string|max:1000'
        ]);
        $book = $this->bookRepo->newFromInput($request->all());
        $slug = Str::slug($book->name);
        while($this->bookRepo->countBySlug($slug) > 0) {
            $slug .= '1';
        }
        $book->slug = $slug;
        $book->created_by = Auth::user()->id;
        $book->updated_by = Auth::user()->id;
        $book->save();
        return redirect('/books');
    }

    /**
     * Display the specified book.
     *
     * @param $slug
     * @return Response
     */
    public function show($slug)
    {
        $book = $this->bookRepo->getBySlug($slug);
        return view('books/show', ['book' => $book]);
    }

    /**
     * Show the form for editing the specified book.
     *
     * @param $slug
     * @return Response
     */
    public function edit($slug)
    {
        $book = $this->bookRepo->getBySlug($slug);
        return view('books/edit', ['book' => $book]);
    }

    /**
     * Update the specified book in storage.
     *
     * @param  Request $request
     * @param $slug
     * @return Response
     */
    public function update(Request $request, $slug)
    {
        $book = $this->bookRepo->getBySlug($slug);
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'description' => 'string|max:1000'
        ]);
        $book->fill($request->all());
        $slug = Str::slug($book->name);
        while($this->bookRepo->countBySlug($slug) > 0 && $book->slug != $slug) {
            $slug += '1';
        }
        $book->slug = $slug;
        $book->updated_by = Auth::user()->id;
        $book->save();
        return redirect($book->getUrl());
    }

    /**
     * Shows the page to confirm deletion
     * @param $bookSlug
     * @return \Illuminate\View\View
     */
    public function showDelete($bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        return view('books/delete', ['book' => $book]);
    }

    /**
     * Remove the specified book from storage.
     *
     * @param $bookSlug
     * @return Response
     */
    public function destroy($bookSlug)
    {
        $this->bookRepo->destroyBySlug($bookSlug);
        return redirect('/books');
    }
}
