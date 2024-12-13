<?php

namespace BookStack\Entities\Controllers;

use BookStack\Api\ApiEntityListFormatter;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Queries\BookQueries;
use BookStack\Entities\Queries\PageQueries;
use BookStack\Entities\Repos\BookRepo;
use BookStack\Entities\Tools\BookContents;
use BookStack\Http\ApiController;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BookApiController extends ApiController
{
    public function __construct(
        protected BookRepo $bookRepo,
        protected BookQueries $queries,
        protected PageQueries $pageQueries,
    ) {
    }

    /**
     * Get a listing of books visible to the user.
     */
    public function list()
    {
        $books = $this->queries
            ->visibleForList()
            ->with(['cover:id,name,url'])
            ->addSelect(['created_by', 'updated_by']);

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

        return response()->json($this->forJsonDisplay($book));
    }

    /**
     * View the details of a single book.
     * The response data will contain 'content' property listing the chapter and pages directly within, in
     * the same structure as you'd see within the BookStack interface when viewing a book. Top-level
     * contents will have a 'type' property to distinguish between pages & chapters.
     */
    public function read(string $id)
    {
        $book = $this->queries->findVisibleByIdOrFail(intval($id));
        $book = $this->forJsonDisplay($book);
        $book->load(['createdBy', 'updatedBy', 'ownedBy']);

        $contents = (new BookContents($book))->getTree(true, false)->all();
        $contentsApiData = (new ApiEntityListFormatter($contents))
            ->withType()
            ->withField('pages', function (Entity $entity) {
                if ($entity instanceof Chapter) {
                    $pages = $this->pageQueries->visibleForChapterList($entity->id)->get()->all();
                    return (new ApiEntityListFormatter($pages))->format();
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
        $book = $this->queries->findVisibleByIdOrFail(intval($id));
        $this->checkOwnablePermission('book-update', $book);

        $requestData = $this->validate($request, $this->rules()['update']);
        $book = $this->bookRepo->update($book, $requestData);

        return response()->json($this->forJsonDisplay($book));
    }

    /**
     * Delete a single book.
     * This will typically send the book to the recycle bin.
     *
     * @throws \Exception
     */
    public function delete(string $id)
    {
        $book = $this->queries->findVisibleByIdOrFail(intval($id));
        $this->checkOwnablePermission('book-delete', $book);

        $this->bookRepo->destroy($book);

        return response('', 204);
    }

    protected function forJsonDisplay(Book $book): Book
    {
        $book = clone $book;
        $book->unsetRelations()->refresh();

        $book->load(['tags', 'cover']);
        $book->makeVisible('description_html')
            ->setAttribute('description_html', $book->descriptionHtml());

        return $book;
    }

    protected function rules(): array
    {
        return [
            'create' => [
                'name'                => ['required', 'string', 'max:255'],
                'description'         => ['string', 'max:1900'],
                'description_html'    => ['string', 'max:2000'],
                'tags'                => ['array'],
                'image'               => array_merge(['nullable'], $this->getImageValidationRules()),
                'default_template_id' => ['nullable', 'integer'],
            ],
            'update' => [
                'name'                => ['string', 'min:1', 'max:255'],
                'description'         => ['string', 'max:1900'],
                'description_html'    => ['string', 'max:2000'],
                'tags'                => ['array'],
                'image'               => array_merge(['nullable'], $this->getImageValidationRules()),
                'default_template_id' => ['nullable', 'integer'],
            ],
        ];
    }
}
