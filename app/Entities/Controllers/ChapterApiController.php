<?php

namespace BookStack\Entities\Controllers;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Repos\ChapterRepo;
use BookStack\Http\Controllers\ApiController;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;

class ChapterApiController extends ApiController
{
    protected $chapterRepo;

    protected $rules = [
        'create' => [
            'book_id'     => ['required', 'integer'],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['string', 'max:1000'],
            'tags'        => ['array'],
        ],
        'update' => [
            'book_id'     => ['integer'],
            'name'        => ['string', 'min:1', 'max:255'],
            'description' => ['string', 'max:1000'],
            'tags'        => ['array'],
        ],
    ];

    /**
     * ChapterController constructor.
     */
    public function __construct(ChapterRepo $chapterRepo)
    {
        $this->chapterRepo = $chapterRepo;
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
        $this->validate($request, $this->rules['create']);

        $bookId = $request->get('book_id');
        $book = Book::visible()->findOrFail($bookId);
        $this->checkOwnablePermission('chapter-create', $book);

        $chapter = $this->chapterRepo->create($request->all(), $book);

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
     */
    public function update(Request $request, string $id)
    {
        $chapter = Chapter::visible()->findOrFail($id);
        $this->checkOwnablePermission('chapter-update', $chapter);

        $updatedChapter = $this->chapterRepo->update($chapter, $request->all());

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
