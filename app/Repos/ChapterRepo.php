<?php namespace BookStack\Repos;


use Activity;
use BookStack\Book;
use BookStack\Exceptions\NotFoundException;
use Illuminate\Support\Str;
use BookStack\Chapter;

class ChapterRepo extends EntityRepo
{
    protected $pageRepo;

    /**
     * ChapterRepo constructor.
     * @param $pageRepo
     */
    public function __construct(PageRepo $pageRepo)
    {
        $this->pageRepo = $pageRepo;
        parent::__construct();
    }

    /**
     * Get the child items for a chapter
     * @param Chapter $chapter
     */
    public function getChildren(Chapter $chapter)
    {
        $pages = $this->permissionService->enforcePageRestrictions($chapter->pages())->get();
        // Sort items with drafts first then by priority.
        return $pages->sortBy(function ($child, $key) {
            $score = $child->priority;
            if ($child->draft) $score -= 100;
            return $score;
        });
    }

    /**
     * Create a new chapter from request input.
     * @param $input
     * @param Book $book
     * @return Chapter
     */
    public function createFromInput($input, Book $book)
    {
        $chapter = $this->chapter->newInstance($input);
        $chapter->slug = $this->findSuitableSlug('chapter', $chapter->name, false, $book->id);
        $chapter->created_by = user()->id;
        $chapter->updated_by = user()->id;
        $chapter = $book->chapters()->save($chapter);
        $this->permissionService->buildJointPermissionsForEntity($chapter);
        return $chapter;
    }

    /**
     * Destroy a chapter and its relations by providing its slug.
     * @param Chapter $chapter
     */
    public function destroy(Chapter $chapter)
    {
        if (count($chapter->pages) > 0) {
            foreach ($chapter->pages as $page) {
                $page->chapter_id = 0;
                $page->save();
            }
        }
        Activity::removeEntity($chapter);
        $chapter->views()->delete();
        $chapter->permissions()->delete();
        $this->permissionService->deleteJointPermissionsForEntity($chapter);
        $chapter->delete();
    }


    /**
     * Get a new priority value for a new page to be added
     * to the given chapter.
     * @param Chapter $chapter
     * @return int
     */
    public function getNewPriority(Chapter $chapter)
    {
        $lastPage = $chapter->pages->last();
        return $lastPage !== null ? $lastPage->priority + 1 : 0;
    }

    /**
     * Changes the book relation of this chapter.
     * @param $bookId
     * @param Chapter $chapter
     * @param bool $rebuildPermissions
     * @return Chapter
     */
    public function changeBook($bookId, Chapter $chapter, $rebuildPermissions = false)
    {
        $chapter->book_id = $bookId;
        // Update related activity
        foreach ($chapter->activity as $activity) {
            $activity->book_id = $bookId;
            $activity->save();
        }
        $chapter->slug = $this->findSuitableSlug('chapter', $chapter->name, $chapter->id, $bookId);
        $chapter->save();
        // Update all child pages
        foreach ($chapter->pages as $page) {
            $this->pageRepo->changeBook($bookId, $page);
        }

        // Update permissions if applicable
        if ($rebuildPermissions) {
            $chapter->load('book');
            $this->permissionService->buildJointPermissionsForEntity($chapter->book);
        }

        return $chapter;
    }

}