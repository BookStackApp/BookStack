<?php namespace BookStack\Http\Controllers\Api;

use BookStack\Entities\Book;
use BookStack\Entities\Chapter;
use BookStack\Entities\Repos\ChapterRepo;
use BookStack\Facades\Activity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;

class ChapterApiController extends ApiController
{
    protected $chapterRepo;

    protected $rules = [
        'create' => [
            'book_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'description' => 'string|max:1000',
            'tags' => 'array',
        ],
        'update' => [
            'book_id' => 'integer',
            'name' => 'string|min:1|max:255',
            'description' => 'string|max:1000',
            'tags' => 'array',
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
            'created_at', 'updated_at', 'created_by', 'updated_by',
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
        Activity::add($chapter, 'chapter_create', $book->id);

        return response()->json($chapter->load(['tags']));
    }

    /**
     * View the details of a single chapter.
     */
    public function read(string $id)
    {
        $chapter = Chapter::visible()->with(['tags', 'createdBy', 'updatedBy', 'pages' => function (HasMany $query) {
            $query->visible()->get(['id', 'name', 'slug']);
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
        Activity::add($chapter, 'chapter_update', $chapter->book->id);

        return response()->json($updatedChapter->load(['tags']));
    }

    /**
     * Delete a chapter from the system.
     */
    public function delete(string $id)
    {
        $chapter = Chapter::visible()->findOrFail($id);
        $this->checkOwnablePermission('chapter-delete', $chapter);

        $this->chapterRepo->destroy($chapter);
        Activity::addMessage('chapter_delete', $chapter->name, $chapter->book->id);

        return response('', 204);
    }
}
