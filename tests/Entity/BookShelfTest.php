<?php

namespace Tests\Entity;

use BookStack\Auth\User;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Uploads\Image;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\Uploads\UsesImages;

class BookShelfTest extends TestCase
{
    use UsesImages;

    public function test_shelves_shows_in_header_if_have_view_permissions()
    {
        $viewer = $this->getViewer();
        $resp = $this->actingAs($viewer)->get('/');
        $this->withHtml($resp)->assertElementContains('header', 'Shelves');

        $viewer->roles()->delete();
        $this->giveUserPermissions($viewer);
        $resp = $this->actingAs($viewer)->get('/');
        $this->withHtml($resp)->assertElementNotContains('header', 'Shelves');

        $this->giveUserPermissions($viewer, ['bookshelf-view-all']);
        $resp = $this->actingAs($viewer)->get('/');
        $this->withHtml($resp)->assertElementContains('header', 'Shelves');

        $viewer->roles()->delete();
        $this->giveUserPermissions($viewer, ['bookshelf-view-own']);
        $resp = $this->actingAs($viewer)->get('/');
        $this->withHtml($resp)->assertElementContains('header', 'Shelves');
    }

    public function test_shelves_shows_in_header_if_have_any_shelve_view_permission()
    {
        $user = User::factory()->create();
        $this->giveUserPermissions($user, ['image-create-all']);
        $shelf = Bookshelf::first();
        $userRole = $user->roles()->first();

        $resp = $this->actingAs($user)->get('/');
        $this->withHtml($resp)->assertElementNotContains('header', 'Shelves');

        $this->entities->setPermissions($shelf, ['view'], [$userRole]);

        $resp = $this->get('/');
        $this->withHtml($resp)->assertElementContains('header', 'Shelves');
    }

    public function test_shelves_page_contains_create_link()
    {
        $resp = $this->asEditor()->get('/shelves');
        $this->withHtml($resp)->assertElementContains('a', 'New Shelf');
    }

    public function test_book_not_visible_in_shelf_list_view_if_user_cant_view_shelf()
    {
        config()->set([
            'setting-defaults.user.bookshelves_view_type' => 'list',
        ]);
        $shelf = $this->entities->shelf();
        $book = $shelf->books()->first();

        $resp = $this->asEditor()->get('/shelves');
        $resp->assertSee($book->name);
        $resp->assertSee($book->getUrl());

        $this->entities->setPermissions($book, []);

        $resp = $this->asEditor()->get('/shelves');
        $resp->assertDontSee($book->name);
        $resp->assertDontSee($book->getUrl());
    }

    public function test_shelves_create()
    {
        $booksToInclude = Book::take(2)->get();
        $shelfInfo = [
            'name'        => 'My test book' . Str::random(4),
            'description' => 'Test book description ' . Str::random(10),
        ];
        $resp = $this->asEditor()->post('/shelves', array_merge($shelfInfo, [
            'books' => $booksToInclude->implode('id', ','),
            'tags'  => [
                [
                    'name'  => 'Test Category',
                    'value' => 'Test Tag Value',
                ],
            ],
        ]));
        $resp->assertRedirect();
        $editorId = $this->getEditor()->id;
        $this->assertDatabaseHas('bookshelves', array_merge($shelfInfo, ['created_by' => $editorId, 'updated_by' => $editorId]));

        $shelf = Bookshelf::where('name', '=', $shelfInfo['name'])->first();
        $shelfPage = $this->get($shelf->getUrl());
        $shelfPage->assertSee($shelfInfo['name']);
        $shelfPage->assertSee($shelfInfo['description']);
        $this->withHtml($shelfPage)->assertElementContains('.tag-item', 'Test Category');
        $this->withHtml($shelfPage)->assertElementContains('.tag-item', 'Test Tag Value');

        $this->assertDatabaseHas('bookshelves_books', ['bookshelf_id' => $shelf->id, 'book_id' => $booksToInclude[0]->id]);
        $this->assertDatabaseHas('bookshelves_books', ['bookshelf_id' => $shelf->id, 'book_id' => $booksToInclude[1]->id]);
    }

    public function test_shelves_create_sets_cover_image()
    {
        $shelfInfo = [
            'name'        => 'My test book' . Str::random(4),
            'description' => 'Test book description ' . Str::random(10),
        ];

        $imageFile = $this->getTestImage('shelf-test.png');
        $resp = $this->asEditor()->call('POST', '/shelves', $shelfInfo, [], ['image' => $imageFile]);
        $resp->assertRedirect();

        $lastImage = Image::query()->orderByDesc('id')->firstOrFail();
        $shelf = Bookshelf::query()->where('name', '=', $shelfInfo['name'])->first();
        $this->assertDatabaseHas('bookshelves', [
            'id'       => $shelf->id,
            'image_id' => $lastImage->id,
        ]);
        $this->assertEquals($lastImage->id, $shelf->cover->id);
        $this->assertEquals('cover_bookshelf', $lastImage->type);
    }

    public function test_shelf_view()
    {
        $shelf = Bookshelf::first();
        $resp = $this->asEditor()->get($shelf->getUrl());
        $resp->assertStatus(200);
        $resp->assertSeeText($shelf->name);
        $resp->assertSeeText($shelf->description);

        foreach ($shelf->books as $book) {
            $resp->assertSee($book->name);
        }
    }

    public function test_shelf_view_shows_action_buttons()
    {
        $shelf = Bookshelf::first();
        $resp = $this->asAdmin()->get($shelf->getUrl());
        $resp->assertSee($shelf->getUrl('/create-book'));
        $resp->assertSee($shelf->getUrl('/edit'));
        $resp->assertSee($shelf->getUrl('/permissions'));
        $resp->assertSee($shelf->getUrl('/delete'));
        $this->withHtml($resp)->assertElementContains('a', 'New Book');
        $this->withHtml($resp)->assertElementContains('a', 'Edit');
        $this->withHtml($resp)->assertElementContains('a', 'Permissions');
        $this->withHtml($resp)->assertElementContains('a', 'Delete');

        $resp = $this->asEditor()->get($shelf->getUrl());
        $resp->assertDontSee($shelf->getUrl('/permissions'));
    }

    public function test_shelf_view_has_sort_control_that_defaults_to_default()
    {
        $shelf = $this->entities->shelf();
        $resp = $this->asAdmin()->get($shelf->getUrl());
        $this->withHtml($resp)->assertElementExists('form[action$="change-sort/shelf_books"]');
        $this->withHtml($resp)->assertElementContains('form[action$="change-sort/shelf_books"] [aria-haspopup="true"]', 'Default');
    }

    public function test_shelf_view_sort_takes_action()
    {
        $shelf = Bookshelf::query()->whereHas('books')->with('books')->first();
        $books = Book::query()->take(3)->get(['id', 'name']);
        $books[0]->fill(['name' => 'bsfsdfsdfsd'])->save();
        $books[1]->fill(['name' => 'adsfsdfsdfsd'])->save();
        $books[2]->fill(['name' => 'hdgfgdfg'])->save();

        // Set book ordering
        $this->asAdmin()->put($shelf->getUrl(), [
            'books' => $books->implode('id', ','),
            'tags'  => [], 'description' => 'abc', 'name' => 'abc',
        ]);
        $this->assertEquals(3, $shelf->books()->count());
        $shelf->refresh();

        $resp = $this->asEditor()->get($shelf->getUrl());
        $this->withHtml($resp)->assertElementContains('.book-content a.grid-card:nth-child(1)', $books[0]->name);
        $this->withHtml($resp)->assertElementNotContains('.book-content a.grid-card:nth-child(3)', $books[0]->name);

        setting()->putUser($this->getEditor(), 'shelf_books_sort_order', 'desc');
        $resp = $this->asEditor()->get($shelf->getUrl());
        $this->withHtml($resp)->assertElementNotContains('.book-content a.grid-card:nth-child(1)', $books[0]->name);
        $this->withHtml($resp)->assertElementContains('.book-content a.grid-card:nth-child(3)', $books[0]->name);

        setting()->putUser($this->getEditor(), 'shelf_books_sort_order', 'desc');
        setting()->putUser($this->getEditor(), 'shelf_books_sort', 'name');
        $resp = $this->asEditor()->get($shelf->getUrl());
        $this->withHtml($resp)->assertElementContains('.book-content a.grid-card:nth-child(1)', 'hdgfgdfg');
        $this->withHtml($resp)->assertElementContains('.book-content a.grid-card:nth-child(2)', 'bsfsdfsdfsd');
        $this->withHtml($resp)->assertElementContains('.book-content a.grid-card:nth-child(3)', 'adsfsdfsdfsd');
    }

    public function test_shelf_edit()
    {
        $shelf = Bookshelf::first();
        $resp = $this->asEditor()->get($shelf->getUrl('/edit'));
        $resp->assertSeeText('Edit Shelf');

        $booksToInclude = Book::take(2)->get();
        $shelfInfo = [
            'name'        => 'My test book' . Str::random(4),
            'description' => 'Test book description ' . Str::random(10),
        ];

        $resp = $this->asEditor()->put($shelf->getUrl(), array_merge($shelfInfo, [
            'books' => $booksToInclude->implode('id', ','),
            'tags'  => [
                [
                    'name'  => 'Test Category',
                    'value' => 'Test Tag Value',
                ],
            ],
        ]));
        $shelf = Bookshelf::find($shelf->id);
        $resp->assertRedirect($shelf->getUrl());
        $this->assertSessionHas('success');

        $editorId = $this->getEditor()->id;
        $this->assertDatabaseHas('bookshelves', array_merge($shelfInfo, ['id' => $shelf->id, 'created_by' => $editorId, 'updated_by' => $editorId]));

        $shelfPage = $this->get($shelf->getUrl());
        $shelfPage->assertSee($shelfInfo['name']);
        $shelfPage->assertSee($shelfInfo['description']);
        $this->withHtml($shelfPage)->assertElementContains('.tag-item', 'Test Category');
        $this->withHtml($shelfPage)->assertElementContains('.tag-item', 'Test Tag Value');

        $this->assertDatabaseHas('bookshelves_books', ['bookshelf_id' => $shelf->id, 'book_id' => $booksToInclude[0]->id]);
        $this->assertDatabaseHas('bookshelves_books', ['bookshelf_id' => $shelf->id, 'book_id' => $booksToInclude[1]->id]);
    }

    public function test_shelf_create_new_book()
    {
        $shelf = Bookshelf::first();
        $resp = $this->asEditor()->get($shelf->getUrl('/create-book'));

        $resp->assertSee('Create New Book');
        $resp->assertSee($shelf->getShortName());

        $testName = 'Test Book in Shelf Name';

        $createBookResp = $this->asEditor()->post($shelf->getUrl('/create-book'), [
            'name'        => $testName,
            'description' => 'Book in shelf description',
        ]);
        $createBookResp->assertRedirect();

        $newBook = Book::query()->orderBy('id', 'desc')->first();
        $this->assertDatabaseHas('bookshelves_books', [
            'bookshelf_id' => $shelf->id,
            'book_id'      => $newBook->id,
        ]);

        $resp = $this->asEditor()->get($shelf->getUrl());
        $resp->assertSee($testName);
    }

    public function test_shelf_delete()
    {
        $shelf = Bookshelf::query()->whereHas('books')->first();
        $this->assertNull($shelf->deleted_at);
        $bookCount = $shelf->books()->count();

        $deleteViewReq = $this->asEditor()->get($shelf->getUrl('/delete'));
        $deleteViewReq->assertSeeText('Are you sure you want to delete this shelf?');

        $deleteReq = $this->delete($shelf->getUrl());
        $deleteReq->assertRedirect(url('/shelves'));
        $this->assertActivityExists('bookshelf_delete', $shelf);

        $shelf->refresh();
        $this->assertNotNull($shelf->deleted_at);

        $this->assertTrue($shelf->books()->count() === $bookCount);
        $this->assertTrue($shelf->deletions()->count() === 1);

        $redirectReq = $this->get($deleteReq->baseResponse->headers->get('location'));
        $this->assertNotificationContains($redirectReq, 'Shelf Successfully Deleted');
    }

    public function test_shelf_copy_permissions()
    {
        $shelf = Bookshelf::first();
        $resp = $this->asAdmin()->get($shelf->getUrl('/permissions'));
        $resp->assertSeeText('Copy Permissions');
        $resp->assertSee("action=\"{$shelf->getUrl('/copy-permissions')}\"", false);

        $child = $shelf->books()->first();
        $editorRole = $this->getEditor()->roles()->first();
        $this->assertFalse(boolval($child->restricted), 'Child book should not be restricted by default');
        $this->assertTrue($child->permissions()->count() === 0, 'Child book should have no permissions by default');

        $this->entities->setPermissions($shelf, ['view', 'update'], [$editorRole]);
        $resp = $this->post($shelf->getUrl('/copy-permissions'));
        $child = $shelf->books()->first();

        $resp->assertRedirect($shelf->getUrl());
        $this->assertTrue(boolval($child->restricted), 'Child book should now be restricted');
        $this->assertTrue($child->permissions()->count() === 2, 'Child book should have copied permissions');
        $this->assertDatabaseHas('entity_permissions', ['restrictable_id' => $child->id, 'action' => 'view', 'role_id' => $editorRole->id]);
        $this->assertDatabaseHas('entity_permissions', ['restrictable_id' => $child->id, 'action' => 'update', 'role_id' => $editorRole->id]);
    }

    public function test_permission_page_has_a_warning_about_no_cascading()
    {
        $shelf = Bookshelf::first();
        $resp = $this->asAdmin()->get($shelf->getUrl('/permissions'));
        $resp->assertSeeText('Permissions on shelves do not automatically cascade to contained books.');
    }

    public function test_bookshelves_show_in_breadcrumbs_if_in_context()
    {
        $shelf = Bookshelf::first();
        $shelfBook = $shelf->books()->first();
        $shelfPage = $shelfBook->pages()->first();
        $this->asAdmin();

        $bookVisit = $this->get($shelfBook->getUrl());
        $this->withHtml($bookVisit)->assertElementNotContains('.breadcrumbs', 'Shelves');
        $this->withHtml($bookVisit)->assertElementNotContains('.breadcrumbs', $shelf->getShortName());

        $this->get($shelf->getUrl());
        $bookVisit = $this->get($shelfBook->getUrl());
        $this->withHtml($bookVisit)->assertElementContains('.breadcrumbs', 'Shelves');
        $this->withHtml($bookVisit)->assertElementContains('.breadcrumbs', $shelf->getShortName());

        $pageVisit = $this->get($shelfPage->getUrl());
        $this->withHtml($pageVisit)->assertElementContains('.breadcrumbs', 'Shelves');
        $this->withHtml($pageVisit)->assertElementContains('.breadcrumbs', $shelf->getShortName());

        $this->get('/books');
        $pageVisit = $this->get($shelfPage->getUrl());
        $this->withHtml($pageVisit)->assertElementNotContains('.breadcrumbs', 'Shelves');
        $this->withHtml($pageVisit)->assertElementNotContains('.breadcrumbs', $shelf->getShortName());
    }

    public function test_bookshelves_show_on_book()
    {
        // Create shelf
        $shelfInfo = [
            'name'        => 'My test shelf' . Str::random(4),
            'description' => 'Test shelf description ' . Str::random(10),
        ];

        $this->asEditor()->post('/shelves', $shelfInfo);
        $shelf = Bookshelf::where('name', '=', $shelfInfo['name'])->first();

        // Create book and add to shelf
        $this->asEditor()->post($shelf->getUrl('/create-book'), [
            'name'        => 'Test book name',
            'description' => 'Book in shelf description',
        ]);

        $newBook = Book::query()->orderBy('id', 'desc')->first();

        $resp = $this->asEditor()->get($newBook->getUrl());
        $this->withHtml($resp)->assertElementContains('.tri-layout-left-contents', $shelfInfo['name']);

        // Remove shelf
        $this->delete($shelf->getUrl());

        $resp = $this->asEditor()->get($newBook->getUrl());
        $resp->assertDontSee($shelfInfo['name']);
    }

    public function test_cancel_on_child_book_creation_returns_to_original_shelf()
    {
        $shelf = $this->entities->shelf();
        $resp = $this->asEditor()->get($shelf->getUrl('/create-book'));
        $this->withHtml($resp)->assertElementContains('form a[href="' . $shelf->getUrl() . '"]', 'Cancel');
    }
}
