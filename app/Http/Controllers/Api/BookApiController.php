<?php

namespace BookStack\Http\Controllers\Api;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Repos\BookRepo;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BookApiController extends ApiController
{
    protected $bookRepo;

    protected $rules = [
        'create' => [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['string', 'max:1000'],
            'tags'        => ['array'],
        ],
        'update' => [
            'name'        => ['string', 'min:1', 'max:255'],
            'description' => ['string', 'max:1000'],
            'tags'        => ['array'],
        ],
    ];

    public function __construct(BookRepo $bookRepo)
    {
        $this->bookRepo = $bookRepo;
    }

    /**
     * Get a listing of books visible to the user.
     */
    public function list()
    {
        $books = Book::visible();

        return $this->apiListingResponse($books, [
            'id', 'name', 'slug', 'description', 'created_at', 'updated_at', 'created_by', 'updated_by', 'owned_by',
        ]);
    }

    /**
     * Create a new book in the system.
     *
     * @throws ValidationException
     */
    public function create(Request $request)
    {
        $this->checkPermission('book-create-all');
        $requestData = $this->validate($request, $this->rules['create']);

        $book = $this->bookRepo->create($requestData);

        return response()->json($book);
    }

    /**
     * View the details of a single book.
     */
    public function read(string $id)
    {
        $book = Book::visible()->with(['tags', 'cover', 'createdBy', 'updatedBy', 'ownedBy'])->findOrFail($id);

        return response()->json($book);
    }

    /**
     * Update the details of a single book.
     *
     * @throws ValidationException
     */
    public function update(Request $request, string $id)
    {
        $book = Book::visible()->findOrFail($id);
        $this->checkOwnablePermission('book-update', $book);

        $requestData = $this->validate($request, $this->rules['update']);
        $book = $this->bookRepo->update($book, $requestData);

        return response()->json($book);
    }

    /**
     * Delete a single book.
     * This will typically send the book to the recycle bin.
     *
     * @throws \Exception
     */
    public function delete(string $id)
    {
        $book = Book::visible()->findOrFail($id);
        $this->checkOwnablePermission('book-delete', $book);

        $this->bookRepo->destroy($book);

        return response('', 204);
    }
}
