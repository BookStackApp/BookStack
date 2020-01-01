<?php namespace BookStack\Http\Controllers\Api;

use BookStack\Entities\Book;

class BooksApiController extends ApiController
{
    public $validation = [
        'create' => [
            // TODO
        ],
        'update' => [
            // TODO
        ],
    ];

    /**
     * Get a listing of books visible to the user.
     */
    public function index()
    {
        $books = Book::visible();
        return $this->apiListingResponse($books, [
            'id', 'name', 'slug', 'description', 'created_at', 'updated_at', 'created_by', 'updated_by',
            'restricted', 'image_id',
        ]);
    }

    public function create()
    {
        // TODO -
    }

    public function read()
    {
        // TODO -
    }

    public function update()
    {
        // TODO -
    }

    public function delete()
    {
        // TODO -
    }
}