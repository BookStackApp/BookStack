<?php namespace BookStack\Entities\Repos;

use BookStack\Entities\Book;
use BookStack\Entities\Chapter;
use BookStack\Entities\Managers\BookContents;
use BookStack\Entities\Managers\TrashCan;
use BookStack\Exceptions\MoveOperationException;
use BookStack\Exceptions\NotFoundException;
use BookStack\Exceptions\NotifyException;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ChapterRepo
{

    protected $baseRepo;

    /**
     * ChapterRepo constructor.
     * @param $baseRepo
     */
    public function __construct(BaseRepo $baseRepo)
    {
        $this->baseRepo = $baseRepo;
    }

    /**
     * Get a chapter via the slug.
     * @throws NotFoundException
     */
    public function getBySlug(string $bookSlug, string $chapterSlug): Chapter
    {
        $chapter = Chapter::visible()->whereSlugs($bookSlug, $chapterSlug)->first();

        if ($chapter === null) {
            throw new NotFoundException(trans('errors.chapter_not_found'));
        }

        return $chapter;
    }

    /**
     * Create a new chapter in the system.
     */
    public function create(array $input, Book $parentBook): Chapter
    {
        $chapter = new Chapter();
        $chapter->book_id = $parentBook->id;
        $chapter->priority = (new BookContents($parentBook))->getLastPriority() + 1;
        $this->baseRepo->create($chapter, $input);
        return $chapter;
    }

    /**
     * Update the given chapter.
     */
    public function update(Chapter $chapter, array $input): Chapter
    {
        $this->baseRepo->update($chapter, $input);
        return $chapter;
    }

    /**
     * Update the permissions of a chapter.
     */
    public function updatePermissions(Chapter $chapter, bool $restricted, Collection $permissions = null)
    {
        $this->baseRepo->updatePermissions($chapter, $restricted, $permissions);
    }

    /**
     * Remove a chapter from the system.
     * @throws Exception
     */
    public function destroy(Chapter $chapter)
    {
        $trashCan = new TrashCan();
        $trashCan->destroyChapter($chapter);
    }

    /**
     * Move the given chapter into a new parent book.
     * The $parentIdentifier must be a string of the following format:
     * 'book:<id>' (book:5)
     * @throws MoveOperationException
     */
    public function move(Chapter $chapter, string $parentIdentifier): Book
    {
        $stringExploded = explode(':', $parentIdentifier);
        $entityType = $stringExploded[0];
        $entityId = intval($stringExploded[1]);

        if ($entityType !== 'book') {
            throw new MoveOperationException('Chapters can only be moved into books');
        }

        $parent = Book::visible()->where('id', '=', $entityId)->first();
        if ($parent === null) {
            throw new MoveOperationException('Book to move chapter into not found');
        }

        $chapter->changeBook($parent->id);
        $chapter->rebuildPermissions();
        return $parent;
    }
}
