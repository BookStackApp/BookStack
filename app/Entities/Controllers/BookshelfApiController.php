<?php

namespace BookStack\Entities\Controllers;

use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Repos\BookshelfRepo;
use BookStack\Http\Controllers\ApiController;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BookshelfApiController extends ApiController
{
    protected BookshelfRepo $bookshelfRepo;

    public function __construct(BookshelfRepo $bookshelfRepo)
    {
        $this->bookshelfRepo = $bookshelfRepo;
    }

    /**
     * Get a listing of shelves visible to the user.
     */
    public function list()
    {
        $shelves = Bookshelf::visible();

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

        return response()->json($shelf);
    }

    /**
     * View the details of a single shelf.
     */
    public function read(string $id)
    {
        $shelf = Bookshelf::visible()->with([
            'tags', 'cover', 'createdBy', 'updatedBy', 'ownedBy',
            'books' => function (BelongsToMany $query) {
                $query->scopes('visible')->get(['id', 'name', 'slug']);
            },
        ])->findOrFail($id);

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
        $shelf = Bookshelf::visible()->findOrFail($id);
        $this->checkOwnablePermission('bookshelf-update', $shelf);

        $requestData = $this->validate($request, $this->rules()['update']);
        $bookIds = $request->get('books', null);

        $shelf = $this->bookshelfRepo->update($shelf, $requestData, $bookIds);

        return response()->json($shelf);
    }

    /**
     * Delete a single shelf.
     * This will typically send the shelf to the recycle bin.
     *
     * @throws Exception
     */
    public function delete(string $id)
    {
        $shelf = Bookshelf::visible()->findOrFail($id);
        $this->checkOwnablePermission('bookshelf-delete', $shelf);

        $this->bookshelfRepo->destroy($shelf);

        return response('', 204);
    }

    protected function rules(): array
    {
        return [
            'create' => [
                'name'        => ['required', 'string', 'max:255'],
                'description' => ['string', 'max:1000'],
                'books'       => ['array'],
                'tags'        => ['array'],
                'image'       => array_merge(['nullable'], $this->getImageValidationRules()),
            ],
            'update' => [
                'name'        => ['string', 'min:1', 'max:255'],
                'description' => ['string', 'max:1000'],
                'books'       => ['array'],
                'tags'        => ['array'],
                'image'       => array_merge(['nullable'], $this->getImageValidationRules()),
            ],
        ];
    }
}
