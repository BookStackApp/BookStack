<?php namespace Tests;

use BookStack\Auth\Role;
use BookStack\Auth\User;
use BookStack\Entities\Book;
use BookStack\Entities\Bookshelf;

class BookShelfTest extends TestCase
{

    public function test_shelves_shows_in_header_if_have_view_permissions()
    {
        $viewer = $this->getViewer();
        $resp = $this->actingAs($viewer)->get('/');
        $resp->assertElementContains('header', 'Shelves');

        $viewer->roles()->delete();
        $this->giveUserPermissions($viewer);
        $resp = $this->actingAs($viewer)->get('/');
        $resp->assertElementNotContains('header', 'Shelves');

        $this->giveUserPermissions($viewer, ['bookshelf-view-all']);
        $resp = $this->actingAs($viewer)->get('/');
        $resp->assertElementContains('header', 'Shelves');

        $viewer->roles()->delete();
        $this->giveUserPermissions($viewer, ['bookshelf-view-own']);
        $resp = $this->actingAs($viewer)->get('/');
        $resp->assertElementContains('header', 'Shelves');
    }

    public function test_shelves_shows_in_header_if_have_any_shelve_view_permission()
    {
        $user = factory(User::class)->create();
        $this->giveUserPermissions($user, ['image-create-all']);
        $shelf = Bookshelf::first();
        $userRole = $user->roles()->first();

        $resp = $this->actingAs($user)->get('/');
        $resp->assertElementNotContains('header', 'Shelves');

        $this->setEntityRestrictions($shelf, ['view'], [$userRole]);

        $resp = $this->get('/');
        $resp->assertElementContains('header', 'Shelves');
    }

    public function test_shelves_page_contains_create_link()
    {
        $resp = $this->asEditor()->get('/shelves');
        $resp->assertElementContains('a', 'Create New Shelf');
    }

    public function test_shelves_create()
    {
        $booksToInclude = Book::take(2)->get();
        $shelfInfo = [
            'name' => 'My test book' . str_random(4),
            'description' => 'Test book description ' . str_random(10)
        ];
        $resp = $this->asEditor()->post('/shelves', array_merge($shelfInfo, [
            'books' => $booksToInclude->implode('id', ','),
            'tags' => [
                [
                    'name' => 'Test Category',
                    'value' => 'Test Tag Value',
                ]
            ],
        ]));
        $resp->assertRedirect();
        $editorId = $this->getEditor()->id;
        $this->assertDatabaseHas('bookshelves', array_merge($shelfInfo, ['created_by' => $editorId, 'updated_by' => $editorId]));

        $shelf = Bookshelf::where('name', '=', $shelfInfo['name'])->first();
        $shelfPage = $this->get($shelf->getUrl());
        $shelfPage->assertSee($shelfInfo['name']);
        $shelfPage->assertSee($shelfInfo['description']);
        $shelfPage->assertElementContains('.tag-item', 'Test Category');
        $shelfPage->assertElementContains('.tag-item', 'Test Tag Value');

        $this->assertDatabaseHas('bookshelves_books', ['bookshelf_id' => $shelf->id, 'book_id' => $booksToInclude[0]->id]);
        $this->assertDatabaseHas('bookshelves_books', ['bookshelf_id' => $shelf->id, 'book_id' => $booksToInclude[1]->id]);
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
        $resp->assertElementContains('a', 'Create New Book');
        $resp->assertElementContains('a', 'Edit');
        $resp->assertElementContains('a', 'Permissions');
        $resp->assertElementContains('a', 'Delete');

        $resp = $this->asEditor()->get($shelf->getUrl());
        $resp->assertDontSee($shelf->getUrl('/permissions'));
    }

    public function test_shelf_edit()
    {
        $shelf = Bookshelf::first();
        $resp = $this->asEditor()->get($shelf->getUrl('/edit'));
        $resp->assertSeeText('Edit Bookshelf');

        $booksToInclude = Book::take(2)->get();
        $shelfInfo = [
            'name' => 'My test book' . str_random(4),
            'description' => 'Test book description ' . str_random(10)
        ];

        $resp = $this->asEditor()->put($shelf->getUrl(), array_merge($shelfInfo, [
            'books' => $booksToInclude->implode('id', ','),
            'tags' => [
                [
                    'name' => 'Test Category',
                    'value' => 'Test Tag Value',
                ]
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
        $shelfPage->assertElementContains('.tag-item', 'Test Category');
        $shelfPage->assertElementContains('.tag-item', 'Test Tag Value');

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
            'name' => $testName,
            'description' => 'Book in shelf description'
        ]);

        $resp = $this->asEditor()->get($shelf->getUrl());

        $resp->assertSee($testName);
    }

    public function test_shelf_delete()
    {
        $shelf = Bookshelf::first();
        $resp = $this->asEditor()->get($shelf->getUrl('/delete'));
        $resp->assertSeeText('Delete Bookshelf');
        $resp->assertSee("action=\"{$shelf->getUrl()}\"");

        $resp = $this->delete($shelf->getUrl());
        $resp->assertRedirect('/shelves');
        $this->assertDatabaseMissing('bookshelves', ['id' => $shelf->id]);
        $this->assertDatabaseMissing('bookshelves_books', ['bookshelf_id' => $shelf->id]);
        $this->assertSessionHas('success');
    }

    public function test_shelf_copy_permissions()
    {
        $shelf = Bookshelf::first();
        $resp = $this->asAdmin()->get($shelf->getUrl('/permissions'));
        $resp->assertSeeText('Copy Permissions');
        $resp->assertSee("action=\"{$shelf->getUrl('/copy-permissions')}\"");

        $child = $shelf->books()->first();
        $editorRole = $this->getEditor()->roles()->first();
        $this->assertFalse(boolval($child->restricted), "Child book should not be restricted by default");
        $this->assertTrue($child->permissions()->count() === 0, "Child book should have no permissions by default");

        $this->setEntityRestrictions($shelf, ['view', 'update'], [$editorRole]);
        $resp = $this->post($shelf->getUrl('/copy-permissions'));
        $child = $shelf->books()->first();

        $resp->assertRedirect($shelf->getUrl());
        $this->assertTrue(boolval($child->restricted), "Child book should now be restricted");
        $this->assertTrue($child->permissions()->count() === 2, "Child book should have copied permissions");
        $this->assertDatabaseHas('entity_permissions', ['restrictable_id' => $child->id, 'action' => 'view', 'role_id' => $editorRole->id]);
        $this->assertDatabaseHas('entity_permissions', ['restrictable_id' => $child->id, 'action' => 'update', 'role_id' => $editorRole->id]);
    }

    public function test_bookshelves_show_in_breadcrumbs_if_in_context()
    {
        $shelf = Bookshelf::first();
        $shelfBook = $shelf->books()->first();
        $shelfPage = $shelfBook->pages()->first();
        $this->asAdmin();

        $bookVisit = $this->get($shelfBook->getUrl());
        $bookVisit->assertElementNotContains('.breadcrumbs', 'Shelves');
        $bookVisit->assertElementNotContains('.breadcrumbs', $shelf->getShortName());

        $this->get($shelf->getUrl());
        $bookVisit = $this->get($shelfBook->getUrl());
        $bookVisit->assertElementContains('.breadcrumbs', 'Shelves');
        $bookVisit->assertElementContains('.breadcrumbs', $shelf->getShortName());

        $pageVisit = $this->get($shelfPage->getUrl());
        $pageVisit->assertElementContains('.breadcrumbs', 'Shelves');
        $pageVisit->assertElementContains('.breadcrumbs', $shelf->getShortName());

        $this->get('/books');
        $pageVisit = $this->get($shelfPage->getUrl());
        $pageVisit->assertElementNotContains('.breadcrumbs', 'Shelves');
        $pageVisit->assertElementNotContains('.breadcrumbs', $shelf->getShortName());
    }

}
