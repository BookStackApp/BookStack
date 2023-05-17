<?php

namespace BookStack\Entities\Controllers;

use BookStack\Api\ApiEntityListFormatter;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Repos\BookRepo;
use BookStack\Entities\Tools\BookContents;
use BookStack\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BookApiController extends ApiController
{
    protected BookRepo $bookRepo;

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
     * The cover image of a book can be set by sending a file via an 'image' property within a 'multipart/form-data' request.
     * If the 'image' property is null then the book cover image will be removed.
     *
     * @throws ValidationException
     */
    public function create(Request $request)
    {
        $this->checkPermission('book-create-all');
        $requestData = $this->validate($request, $this->rules()['create']);

        $book = $this->bookRepo->create($requestData);

        return response()->json($book);
    }

    /**
     * View the details of a single book.
     * The response data will contain 'content' property listing the chapter and pages directly within, in
     * the same structure as you'd see within the BookStack interface when viewing a book. Top-level
     * contents will have a 'type' property to distinguish between pages & chapters.
     */
    public function read(string $id)
    {
        $book = Book::visible()->with(['tags', 'cover', 'createdBy', 'updatedBy', 'ownedBy'])->findOrFail($id);

        $contents = (new BookContents($book))->getTree(true, false)->all();
        $contentsApiData = (new ApiEntityListFormatter($contents))
            ->withType()
            ->withField('pages', function (Entity $entity) {
                if ($entity instanceof Chapter) {
                    return (new ApiEntityListFormatter($entity->pages->all()))->format();
                }
                return null;
            })->format();
        $book->setAttribute('contents', $contentsApiData);

        return response()->json($book);
    }

    /**
     * Update the details of a single book.
     * The cover image of a book can be set by sending a file via an 'image' property within a 'multipart/form-data' request.
     * If the 'image' property is null then the book cover image will be removed.
     *
     * @throws ValidationException
     */
    public function update(Request $request, string $id)
    {
        $book = Book::visible()->findOrFail($id);
        $this->checkOwnablePermission('book-update', $book);

        $requestData = $this->validate($request, $this->rules()['update']);
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

    protected function rules(): array
    {
        return [
            'create' => [
                'name'        => ['required', 'string', 'max:255'],
                'description' => ['string', 'max:1000'],
                'tags'        => ['array'],
                'image'       => array_merge(['nullable'], $this->getImageValidationRules()),
            ],
            'update' => [
                'name'        => ['string', 'min:1', 'max:255'],
                'description' => ['string', 'max:1000'],
                'tags'        => ['array'],
                'image'       => array_merge(['nullable'], $this->getImageValidationRules()),
            ],
        ];
    }
}
