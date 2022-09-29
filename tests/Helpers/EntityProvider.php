<?php

namespace Tests\Helpers;

use BookStack\Auth\Role;
use BookStack\Auth\User;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Repos\BookRepo;
use BookStack\Entities\Repos\BookshelfRepo;
use BookStack\Entities\Repos\ChapterRepo;
use BookStack\Entities\Repos\PageRepo;

class EntityProvider
{
    /**
     * @var array<string, int[]>
     */
    protected array $fetchCache = [
        'book' => [],
        'page' => [],
        'bookshelf' => [],
        'chapter' => [],
    ];

    /**
     * Get an un-fetched page from the system.
     */
    public function page(): Page
    {
        /** @var Page $page */
        $page = Page::query()->whereNotIn('id', $this->fetchCache['page'])->first();
        $this->addToCache($page);
        return $page;
    }

    /**
     * Get an un-fetched chapter from the system.
     */
    public function chapter(): Chapter
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->whereNotIn('id', $this->fetchCache['chapter'])->first();
        $this->addToCache($chapter);
        return $chapter;
    }

    /**
     * Get an un-fetched book from the system.
     */
    public function book(): Book
    {
        /** @var Book $book */
        $book = Book::query()->whereNotIn('id', $this->fetchCache['book'])->first();
        $this->addToCache($book);
        return $book;
    }

    /**
     * Get an un-fetched shelf from the system.
     */
    public function shelf(): Bookshelf
    {
        /** @var Bookshelf $shelf */
        $shelf = Bookshelf::query()->whereNotIn('id', $this->fetchCache['bookshelf'])->first();
        $this->addToCache($shelf);
        return $shelf;
    }

    /**
     * Get all entity types from the system.
     * @return array{page: Page, chapter: Chapter, book: Book, bookshelf: Bookshelf}
     */
    public function all(): array
    {
        return [
            'page'      => $this->page(),
            'chapter'   => $this->chapter(),
            'book'      => $this->book(),
            'bookshelf' => $this->shelf(),
        ];
    }

    /**
     * Create a book to page chain of entities that belong to a specific user.
     * @return array{book: Book, chapter: Chapter, page: Page}
     */
    public function createChainBelongingToUser(User $creatorUser, ?User $updaterUser = null): array
    {
        if (empty($updaterUser)) {
            $updaterUser = $creatorUser;
        }

        $userAttrs = ['created_by' => $creatorUser->id, 'owned_by' => $creatorUser->id, 'updated_by' => $updaterUser->id];
        /** @var Book $book */
        $book = Book::factory()->create($userAttrs);
        $chapter = Chapter::factory()->create(array_merge(['book_id' => $book->id], $userAttrs));
        $page = Page::factory()->create(array_merge(['book_id' => $book->id, 'chapter_id' => $chapter->id], $userAttrs));

        $book->rebuildPermissions();
        $this->addToCache([$page, $chapter, $book]);

        return compact('book', 'chapter', 'page');
    }

    /**
     * Create and return a new bookshelf.
     */
    public function newShelf(array $input = ['name' => 'test shelf', 'description' => 'My new test shelf']): Bookshelf
    {
        $shelf = app(BookshelfRepo::class)->create($input, []);
        $this->addToCache($shelf);
        return $shelf;
    }

    /**
     * Create and return a new book.
     */
    public function newBook(array $input = ['name' => 'test book', 'description' => 'My new test book']): Book
    {
        $book = app(BookRepo::class)->create($input);
        $this->addToCache($book);
        return $book;
    }

    /**
     * Create and return a new test chapter.
     */
    public function newChapter(array $input, Book $book): Chapter
    {
        $chapter = app(ChapterRepo::class)->create($input, $book);
        $this->addToCache($chapter);
        return $chapter;
    }

    /**
     * Create and return a new test page.
     */
    public function newPage(array $input = ['name' => 'test page', 'html' => 'My new test page']): Page
    {
        $book = $this->book();
        $pageRepo = app(PageRepo::class);
        $draftPage = $pageRepo->getNewDraftPage($book);
        $this->addToCache($draftPage);
        return $pageRepo->publishDraft($draftPage, $input);
    }

    /**
     * Regenerate the permission for an entity.
     * Centralised to manage clearing of cached elements between requests.
     */
    public function regenPermissions(Entity $entity): void
    {
        $entity->rebuildPermissions();
        $entity->load('jointPermissions');
    }

    /**
     * Set the given entity as having restricted permissions, and apply the given
     * permissions for the given roles.
     * @param string[] $actions
     * @param Role[] $roles
     */
    public function setPermissions(Entity $entity, array $actions = [], array $roles = []): void
    {
        $entity->restricted = true;
        $entity->permissions()->delete();

        $permissions = [];
        foreach ($actions as $action) {
            foreach ($roles as $role) {
                $permissions[] = [
                    'role_id' => $role->id,
                    'action'  => strtolower($action),
                ];
            }
        }

        $entity->permissions()->createMany($permissions);
        $entity->save();
        $entity->load('permissions');
        $this->regenPermissions($entity);
    }

    /**
     * @param Entity|Entity[] $entities
     */
    protected function addToCache($entities): void
    {
        if (!is_array($entities)) {
            $entities = [$entities];
        }

        foreach ($entities as $entity) {
            $this->fetchCache[$entity->getType()][] = $entity->id;
        }
    }
}
