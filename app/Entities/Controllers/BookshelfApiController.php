<?php

namespace BookStack\Entities\Controllers;

use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Queries\BookshelfQueries;
use BookStack\Entities\Repos\BookshelfRepo;
use BookStack\Http\ApiController;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BookshelfApiController extends ApiController
{
    public function __construct(
        protected BookshelfRepo $bookshelfRepo,
        protected BookshelfQueries $queries,
    ) {
    }

    /**
     * Get a listing of shelves visible to the user.
     */
    public function list()
    {
        $shelves = $this->queries
            ->visibleForList()
            ->with(['cover:id,name,url'])
            ->addSelect(['created_by', 'updated_by']);

        return $this->apiListingResponse($shelves, [
            'id', 'name', 'slug', 'description', 'created_at', 'updated_at', 'created_by', 'updated_by', 'owned_by',
        ]);
    }

    /**
     * Create a new shelf in the system.
     * An array of books IDs can be provided in the request. These
     * will be added to the shelf in the same order as provided.
     * The cover image of a shelf can be set by sending a file via an 'image' property within a 'multipart/form-data' request.
     * If the 'image' property is null then the shelf cover image will be removed.
     *
     * @throws ValidationException
     */
    public function create(Request $request)
    {
        $this->checkPermission('bookshelf-create-all');
        $requestData = $this->validate($request, $this->rules()['create']);

        $bookIds = $request->get('books', []);
        $shelf = $this->bookshelfRepo->create($requestData, $bookIds);

        return response()->json($this->forJsonDisplay($shelf));
    }

    /**
     * View the details of a single shelf.
     */
    public function read(string $id)
    {
        $shelf = $this->queries->findVisibleByIdOrFail(intval($id));
        $shelf = $this->forJsonDisplay($shelf);
        $shelf->load([
            'createdBy', 'updatedBy', 'ownedBy',
            'books' => function (BelongsToMany $query) {
                $query->scopes('visible')->get(['id', 'name', 'slug']);
            },
        ]);

        return response()->json($shelf);
    }

    /**
     * Update the details of a single shelf.
     * An array of books IDs can be provided in the request. These
     * will be added to the shelf in the same order as provided and overwrite
     * any existing book assignments.
     * The cover image of a shelf can be set by sending a file via an 'image' property within a 'multipart/form-data' request.
     * If the 'image' property is null then the shelf cover image will be removed.
     *
     * @throws ValidationException
     */
    public function update(Request $request, string $id)
    {
        $shelf = $this->queries->findVisibleByIdOrFail(intval($id));
        $this->checkOwnablePermission('bookshelf-update', $shelf);

        $requestData = $this->validate($request, $this->rules()['update']);
        $bookIds = $request->get('books', null);

        $shelf = $this->bookshelfRepo->update($shelf, $requestData, $bookIds);

        return response()->json($this->forJsonDisplay($shelf));
    }

    /**
     * Delete a single shelf.
     * This will typically send the shelf to the recycle bin.
     *
     * @throws Exception
     */
    public function delete(string $id)
    {
        $shelf = $this->queries->findVisibleByIdOrFail(intval($id));
        $this->checkOwnablePermission('bookshelf-delete', $shelf);

        $this->bookshelfRepo->destroy($shelf);

        return response('', 204);
    }

    protected function forJsonDisplay(Bookshelf $shelf): Bookshelf
    {
        $shelf = clone $shelf;
        $shelf->unsetRelations()->refresh();

        $shelf->load(['tags', 'cover']);
        $shelf->makeVisible('description_html')
            ->setAttribute('description_html', $shelf->descriptionHtml());

        return $shelf;
    }

    protected function rules(): array
    {
        return [
            'create' => [
                'name'             => ['required', 'string', 'max:255'],
                'description'      => ['string', 'max:1900'],
                'description_html' => ['string', 'max:2000'],
                'books'            => ['array'],
                'tags'             => ['array'],
                'image'            => array_merge(['nullable'], $this->getImageValidationRules()),
            ],
            'update' => [
                'name'             => ['string', 'min:1', 'max:255'],
                'description'      => ['string', 'max:1900'],
                'description_html' => ['string', 'max:2000'],
                'books'            => ['array'],
                'tags'             => ['array'],
                'image'            => array_merge(['nullable'], $this->getImageValidationRules()),
            ],
        ];
    }
}
