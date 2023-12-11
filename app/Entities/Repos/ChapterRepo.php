<?php

namespace BookStack\Entities\Repos;

use BookStack\Activity\ActivityType;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Tools\BookContents;
use BookStack\Entities\Tools\TrashCan;
use BookStack\Exceptions\MoveOperationException;
use BookStack\Exceptions\NotFoundException;
use BookStack\Exceptions\PermissionsException;
use BookStack\Facades\Activity;
use Exception;

class ChapterRepo
{
    public function __construct(
        protected BaseRepo $baseRepo
    ) {
    }

    /**
     * Get a chapter via the slug.
     *
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
        Activity::add(ActivityType::CHAPTER_CREATE, $chapter);

        return $chapter;
    }

    /**
     * Update the given chapter.
     */
    public function update(Chapter $chapter, array $input): Chapter
    {
        $this->baseRepo->update($chapter, $input);
        Activity::add(ActivityType::CHAPTER_UPDATE, $chapter);

        return $chapter;
    }

    /**
     * Remove a chapter from the system.
     *
     * @throws Exception
     */
    public function destroy(Chapter $chapter)
    {
        $trashCan = new TrashCan();
        $trashCan->softDestroyChapter($chapter);
        Activity::add(ActivityType::CHAPTER_DELETE, $chapter);
        $trashCan->autoClearOld();
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
        $parent = $this->findParentByIdentifier($parentIdentifier);
        if (is_null($parent)) {
            throw new MoveOperationException('Book to move chapter into not found');
        }

        if (!userCan('chapter-create', $parent)) {
            throw new PermissionsException('User does not have permission to create a chapter within the chosen book');
        }

        $chapter->changeBook($parent->id);
        $chapter->rebuildPermissions();
        Activity::add(ActivityType::CHAPTER_MOVE, $chapter);

        return $parent;
    }

    /**
     * Find a page parent entity via an identifier string in the format:
     * {type}:{id}
     * Example: (book:5).
     *
     * @throws MoveOperationException
     */
    public function findParentByIdentifier(string $identifier): ?Book
    {
        $stringExploded = explode(':', $identifier);
        $entityType = $stringExploded[0];
        $entityId = intval($stringExploded[1]);

        if ($entityType !== 'book') {
            throw new MoveOperationException('Chapters can only be in books');
        }

        return Book::visible()->where('id', '=', $entityId)->first();
    }
}
