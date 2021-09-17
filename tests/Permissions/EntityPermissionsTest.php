<?php

namespace Tests\Permissions;

use BookStack\Auth\User;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use Illuminate\Support\Str;
use Tests\TestCase;

class EntityPermissionsTest extends TestCase
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var User
     */
    protected $viewer;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->getEditor();
        $this->viewer = $this->getViewer();
    }

    protected function setRestrictionsForTestRoles(Entity $entity, array $actions = [])
    {
        $roles = [
            $this->user->roles->first(),
            $this->viewer->roles->first(),
        ];
        $this->setEntityRestrictions($entity, $actions, $roles);
    }

    public function test_bookshelf_view_restriction()
    {
        /** @var Bookshelf $shelf */
        $shelf = Bookshelf::query()->first();

        $this->actingAs($this->user)
            ->get($shelf->getUrl())
            ->assertStatus(200);

        $this->setRestrictionsForTestRoles($shelf, []);

        $this->followingRedirects()->get($shelf->getUrl())
            ->assertSee('Bookshelf not found');

        $this->setRestrictionsForTestRoles($shelf, ['view']);

        $this->get($shelf->getUrl())
            ->assertSee($shelf->name);
    }

    public function test_bookshelf_update_restriction()
    {
        /** @var Bookshelf $shelf */
        $shelf = Bookshelf::query()->first();

        $this->actingAs($this->user)
            ->get($shelf->getUrl('/edit'))
            ->assertSee('Edit Book');

        $this->setRestrictionsForTestRoles($shelf, ['view', 'delete']);

        $resp = $this->get($shelf->getUrl('/edit'))
            ->assertRedirect('/');
        $this->followRedirects($resp)->assertSee('You do not have permission');

        $this->setRestrictionsForTestRoles($shelf, ['view', 'update']);

        $this->get($shelf->getUrl('/edit'))
            ->assertOk();
    }

    public function test_bookshelf_delete_restriction()
    {
        /** @var Bookshelf $shelf */
        $shelf = Bookshelf::query()->first();

        $this->actingAs($this->user)
            ->get($shelf->getUrl('/delete'))
            ->assertSee('Delete Book');

        $this->setRestrictionsForTestRoles($shelf, ['view', 'update']);

        $this->get($shelf->getUrl('/delete'))->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');

        $this->setRestrictionsForTestRoles($shelf, ['view', 'delete']);

        $this->get($shelf->getUrl('/delete'))
            ->assertOk()
            ->assertSee('Delete Book');
    }

    public function test_book_view_restriction()
    {
        /** @var Book $book */
        $book = Book::query()->first();
        $bookPage = $book->pages->first();
        $bookChapter = $book->chapters->first();

        $bookUrl = $book->getUrl();
        $this->actingAs($this->user)
            ->get($bookUrl)
            ->assertOk();

        $this->setRestrictionsForTestRoles($book, []);

        $this->followingRedirects()->get($bookUrl)
            ->assertSee('Book not found');
        $this->followingRedirects()->get($bookPage->getUrl())
            ->assertSee('Page not found');
        $this->followingRedirects()->get($bookChapter->getUrl())
            ->assertSee('Chapter not found');

        $this->setRestrictionsForTestRoles($book, ['view']);

        $this->get($bookUrl)
            ->assertSee($book->name);
        $this->get($bookPage->getUrl())
            ->assertSee($bookPage->name);
        $this->get($bookChapter->getUrl())
            ->assertSee($bookChapter->name);
    }

    public function test_book_create_restriction()
    {
        /** @var Book $book */
        $book = Book::query()->first();

        $bookUrl = $book->getUrl();
        $this->actingAs($this->viewer)
            ->get($bookUrl)
            ->assertElementNotContains('.actions', 'New Page')
            ->assertElementNotContains('.actions', 'New Chapter');
        $this->actingAs($this->user)
            ->get($bookUrl)
            ->assertElementContains('.actions', 'New Page')
            ->assertElementContains('.actions', 'New Chapter');

        $this->setRestrictionsForTestRoles($book, ['view', 'delete', 'update']);

        $this->get($bookUrl . '/create-chapter')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');

        $this->get($bookUrl . '/create-page')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');

        $this->get($bookUrl)
            ->assertElementNotContains('.actions', 'New Page')
            ->assertElementNotContains('.actions', 'New Chapter');

        $this->setRestrictionsForTestRoles($book, ['view', 'create']);

        $resp = $this->post($book->getUrl('/create-chapter'), [
            'name' => 'test chapter',
            'description' => 'desc',
        ]);
        $resp->assertRedirect($book->getUrl('/chapter/test-chapter'));


        $this->get($book->getUrl('/create-page'));
        /** @var Page $page */
        $page = Page::query()->where('draft', '=', true)->orderBy('id', 'desc')->first();
        $resp = $this->post($page->getUrl(), [
            'name' => 'test page',
            'html' => 'test content',
        ]);
        $resp->assertRedirect($book->getUrl('/page/test-page'));

        $this->get($bookUrl)
            ->assertElementContains('.actions', 'New Page')
            ->assertElementContains('.actions', 'New Chapter');
    }

    public function test_book_update_restriction()
    {
        /** @var Book $book */
        $book = Book::query()->first();
        $bookPage = $book->pages->first();
        $bookChapter = $book->chapters->first();

        $bookUrl = $book->getUrl();
        $this->actingAs($this->user)
            ->get($bookUrl . '/edit')
            ->assertSee('Edit Book');

        $this->setRestrictionsForTestRoles($book, ['view', 'delete']);

        $this->get($bookUrl . '/edit')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');
        $this->get($bookPage->getUrl() . '/edit')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');
        $this->get($bookChapter->getUrl() . '/edit')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');

        $this->setRestrictionsForTestRoles($book, ['view', 'update']);

        $this->get($bookUrl . '/edit')->assertOk();
        $this->get($bookPage->getUrl() . '/edit')->assertOk();
        $this->get($bookChapter->getUrl() . '/edit')->assertSee('Edit Chapter');
    }

    public function test_book_delete_restriction()
    {
        /** @var Book $book */
        $book = Book::query()->first();
        $bookPage = $book->pages->first();
        $bookChapter = $book->chapters->first();

        $bookUrl = $book->getUrl();
        $this->actingAs($this->user)->get($bookUrl . '/delete')
            ->assertSee('Delete Book');

        $this->setRestrictionsForTestRoles($book, ['view', 'update']);

        $this->get($bookUrl . '/delete')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');
        $this->get($bookPage->getUrl() . '/delete')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');
        $this->get($bookChapter->getUrl() . '/delete')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');

        $this->setRestrictionsForTestRoles($book, ['view', 'delete']);

        $this->get($bookUrl . '/delete')->assertOk()->assertSee('Delete Book');
        $this->get($bookPage->getUrl('/delete'))->assertOk()->assertSee('Delete Page');
        $this->get($bookChapter->getUrl('/delete'))->assertSee('Delete Chapter');
    }

    public function test_chapter_view_restriction()
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->first();
        $chapterPage = $chapter->pages->first();

        $chapterUrl = $chapter->getUrl();
        $this->actingAs($this->user)->get($chapterUrl)->assertOk();

        $this->setRestrictionsForTestRoles($chapter, []);

        $this->followingRedirects()->get($chapterUrl)->assertSee('Chapter not found');
        $this->followingRedirects()->get($chapterPage->getUrl())->assertSee('Page not found');

        $this->setRestrictionsForTestRoles($chapter, ['view']);

        $this->get($chapterUrl)->assertSee($chapter->name);
        $this->get($chapterPage->getUrl())->assertSee($chapterPage->name);
    }

    public function test_chapter_create_restriction()
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->first();

        $chapterUrl = $chapter->getUrl();
        $this->actingAs($this->user)
            ->get($chapterUrl)
            ->assertElementContains('.actions', 'New Page');

        $this->setRestrictionsForTestRoles($chapter, ['view', 'delete', 'update']);

        $this->get($chapterUrl . '/create-page')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');
        $this->get($chapterUrl)->assertElementNotContains('.actions', 'New Page');

        $this->setRestrictionsForTestRoles($chapter, ['view', 'create']);

        $this->get($chapter->getUrl('/create-page'));
        /** @var Page $page */
        $page = Page::query()->where('draft', '=', true)->orderBy('id', 'desc')->first();
        $resp = $this->post($page->getUrl(), [
            'name' => 'test page',
            'html' => 'test content',
        ]);
        $resp->assertRedirect($chapter->book->getUrl('/page/test-page'));

        $this->get($chapterUrl)->assertElementContains('.actions', 'New Page');
    }

    public function test_chapter_update_restriction()
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->first();
        $chapterPage = $chapter->pages->first();

        $chapterUrl = $chapter->getUrl();
        $this->actingAs($this->user)->get($chapterUrl . '/edit')
            ->assertSee('Edit Chapter');

        $this->setRestrictionsForTestRoles($chapter, ['view', 'delete']);

        $this->get($chapterUrl . '/edit')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');
        $this->get($chapterPage->getUrl() . '/edit')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');

        $this->setRestrictionsForTestRoles($chapter, ['view', 'update']);

        $this->get($chapterUrl . '/edit')->assertOk()->assertSee('Edit Chapter');
        $this->get($chapterPage->getUrl() . '/edit')->assertOk();
    }

    public function test_chapter_delete_restriction()
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->first();
        $chapterPage = $chapter->pages->first();

        $chapterUrl = $chapter->getUrl();
        $this->actingAs($this->user)
            ->get($chapterUrl . '/delete')
            ->assertSee('Delete Chapter');

        $this->setRestrictionsForTestRoles($chapter, ['view', 'update']);

        $this->get($chapterUrl . '/delete')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');
        $this->get($chapterPage->getUrl() . '/delete')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');

        $this->setRestrictionsForTestRoles($chapter, ['view', 'delete']);

        $this->get($chapterUrl . '/delete')->assertOk()->assertSee('Delete Chapter');
        $this->get($chapterPage->getUrl() . '/delete')->assertOk()->assertSee('Delete Page');
    }

    public function test_page_view_restriction()
    {
        /** @var Page $page */
        $page = Page::query()->first();

        $pageUrl = $page->getUrl();
        $this->actingAs($this->user)->get($pageUrl)->assertOk();

        $this->setRestrictionsForTestRoles($page, ['update', 'delete']);

        $this->get($pageUrl)->assertSee('Page not found');

        $this->setRestrictionsForTestRoles($page, ['view']);

        $this->get($pageUrl)->assertSee($page->name);
    }

    public function test_page_update_restriction()
    {
        /** @var Page $page */
        $page = Page::query()->first();

        $pageUrl = $page->getUrl();
        $this->actingAs($this->user)
            ->get($pageUrl . '/edit')
            ->assertElementExists('input[name="name"][value="' . $page->name . '"]');

        $this->setRestrictionsForTestRoles($page, ['view', 'delete']);

        $this->get($pageUrl . '/edit')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');

        $this->setRestrictionsForTestRoles($page, ['view', 'update']);

        $this->get($pageUrl . '/edit')
            ->assertOk()
            ->assertElementExists('input[name="name"][value="' . $page->name . '"]');
    }

    public function test_page_delete_restriction()
    {
        /** @var Page $page */
        $page = Page::query()->first();

        $pageUrl = $page->getUrl();
        $this->actingAs($this->user)
            ->get($pageUrl . '/delete')
            ->assertSee('Delete Page');

        $this->setRestrictionsForTestRoles($page, ['view', 'update']);

        $this->get($pageUrl . '/delete')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');

        $this->setRestrictionsForTestRoles($page, ['view', 'delete']);

        $this->get($pageUrl . '/delete')->assertOk()->assertSee('Delete Page');
    }

    protected function entityRestrictionFormTest(string $model, string $title, string $permission, string $roleId)
    {
        /** @var Entity $modelInstance */
        $modelInstance = $model::query()->first();
        $this->asAdmin()->get($modelInstance->getUrl('/permissions'))
            ->assertSee($title);

        $this->put($modelInstance->getUrl('/permissions'), [
            'restricted' => 'true',
            'restrictions' => [
                $roleId => [
                    $permission => 'true'
                ]
            ],
        ]);

        $this->assertDatabaseHas($modelInstance->getTable(), ['id' => $modelInstance->id, 'restricted' => true]);
        $this->assertDatabaseHas('entity_permissions', [
            'restrictable_id'   => $modelInstance->id,
            'restrictable_type' => $modelInstance->getMorphClass(),
            'role_id'           => $roleId,
            'action'            => $permission,
        ]);
    }

    public function test_bookshelf_restriction_form()
    {
        $this->entityRestrictionFormTest(Bookshelf::class, 'Bookshelf Permissions', 'view', '2');
    }

    public function test_book_restriction_form()
    {
        $this->entityRestrictionFormTest(Book::class, 'Book Permissions', 'view', '2');
    }

    public function test_chapter_restriction_form()
    {
        $this->entityRestrictionFormTest(Chapter::class, 'Chapter Permissions', 'update', '2');
    }

    public function test_page_restriction_form()
    {
        $this->entityRestrictionFormTest(Page::class, 'Page Permissions', 'delete', '2');
    }

    public function test_restricted_pages_not_visible_in_book_navigation_on_pages()
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->first();
        $page = $chapter->pages->first();
        $page2 = $chapter->pages[2];

        $this->setRestrictionsForTestRoles($page, []);

        $this->actingAs($this->user)
            ->get($page2->getUrl())
            ->assertElementNotContains('.sidebar-page-list', $page->name);
    }

    public function test_restricted_pages_not_visible_in_book_navigation_on_chapters()
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->first();
        $page = $chapter->pages->first();

        $this->setRestrictionsForTestRoles($page, []);

        $this->actingAs($this->user)
            ->get($chapter->getUrl())
            ->assertElementNotContains('.sidebar-page-list', $page->name);
    }

    public function test_restricted_pages_not_visible_on_chapter_pages()
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->first();
        $page = $chapter->pages->first();

        $this->setRestrictionsForTestRoles($page, []);

        $this->actingAs($this->user)
            ->get($chapter->getUrl())
            ->assertDontSee($page->name);
    }

    public function test_restricted_chapter_pages_not_visible_on_book_page()
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->first();
        $this->actingAs($this->user)
            ->get($chapter->book->getUrl())
            ->assertSee($chapter->pages->first()->name);

        foreach ($chapter->pages as $page) {
            $this->setRestrictionsForTestRoles($page, []);
        }

        $this->actingAs($this->user)
            ->get($chapter->book->getUrl())
            ->assertDontSee($chapter->pages->first()->name);
    }

    public function test_bookshelf_update_restriction_override()
    {
        /** @var Bookshelf $shelf */
        $shelf = Bookshelf::query()->first();

        $this->actingAs($this->viewer)
            ->get($shelf->getUrl('/edit'))
            ->assertDontSee('Edit Book');

        $this->setRestrictionsForTestRoles($shelf, ['view', 'delete']);

        $this->get($shelf->getUrl('/edit'))->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');

        $this->setRestrictionsForTestRoles($shelf, ['view', 'update']);

        $this->get($shelf->getUrl('/edit'))->assertOk();
    }

    public function test_bookshelf_delete_restriction_override()
    {
        /** @var Bookshelf $shelf */
        $shelf = Bookshelf::query()->first();

        $this->actingAs($this->viewer)
            ->get($shelf->getUrl('/delete'))
            ->assertDontSee('Delete Book');

        $this->setRestrictionsForTestRoles($shelf, ['view', 'update']);

        $this->get($shelf->getUrl('/delete'))->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');

        $this->setRestrictionsForTestRoles($shelf, ['view', 'delete']);

        $this->get($shelf->getUrl('/delete'))->assertOk()->assertSee('Delete Book');
    }

    public function test_book_create_restriction_override()
    {
        /** @var Book $book */
        $book = Book::query()->first();

        $bookUrl = $book->getUrl();
        $this->actingAs($this->viewer)
            ->get($bookUrl)
            ->assertElementNotContains('.actions', 'New Page')
            ->assertElementNotContains('.actions', 'New Chapter');

        $this->setRestrictionsForTestRoles($book, ['view', 'delete', 'update']);

        $this->get($bookUrl . '/create-chapter')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');
        $this->get($bookUrl . '/create-page')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');
        $this->get($bookUrl)->assertElementNotContains('.actions', 'New Page')
            ->assertElementNotContains('.actions', 'New Chapter');

        $this->setRestrictionsForTestRoles($book, ['view', 'create']);

        $resp = $this->post($book->getUrl('/create-chapter'), [
            'name' => 'test chapter',
            'description' => 'test desc',
        ]);
        $resp->assertRedirect($book->getUrl('/chapter/test-chapter'));


        $this->get($book->getUrl('/create-page'));
        /** @var Page $page */
        $page = Page::query()->where('draft', '=', true)->orderByDesc('id')->first();
        $resp = $this->post($page->getUrl(), [
            'name' => 'test page',
            'html' => 'test desc',
        ]);
        $resp->assertRedirect($book->getUrl('/page/test-page'));

        $this->get($bookUrl)
            ->assertElementContains('.actions', 'New Page')
            ->assertElementContains('.actions', 'New Chapter');
    }

    public function test_book_update_restriction_override()
    {
        /** @var Book $book */
        $book = Book::query()->first();
        $bookPage = $book->pages->first();
        $bookChapter = $book->chapters->first();

        $bookUrl = $book->getUrl();
        $this->actingAs($this->viewer)->get($bookUrl . '/edit')
            ->assertDontSee('Edit Book');

        $this->setRestrictionsForTestRoles($book, ['view', 'delete']);

        $this->get($bookUrl . '/edit')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');
        $this->get($bookPage->getUrl() . '/edit')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');
        $this->get($bookChapter->getUrl() . '/edit')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');

        $this->setRestrictionsForTestRoles($book, ['view', 'update']);

        $this->get($bookUrl . '/edit')->assertOk();
        $this->get($bookPage->getUrl() . '/edit')->assertOk();
        $this->get($bookChapter->getUrl() . '/edit')->assertSee('Edit Chapter');
    }

    public function test_book_delete_restriction_override()
    {
        /** @var Book $book */
        $book = Book::query()->first();
        $bookPage = $book->pages->first();
        $bookChapter = $book->chapters->first();

        $bookUrl = $book->getUrl();
        $this->actingAs($this->viewer)
            ->get($bookUrl . '/delete')
            ->assertDontSee('Delete Book');

        $this->setRestrictionsForTestRoles($book, ['view', 'update']);

        $this->get($bookUrl . '/delete')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');
        $this->get($bookPage->getUrl() . '/delete')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');
        $this->get($bookChapter->getUrl() . '/delete')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');

        $this->setRestrictionsForTestRoles($book, ['view', 'delete']);

        $this->get($bookUrl . '/delete')->assertOk()->assertSee('Delete Book');
        $this->get($bookPage->getUrl() . '/delete')->assertOk()->assertSee('Delete Page');
        $this->get($bookChapter->getUrl() . '/delete')->assertSee('Delete Chapter');
    }

    public function test_page_visible_if_has_permissions_when_book_not_visible()
    {
        /** @var Book $book */
        $book = Book::query()->first();
        $bookChapter = $book->chapters->first();
        $bookPage = $bookChapter->pages->first();

        foreach ([$book, $bookChapter, $bookPage] as $entity) {
            $entity->name = Str::random(24);
            $entity->save();
        }

        $this->setRestrictionsForTestRoles($book, []);
        $this->setRestrictionsForTestRoles($bookPage, ['view']);

        $this->actingAs($this->viewer);
        $resp = $this->get($bookPage->getUrl());
        $resp->assertOk();
        $resp->assertSee($bookPage->name);
        $resp->assertDontSee(substr($book->name, 0, 15));
        $resp->assertDontSee(substr($bookChapter->name, 0, 15));
    }

    public function test_book_sort_view_permission()
    {
        /** @var Book $firstBook */
        $firstBook = Book::query()->first();
        /** @var Book $secondBook */
        $secondBook = Book::query()->find(2);

        $this->setRestrictionsForTestRoles($firstBook, ['view', 'update']);
        $this->setRestrictionsForTestRoles($secondBook, ['view']);

        // Test sort page visibility
        $this->actingAs($this->user)->get($secondBook->getUrl('/sort'))->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');

        // Check sort page on first book
        $this->actingAs($this->user)->get($firstBook->getUrl('/sort'));
    }

    public function test_book_sort_permission()
    {
        /** @var Book $firstBook */
        $firstBook = Book::query()->first();
        /** @var Book $secondBook */
        $secondBook = Book::query()->find(2);

        $this->setRestrictionsForTestRoles($firstBook, ['view', 'update']);
        $this->setRestrictionsForTestRoles($secondBook, ['view']);

        $firstBookChapter = $this->newChapter(['name' => 'first book chapter'], $firstBook);
        $secondBookChapter = $this->newChapter(['name' => 'second book chapter'], $secondBook);

        // Create request data
        $reqData = [
            [
                'id'            => $firstBookChapter->id,
                'sort'          => 0,
                'parentChapter' => false,
                'type'          => 'chapter',
                'book'          => $secondBook->id,
            ],
        ];

        // Move chapter from first book to a second book
        $this->actingAs($this->user)->put($firstBook->getUrl() . '/sort', ['sort-tree' => json_encode($reqData)])
            ->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');

        $reqData = [
            [
                'id'            => $secondBookChapter->id,
                'sort'          => 0,
                'parentChapter' => false,
                'type'          => 'chapter',
                'book'          => $firstBook->id,
            ],
        ];

        // Move chapter from second book to first book
        $this->actingAs($this->user)->put($firstBook->getUrl() . '/sort', ['sort-tree' => json_encode($reqData)])
                ->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');
    }

    public function test_can_create_page_if_chapter_has_permissions_when_book_not_visible()
    {
        /** @var Book $book */
        $book = Book::query()->first();
        $this->setRestrictionsForTestRoles($book, []);
        $bookChapter = $book->chapters->first();
        $this->setRestrictionsForTestRoles($bookChapter, ['view']);

        $this->actingAs($this->user)->get($bookChapter->getUrl())
            ->assertDontSee('New Page');

        $this->setRestrictionsForTestRoles($bookChapter, ['view', 'create']);


        $this->get($bookChapter->getUrl('/create-page'));
        /** @var Page $page */
        $page = Page::query()->where('draft', '=', true)->orderByDesc('id')->first();
        $resp = $this->post($page->getUrl(), [
            'name' => 'test page',
            'html' => 'test content',
        ]);
        $resp->assertRedirect($book->getUrl('/page/test-page'));
    }
}
