<?php

namespace BookStack\Entities\Repos;

use BookStack\Activity\ActivityType;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Queries\EntityQueries;
use BookStack\Entities\Tools\BookContents;
use BookStack\Entities\Tools\TrashCan;
use BookStack\Exceptions\MoveOperationException;
use BookStack\Exceptions\PermissionsException;
use BookStack\Facades\Activity;
use BookStack\Util\DatabaseTransaction;
use Exception;

class ChapterRepo
{
    public function __construct(
        protected BaseRepo $baseRepo,
        protected EntityQueries $entityQueries,
        protected TrashCan $trashCan,
    ) {
    }

    /**
     * Create a new chapter in the system.
     */
    public function create(array $input, Book $parentBook): Chapter
    {
        return (new DatabaseTransaction(function () use ($input, $parentBook) {
            $chapter = new Chapter();
            $chapter->book_id = $parentBook->id;
            $chapter->priority = (new BookContents($parentBook))->getLastPriority() + 1;
            $this->baseRepo->create($chapter, $input);
            $this->baseRepo->updateDefaultTemplate($chapter, intval($input['default_template_id'] ?? null));
            Activity::add(ActivityType::CHAPTER_CREATE, $chapter);

            $this->baseRepo->sortParent($chapter);

            return $chapter;
        }))->run();
    }

    /**
     * Update the given chapter.
     */
    public function update(Chapter $chapter, array $input): Chapter
    {
        $this->baseRepo->update($chapter, $input);

        if (array_key_exists('default_template_id', $input)) {
            $this->baseRepo->updateDefaultTemplate($chapter, intval($input['default_template_id']));
        }

        Activity::add(ActivityType::CHAPTER_UPDATE, $chapter);

        $this->baseRepo->sortParent($chapter);

        return $chapter;
    }

    /**
     * Remove a chapter from the system.
     *
     * @throws Exception
     */
    public function destroy(Chapter $chapter)
    {
        $this->trashCan->softDestroyChapter($chapter);
        Activity::add(ActivityType::CHAPTER_DELETE, $chapter);
        $this->trashCan->autoClearOld();
    }

    /**
     * Move the given chapter into a new parent book.
     * The $parentIdentifier must be a string of the following format:
     * 'book:<id>' (book:5).
     *
     * @throws MoveOperationException
     * @throws PermissionsException
     */
    public function move(Chapter $chapter, string $parentIdentifier): Book
    {
        $parent = $this->entityQueries->findVisibleByStringIdentifier($parentIdentifier);
        if (!$parent instanceof Book) {
            throw new MoveOperationException('Book to move chapter into not found');
        }

        if (!userCan('chapter-create', $parent)) {
            throw new PermissionsException('User does not have permission to create a chapter within the chosen book');
        }

        return (new DatabaseTransaction(function () use ($chapter, $parent) {
            $chapter->changeBook($parent->id);
            $chapter->rebuildPermissions();
            Activity::add(ActivityType::CHAPTER_MOVE, $chapter);

            $this->baseRepo->sortParent($chapter);

            return $parent;
        }))->run();
    }
}
