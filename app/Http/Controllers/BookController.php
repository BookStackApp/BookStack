<?php

namespace Oxbow\Http\Controllers;

use Activity;
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
        parent::__construct();
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
        $this->checkPermission('book-create');
        return view('books/create');
    }

    /**
     * Store a newly created book in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->checkPermission('book-create');
        $this->validate($request, [
            'name'        => 'required|string|max:255',
            'description' => 'string|max:1000'
        ]);
        $book = $this->bookRepo->newFromInput($request->all());
        $book->slug = $this->bookRepo->findSuitableSlug($book->name);
        $book->created_by = Auth::user()->id;
        $book->updated_by = Auth::user()->id;
        $book->save();
        Activity::add($book, 'book_create', $book->id);
        return redirect($book->getUrl());
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
        return view('books/show', ['book' => $book, 'current' => $book]);
    }

    /**
     * Show the form for editing the specified book.
     *
     * @param $slug
     * @return Response
     */
    public function edit($slug)
    {
        $this->checkPermission('book-update');
        $book = $this->bookRepo->getBySlug($slug);
        return view('books/edit', ['book' => $book, 'current' => $book]);
    }

    /**
     * Update the specified book in storage.
     *
     * @param  Request $request
     * @param          $slug
     * @return Response
     */
    public function update(Request $request, $slug)
    {
        $this->checkPermission('book-update');
        $book = $this->bookRepo->getBySlug($slug);
        $this->validate($request, [
            'name'        => 'required|string|max:255',
            'description' => 'string|max:1000'
        ]);
        $book->fill($request->all());
        $book->slug = $this->bookRepo->findSuitableSlug($book->name, $book->id);
        $book->updated_by = Auth::user()->id;
        $book->save();
        Activity::add($book, 'book_update', $book->id);
        return redirect($book->getUrl());
    }

    /**
     * Shows the page to confirm deletion
     * @param $bookSlug
     * @return \Illuminate\View\View
     */
    public function showDelete($bookSlug)
    {
        $this->checkPermission('book-delete');
        $book = $this->bookRepo->getBySlug($bookSlug);
        return view('books/delete', ['book' => $book, 'current' => $book]);
    }

    /**
     * Remove the specified book from storage.
     *
     * @param $bookSlug
     * @return Response
     */
    public function destroy($bookSlug)
    {
        $this->checkPermission('book-delete');
        $book = $this->bookRepo->getBySlug($bookSlug);
        Activity::addMessage('book_delete', 0, $book->name);
        $this->bookRepo->destroyBySlug($bookSlug);
        return redirect('/books');
    }
}
