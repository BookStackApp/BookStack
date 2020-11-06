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
    protected function destroyShelf(Bookshelf $shelf): int
    {
        $this->destroyCommonRelations($shelf);
        $shelf->forceDelete();
        return 1;
    }

    /**
     * Remove a book from the system.
     * Destroys any child chapters and pages.
     * @throws Exception
     */
    protected function destroyBook(Book $book): int
    {
        $count = 0;
        $pages = $book->pages()->withTrashed()->get();
        foreach ($pages as $page) {
            $this->destroyPage($page);
            $count++;
        }

        $chapters = $book->chapters()->withTrashed()->get();
        foreach ($chapters as $chapter) {
            $this->destroyChapter($chapter);
            $count++;
        }

        $this->destroyCommonRelations($book);
        $book->forceDelete();
        return $count + 1;
    }

    /**
     * Remove a chapter from the system.
     * Destroys all pages within.
     * @throws Exception
     */
    protected function destroyChapter(Chapter $chapter): int
    {
        $count = 0;
        $pages = $chapter->pages()->withTrashed()->get();
        if (count($pages)) {
            foreach ($pages as $page) {
                $this->destroyPage($page);
                $count++;
            }
        }

        $this->destroyCommonRelations($chapter);
        $chapter->forceDelete();
        return $count + 1;
    }

    /**
     * Remove a page from the system.
     * @throws Exception
     */
    protected function destroyPage(Page $page): int
    {
        $this->destroyCommonRelations($page);

        // Delete Attached Files
        $attachmentService = app(AttachmentService::class);
        foreach ($page->attachments as $attachment) {
            $attachmentService->deleteFile($attachment);
        }

        $page->forceDelete();
        return 1;
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
     * @throws Exception
     */
    public function empty(): int
    {
        $deletions = Deletion::all();
        $deleteCount = 0;
        foreach ($deletions as $deletion) {
            $deleteCount += $this->destroyFromDeletion($deletion);
        }
        return $deleteCount;
    }

    /**
     * Destroy an element from the given deletion model.
     * @throws Exception
     */
    public function destroyFromDeletion(Deletion $deletion): int
    {
        // We directly load the deletable element here just to ensure it still
        // exists in the event it has already been destroyed during this request.
        $entity = $deletion->deletable()->first();
        $count = 0;
        if ($entity) {
            $count = $this->destroyEntity($deletion->deletable);
        }
        $deletion->delete();
        return $count;
    }

    /**
     * Restore the content within the given deletion.
     * @throws Exception
     */
    public function restoreFromDeletion(Deletion $deletion): int
    {
        $shouldRestore = true;
        $restoreCount = 0;
        $parent = $deletion->deletable->getParent();

        if ($parent && $parent->trashed()) {
            $shouldRestore = false;
        }

        if ($shouldRestore) {
            $restoreCount = $this->restoreEntity($deletion->deletable);
        }

        $deletion->delete();
        return $restoreCount;
    }

    /**
     * Restore an entity so it is essentially un-deleted.
     * Deletions on restored child elements will be removed during this restoration.
     */
    protected function restoreEntity(Entity $entity): int
    {
        $count = 1;
        $entity->restore();

        $restoreAction = function ($entity) use (&$count) {
            if ($entity->deletions_count > 0) {
                $entity->deletions()->delete();
            }

            $entity->restore();
            $count++;
        };

        if ($entity->isA('chapter') || $entity->isA('book')) {
            $entity->pages()->withTrashed()->withCount('deletions')->get()->each($restoreAction);
        }

        if ($entity->isA('book')) {
            $entity->chapters()->withTrashed()->withCount('deletions')->get()->each($restoreAction);
        }

        return $count;
    }

    /**
     * Destroy the given entity.
     */
    protected function destroyEntity(Entity $entity): int
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
