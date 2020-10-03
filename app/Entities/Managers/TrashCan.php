<?php namespace BookStack\Entities\Managers;

use BookStack\Entities\Book;
use BookStack\Entities\Bookshelf;
use BookStack\Entities\Chapter;
use BookStack\Entities\Deletion;
use BookStack\Entities\Entity;
use BookStack\Entities\EntityProvider;
use BookStack\Entities\HasCoverImage;
use BookStack\Entities\Page;
use BookStack\Exceptions\NotifyException;
use BookStack\Facades\Activity;
use BookStack\Uploads\AttachmentService;
use BookStack\Uploads\ImageService;
use Exception;

class TrashCan
{

    /**
     * Send a shelf to the recycle bin.
     */
    public function softDestroyShelf(Bookshelf $shelf)
    {
        Deletion::createForEntity($shelf);
        $shelf->delete();
    }

    /**
     * Send a book to the recycle bin.
     * @throws Exception
     */
    public function softDestroyBook(Book $book)
    {
        Deletion::createForEntity($book);

        foreach ($book->pages as $page) {
            $this->softDestroyPage($page, false);
        }

        foreach ($book->chapters as $chapter) {
            $this->softDestroyChapter($chapter, false);
        }

        $book->delete();
    }

    /**
     * Send a chapter to the recycle bin.
     * @throws Exception
     */
    public function softDestroyChapter(Chapter $chapter, bool $recordDelete = true)
    {
        if ($recordDelete) {
            Deletion::createForEntity($chapter);
        }

        if (count($chapter->pages) > 0) {
            foreach ($chapter->pages as $page) {
                $this->softDestroyPage($page, false);
            }
        }

        $chapter->delete();
    }

    /**
     * Send a page to the recycle bin.
     * @throws Exception
     */
    public function softDestroyPage(Page $page, bool $recordDelete = true)
    {
        if ($recordDelete) {
            Deletion::createForEntity($page);
        }

        // Check if set as custom homepage & remove setting if not used or throw error if active
        $customHome = setting('app-homepage', '0:');
        if (intval($page->id) === intval(explode(':', $customHome)[0])) {
            if (setting('app-homepage-type') === 'page') {
                throw new NotifyException(trans('errors.page_custom_home_deletion'), $page->getUrl());
            }
            setting()->remove('app-homepage');
        }

        $page->delete();
    }

    /**
     * Remove a bookshelf from the system.
     * @throws Exception
     */
    public function destroyShelf(Bookshelf $shelf)
    {
        $this->destroyCommonRelations($shelf);
        $shelf->forceDelete();
    }

    /**
     * Remove a book from the system.
     * Destroys any child chapters and pages.
     * @throws Exception
     */
    public function destroyBook(Book $book)
    {
        $pages = $book->pages()->withTrashed()->get();
        foreach ($pages as $page) {
            $this->destroyPage($page);
        }

        $chapters = $book->chapters()->withTrashed()->get();
        foreach ($chapters as $chapter) {
            $this->destroyChapter($chapter);
        }

        $this->destroyCommonRelations($book);
        $book->forceDelete();
    }

    /**
     * Remove a chapter from the system.
     * Destroys all pages within.
     * @throws Exception
     */
    public function destroyChapter(Chapter $chapter)
    {
        $pages = $chapter->pages()->withTrashed()->get();
        if (count($pages)) {
            foreach ($pages as $page) {
                $this->destroyPage($page);
            }
        }

        $this->destroyCommonRelations($chapter);
        $chapter->forceDelete();
    }

    /**
     * Remove a page from the system.
     * @throws Exception
     */
    public function destroyPage(Page $page)
    {
        $this->destroyCommonRelations($page);

        // Delete Attached Files
        $attachmentService = app(AttachmentService::class);
        foreach ($page->attachments as $attachment) {
            $attachmentService->deleteFile($attachment);
        }

        $page->forceDelete();
    }

    /**
     * Get the total counts of those that have been trashed
     * but not yet fully deleted (In recycle bin).
     */
    public function getTrashedCounts(): array
    {
        $provider = app(EntityProvider::class);
        $counts = [];

        /** @var Entity $instance */
        foreach ($provider->all() as $key => $instance) {
            $counts[$key] = $instance->newQuery()->onlyTrashed()->count();
        }

        return $counts;
    }

    /**
     * Destroy all items that have pending deletions.
     */
    public function destroyFromAllDeletions()
    {
        $deletions = Deletion::all();
        foreach ($deletions as $deletion) {
            // For each one we load in the relation since it may have already
            // been deleted as part of another deletion in this loop.
            $entity = $deletion->deletable()->first();
            if ($entity) {
                $this->destroyEntity($deletion->deletable);
            }
            $deletion->delete();
        }
    }

    /**
     * Destroy the given entity.
     */
    protected function destroyEntity(Entity $entity)
    {
        if ($entity->isA('page')) {
            return $this->destroyPage($entity);
        }
        if ($entity->isA('chapter')) {
            return $this->destroyChapter($entity);
        }
        if ($entity->isA('book')) {
            return $this->destroyBook($entity);
        }
        if ($entity->isA('shelf')) {
            return $this->destroyShelf($entity);
        }
    }

    /**
     * Update entity relations to remove or update outstanding connections.
     */
    protected function destroyCommonRelations(Entity $entity)
    {
        Activity::removeEntity($entity);
        $entity->views()->delete();
        $entity->permissions()->delete();
        $entity->tags()->delete();
        $entity->comments()->delete();
        $entity->jointPermissions()->delete();
        $entity->searchTerms()->delete();
        $entity->deletions()->delete();

        if ($entity instanceof HasCoverImage && $entity->cover) {
            $imageService = app()->make(ImageService::class);
            $imageService->destroy($entity->cover);
        }
    }
}
