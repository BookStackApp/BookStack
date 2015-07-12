<?php

namespace Oxbow\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Oxbow\Http\Requests;
use Oxbow\Repos\BookRepo;

class BookController extends Controller
{

    protected $bookRepo;

    /**
     * BookController constructor.
     * @param BookRepo $bookRepo
     */
    public function __construct(BookRepo $bookRepo)
    {
        $this->bookRepo = $bookRepo;
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
            $slug += '1';
        }
        $book->slug = $slug;
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
        $slug = Str::slug($book->name);
        while($this->bookRepo->countBySlug($slug) > 0 && $book->slug != $slug) {
            $slug += '1';
        }
        $book->slug = $slug;
        $book->save();
        return redirect('/books');
    }

    /**
     * Remove the specified book from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $this->bookRepo->destroyById($id);
        return redirect('/books');
    }
}
