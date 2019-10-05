<?php namespace BookStack\Entities\Managers;

use BookStack\Entities\Book;
use BookStack\Entities\Bookshelf;
use BookStack\Entities\Chapter;
use BookStack\Entities\Entity;
use BookStack\Entities\HasCoverImage;
use BookStack\Entities\Page;
use BookStack\Exceptions\NotifyException;
use BookStack\Facades\Activity;
use BookStack\Uploads\AttachmentService;
use BookStack\Uploads\ImageService;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;

class TrashCan
{

    /**
     * Remove a bookshelf from the system.
     * @throws Exception
     */
    public function destroyShelf(Bookshelf $shelf)
    {
        $this->destroyCommonRelations($shelf);
        $shelf->delete();
    }

    /**
     * Remove a book from the system.
     * @throws NotifyException
     * @throws BindingResolutionException
     */
    public function destroyBook(Book $book)
    {
        foreach ($book->pages as $page) {
            $this->destroyPage($page);
        }

        foreach ($book->chapters as $chapter) {
            $this->destroyChapter($chapter);
        }

        $this->destroyCommonRelations($book);
        $book->delete();
    }

    /**
     * Remove a page from the system.
     * @throws NotifyException
     */
    public function destroyPage(Page $page)
    {
        // Check if set as custom homepage & remove setting if not used or throw error if active
        $customHome = setting('app-homepage', '0:');
        if (intval($page->id) === intval(explode(':', $customHome)[0])) {
            if (setting('app-homepage-type') === 'page') {
                throw new NotifyException(trans('errors.page_custom_home_deletion'), $page->getUrl());
            }
            setting()->remove('app-homepage');
        }

        $this->destroyCommonRelations($page);

        // Delete Attached Files
        $attachmentService = app(AttachmentService::class);
        foreach ($page->attachments as $attachment) {
            $attachmentService->deleteFile($attachment);
        }

        $page->delete();
    }

    /**
     * Remove a chapter from the system.
     * @throws Exception
     */
    public function destroyChapter(Chapter $chapter)
    {
        if (count($chapter->pages) > 0) {
            foreach ($chapter->pages as $page) {
                $page->chapter_id = 0;
                $page->save();
            }
        }

        $this->destroyCommonRelations($chapter);
        $chapter->delete();
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

        if ($entity instanceof HasCoverImage && $entity->cover) {
            $imageService = app()->make(ImageService::class);
            $imageService->destroy($entity->cover);
        }
    }
}
