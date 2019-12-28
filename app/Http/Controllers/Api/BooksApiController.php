<?php namespace BookStack\Http\Controllers\Api;

use BookStack\Entities\Book;

class BooksApiController extends ApiController
{
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
}