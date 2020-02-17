<?php namespace BookStack\Http\Controllers\Api;

use BookStack\Entities\Book;
use BookStack\Entities\Repos\BookRepo;
use BookStack\Exceptions\NotifyException;
use BookStack\Facades\Activity;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BooksApiController extends ApiController
{

    protected $bookRepo;

    protected $rules = [
        'create' => [
            'name' => 'required|string|max:255',
            'description' => 'string|max:1000',
        ],
        'update' => [
            'name' => 'string|min:1|max:255',
            'description' => 'string|max:1000',
        ],
    ];

    /**
     * BooksApiController constructor.
     */
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
            'id', 'name', 'slug', 'description', 'created_at', 'updated_at', 'created_by', 'updated_by', 'image_id',
        ]);
    }

    /**
     * Create a new book in the system.
     * @throws ValidationException
     */
    public function create(Request $request)
    {
        $this->checkPermission('book-create-all');
        $requestData = $this->validate($request, $this->rules['create']);

        $book = $this->bookRepo->create($requestData);
        Activity::add($book, 'book_create', $book->id);

        return response()->json($book);
    }

    /**
     * View the details of a single book.
     */
    public function read(string $id)
    {
        $book = Book::visible()->with(['tags', 'cover', 'createdBy', 'updatedBy'])->findOrFail($id);
        return response()->json($book);
    }

    /**
     * Update the details of a single book.
     * @throws ValidationException
     */
    public function update(Request $request, string $id)
    {
        $book = Book::visible()->findOrFail($id);
        $this->checkOwnablePermission('book-update', $book);

        $requestData = $this->validate($request, $this->rules['update']);
        $book = $this->bookRepo->update($book, $requestData);
        Activity::add($book, 'book_update', $book->id);

        return response()->json($book);
    }

    /**
     * Delete a single book from the system.
     * @throws NotifyException
     * @throws BindingResolutionException
     */
    public function delete(string $id)
    {
        $book = Book::visible()->findOrFail($id);
        $this->checkOwnablePermission('book-delete', $book);

        $this->bookRepo->destroy($book);
        Activity::addMessage('book_delete', $book->name);

        return response('', 204);
    }
}