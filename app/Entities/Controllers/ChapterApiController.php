<?php

namespace BookStack\Entities\Controllers;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Repos\ChapterRepo;
use BookStack\Exceptions\PermissionsException;
use BookStack\Http\ApiController;
use Exception;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;

class ChapterApiController extends ApiController
{
    protected $rules = [
        'create' => [
            'book_id'     => ['required', 'integer'],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['string', 'max:1000'],
            'tags'        => ['array'],
            'priority'    => ['integer'],
        ],
        'update' => [
            'book_id'     => ['integer'],
            'name'        => ['string', 'min:1', 'max:255'],
            'description' => ['string', 'max:1000'],
            'tags'        => ['array'],
            'priority'    => ['integer'],
        ],
    ];

    public function __construct(
        protected ChapterRepo $chapterRepo
    ) {
    }

    /**
     * Get a listing of chapters visible to the user.
     */
    public function list()
    {
        $chapters = Chapter::visible();

        return $this->apiListingResponse($chapters, [
            'id', 'book_id', 'name', 'slug', 'description', 'priority',
            'created_at', 'updated_at', 'created_by', 'updated_by', 'owned_by',
        ]);
    }

    /**
     * Create a new chapter in the system.
     */
    public function create(Request $request)
    {
        $requestData = $this->validate($request, $this->rules['create']);

        $bookId = $request->get('book_id');
        $book = Book::visible()->findOrFail($bookId);
        $this->checkOwnablePermission('chapter-create', $book);

        $chapter = $this->chapterRepo->create($requestData, $book);

        return response()->json($chapter->load(['tags']));
    }

    /**
     * View the details of a single chapter.
     */
    public function read(string $id)
    {
        $chapter = Chapter::visible()->with(['tags', 'createdBy', 'updatedBy', 'ownedBy', 'pages' => function (HasMany $query) {
            $query->scopes('visible')->get(['id', 'name', 'slug']);
        }])->findOrFail($id);

        return response()->json($chapter);
    }

    /**
     * Update the details of a single chapter.
     * Providing a 'book_id' property will essentially move the chapter
     * into that parent element if you have permissions to do so.
     */
    public function update(Request $request, string $id)
    {
        $requestData = $this->validate($request, $this->rules()['update']);
        $chapter = Chapter::visible()->findOrFail($id);
        $this->checkOwnablePermission('chapter-update', $chapter);

        if ($request->has('book_id') && $chapter->book_id !== intval($requestData['book_id'])) {
            $this->checkOwnablePermission('chapter-delete', $chapter);

            try {
                $this->chapterRepo->move($chapter, "book:{$requestData['book_id']}");
            } catch (Exception $exception) {
                if ($exception instanceof  PermissionsException) {
                    $this->showPermissionError();
                }

                return $this->jsonError(trans('errors.selected_book_not_found'));
            }
        }

        $updatedChapter = $this->chapterRepo->update($chapter, $requestData);

        return response()->json($updatedChapter->load(['tags']));
    }

    /**
     * Delete a chapter.
     * This will typically send the chapter to the recycle bin.
     */
    public function delete(string $id)
    {
        $chapter = Chapter::visible()->findOrFail($id);
        $this->checkOwnablePermission('chapter-delete', $chapter);

        $this->chapterRepo->destroy($chapter);

        return response('', 204);
    }
}
