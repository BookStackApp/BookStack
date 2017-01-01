<?php namespace BookStack\Repos;

use BookStack\Book;

class BookRepo extends EntityRepo
{
    protected $pageRepo;
    protected $chapterRepo;

    /**
     * BookRepo constructor.
     * @param PageRepo $pageRepo
     * @param ChapterRepo $chapterRepo
     */
    public function __construct(PageRepo $pageRepo, ChapterRepo $chapterRepo)
    {
        $this->pageRepo = $pageRepo;
        $this->chapterRepo = $chapterRepo;
        parent::__construct();
    }

    /**
     * Get a new book instance from request input.
     * @param array $input
     * @return Book
     */
    public function createFromInput($input)
    {
        $book = $this->book->newInstance($input);
        $book->slug = $this->findSuitableSlug('book', $book->name);
        $book->created_by = user()->id;
        $book->updated_by = user()->id;
        $book->save();
        $this->permissionService->buildJointPermissionsForEntity($book);
        return $book;
    }

    /**
     * Update the given book from user input.
     * @param Book $book
     * @param $input
     * @return Book
     */
    public function updateFromInput(Book $book, $input)
    {
        if ($book->name !== $input['name']) {
            $book->slug = $this->findSuitableSlug('book', $input['name'], $book->id);
        }
        $book->fill($input);
        $book->updated_by = user()->id;
        $book->save();
        $this->permissionService->buildJointPermissionsForEntity($book);
        return $book;
    }

    /**
     * Destroy the given book.
     * @param Book $book
     * @throws \Exception
     */
    public function destroy(Book $book)
    {
        foreach ($book->pages as $page) {
            $this->pageRepo->destroy($page);
        }
        foreach ($book->chapters as $chapter) {
            $this->chapterRepo->destroy($chapter);
        }
        $book->views()->delete();
        $book->permissions()->delete();
        $this->permissionService->deleteJointPermissionsForEntity($book);
        $book->delete();
    }

    /**
     * Get the next child element priority.
     * @param Book $book
     * @return int
     */
    public function getNewPriority($book)
    {
        $lastElem = $this->getChildren($book)->pop();
        return $lastElem ? $lastElem->priority + 1 : 0;
    }

    /**
     * Get all child objects of a book.
     * Returns a sorted collection of Pages and Chapters.
     * Loads the book slug onto child elements to prevent access database access for getting the slug.
     * @param Book $book
     * @param bool $filterDrafts
     * @return mixed
     */
    public function getChildren(Book $book, $filterDrafts = false)
    {
        $q = $this->permissionService->bookChildrenQuery($book->id, $filterDrafts);
        $entities = [];
        $parents = [];
        $tree = [];

        foreach ($q as $index => $rawEntity) {
            if ($rawEntity->entity_type === 'Bookstack\\Page') {
                $entities[$index] = $this->page->newFromBuilder($rawEntity);
            } else if ($rawEntity->entity_type === 'Bookstack\\Chapter') {
                $entities[$index] = $this->chapter->newFromBuilder($rawEntity);
                $key = $entities[$index]->entity_type . ':' . $entities[$index]->id;
                $parents[$key] = $entities[$index];
                $parents[$key]->setAttribute('pages', collect());
            }
            if ($entities[$index]->chapter_id === 0) $tree[] = $entities[$index];
            $entities[$index]->book = $book;
        }

        foreach ($entities as $entity) {
            if ($entity->chapter_id === 0) continue;
            $parentKey = 'Bookstack\\Chapter:' . $entity->chapter_id;
            $chapter = $parents[$parentKey];
            $chapter->pages->push($entity);
        }

        return collect($tree);
    }

}