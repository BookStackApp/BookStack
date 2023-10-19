<?php

namespace Tests\Permissions;

use BookStack\Activity\ActivityType;
use BookStack\Activity\Models\Comment;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Uploads\Image;
use BookStack\Users\Models\Role;
use BookStack\Users\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class RolePermissionsTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->users->viewer();
    }

    public function test_manage_user_permission()
    {
        $this->actingAs($this->user)->get('/settings/users')->assertRedirect('/');
        $this->permissions->grantUserRolePermissions($this->user, ['users-manage']);
        $this->actingAs($this->user)->get('/settings/users')->assertOk();
    }

    public function test_manage_users_permission_shows_link_in_header_if_does_not_have_settings_manage_permision()
    {
        $usersLink = 'href="' . url('/settings/users') . '"';
        $this->actingAs($this->user)->get('/')->assertDontSee($usersLink, false);
        $this->permissions->grantUserRolePermissions($this->user, ['users-manage']);
        $this->actingAs($this->user)->get('/')->assertSee($usersLink, false);
        $this->permissions->grantUserRolePermissions($this->user, ['settings-manage', 'users-manage']);
        $this->actingAs($this->user)->get('/')->assertDontSee($usersLink, false);
    }

    public function test_user_cannot_change_email_unless_they_have_manage_users_permission()
    {
        $originalEmail = $this->user->email;
        $this->actingAs($this->user);

        $resp = $this->get('/my-account/profile')->assertOk();
        $this->withHtml($resp)->assertElementExists('input[name=email][disabled]');
        $resp->assertSee('Unfortunately you don\'t have permission to change your email address.');
        $this->put('/my-account/profile', [
            'name'  => 'my_new_name',
            'email' => 'new_email@example.com',
        ]);
        $this->assertDatabaseHas('users', [
            'id'    => $this->user->id,
            'email' => $originalEmail,
            'name'  => 'my_new_name',
        ]);

        $this->permissions->grantUserRolePermissions($this->user, ['users-manage']);

        $resp = $this->get('/my-account/profile')->assertOk();
        $this->withHtml($resp)
            ->assertElementNotExists('input[name=email][disabled]')
            ->assertElementExists('input[name=email]');

        $this->put('/my-account/profile', [
            'name'  => 'my_new_name_2',
            'email' => 'new_email@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'id'    => $this->user->id,
            'email' => 'new_email@example.com',
            'name'  => 'my_new_name_2',
        ]);
    }

    public function test_user_roles_manage_permission()
    {
        $this->actingAs($this->user)->get('/settings/roles')->assertRedirect('/');
        $this->get('/settings/roles/1')->assertRedirect('/');
        $this->permissions->grantUserRolePermissions($this->user, ['user-roles-manage']);
        $this->actingAs($this->user)->get('/settings/roles')->assertOk();
        $this->get('/settings/roles/1')
            ->assertOk()
            ->assertSee('Admin');
    }

    public function test_settings_manage_permission()
    {
        $this->actingAs($this->user)->get('/settings/features')->assertRedirect('/');
        $this->permissions->grantUserRolePermissions($this->user, ['settings-manage']);
        $this->get('/settings/features')->assertOk();

        $resp = $this->post('/settings/features', []);
        $resp->assertRedirect('/settings/features');
        $resp = $this->get('/settings/features');
        $resp->assertSee('Settings successfully updated');
    }

    public function test_restrictions_manage_all_permission()
    {
        $page = $this->entities->page();

        $this->actingAs($this->user)->get($page->getUrl())->assertDontSee('Permissions');
        $this->get($page->getUrl('/permissions'))->assertRedirect('/');

        $this->permissions->grantUserRolePermissions($this->user, ['restrictions-manage-all']);

        $this->actingAs($this->user)->get($page->getUrl())->assertSee('Permissions');

        $this->get($page->getUrl('/permissions'))
            ->assertOk()
            ->assertSee('Page Permissions');
    }

    public function test_restrictions_manage_own_permission()
    {
        $otherUsersPage = $this->entities->page();
        $content = $this->entities->createChainBelongingToUser($this->user);

        // Set a different creator on the page we're checking to ensure
        // that the owner fields are checked
        $page = $content['page']; /** @var Page $page */
        $page->created_by = $otherUsersPage->id;
        $page->owned_by = $this->user->id;
        $page->save();

        // Check can't restrict other's content
        $this->actingAs($this->user)->get($otherUsersPage->getUrl())->assertDontSee('Permissions');
        $this->get($otherUsersPage->getUrl('/permissions'))->assertRedirect('/');

        // Check can't restrict own content
        $this->actingAs($this->user)->get($page->getUrl())->assertDontSee('Permissions');
        $this->get($page->getUrl('/permissions'))->assertRedirect('/');

        $this->permissions->grantUserRolePermissions($this->user, ['restrictions-manage-own']);

        // Check can't restrict other's content
        $this->actingAs($this->user)->get($otherUsersPage->getUrl())->assertDontSee('Permissions');
        $this->get($otherUsersPage->getUrl('/permissions'))->assertRedirect();

        // Check can restrict own content
        $this->actingAs($this->user)->get($page->getUrl())->assertSee('Permissions');
        $this->get($page->getUrl('/permissions'))->assertOk();
    }

    /**
     * Check a standard entity access permission.
     */
    private function checkAccessPermission(string $permission, array $accessUrls = [], array $visibles = [])
    {
        foreach ($accessUrls as $url) {
            $this->actingAs($this->user)->get($url)->assertRedirect('/');
        }

        foreach ($visibles as $url => $text) {
            $resp = $this->actingAs($this->user)->get($url);
            $this->withHtml($resp)->assertElementNotContains('.action-buttons', $text);
        }

        $this->permissions->grantUserRolePermissions($this->user, [$permission]);

        foreach ($accessUrls as $url) {
            $this->actingAs($this->user)->get($url)->assertOk();
        }
        foreach ($visibles as $url => $text) {
            $this->actingAs($this->user)->get($url)->assertSee($text);
        }
    }

    public function test_bookshelves_create_all_permissions()
    {
        $this->checkAccessPermission('bookshelf-create-all', [
            '/create-shelf',
        ], [
            '/shelves' => 'New Shelf',
        ]);

        $this->post('/shelves', [
            'name'        => 'test shelf',
            'description' => 'shelf desc',
        ])->assertRedirect('/shelves/test-shelf');
    }

    public function test_bookshelves_edit_own_permission()
    {
        /** @var Bookshelf $otherShelf */
        $otherShelf = Bookshelf::query()->first();
        $ownShelf = $this->entities->newShelf(['name' => 'test-shelf', 'slug' => 'test-shelf']);
        $ownShelf->forceFill(['owned_by' => $this->user->id, 'updated_by' => $this->user->id])->save();
        $this->permissions->regenerateForEntity($ownShelf);

        $this->checkAccessPermission('bookshelf-update-own', [
            $ownShelf->getUrl('/edit'),
        ], [
            $ownShelf->getUrl() => 'Edit',
        ]);

        $resp = $this->get($otherShelf->getUrl());
        $this->withHtml($resp)->assertElementNotContains('.action-buttons', 'Edit');
        $this->get($otherShelf->getUrl('/edit'))->assertRedirect('/');
    }

    public function test_bookshelves_edit_all_permission()
    {
        /** @var Bookshelf $otherShelf */
        $otherShelf = Bookshelf::query()->first();
        $this->checkAccessPermission('bookshelf-update-all', [
            $otherShelf->getUrl('/edit'),
        ], [
            $otherShelf->getUrl() => 'Edit',
        ]);
    }

    public function test_bookshelves_delete_own_permission()
    {
        $this->permissions->grantUserRolePermissions($this->user, ['bookshelf-update-all']);
        /** @var Bookshelf $otherShelf */
        $otherShelf = Bookshelf::query()->first();
        $ownShelf = $this->entities->newShelf(['name' => 'test-shelf', 'slug' => 'test-shelf']);
        $ownShelf->forceFill(['owned_by' => $this->user->id, 'updated_by' => $this->user->id])->save();
        $this->permissions->regenerateForEntity($ownShelf);

        $this->checkAccessPermission('bookshelf-delete-own', [
            $ownShelf->getUrl('/delete'),
        ], [
            $ownShelf->getUrl() => 'Delete',
        ]);

        $resp = $this->get($otherShelf->getUrl());
        $this->withHtml($resp)->assertElementNotContains('.action-buttons', 'Delete');
        $this->get($otherShelf->getUrl('/delete'))->assertRedirect('/');

        $this->get($ownShelf->getUrl());
        $this->delete($ownShelf->getUrl())->assertRedirect('/shelves');
        $this->get('/shelves')->assertDontSee($ownShelf->name);
    }

    public function test_bookshelves_delete_all_permission()
    {
        $this->permissions->grantUserRolePermissions($this->user, ['bookshelf-update-all']);
        /** @var Bookshelf $otherShelf */
        $otherShelf = Bookshelf::query()->first();
        $this->checkAccessPermission('bookshelf-delete-all', [
            $otherShelf->getUrl('/delete'),
        ], [
            $otherShelf->getUrl() => 'Delete',
        ]);

        $this->delete($otherShelf->getUrl())->assertRedirect('/shelves');
        $this->get('/shelves')->assertDontSee($otherShelf->name);
    }

    public function test_books_create_all_permissions()
    {
        $this->checkAccessPermission('book-create-all', [
            '/create-book',
        ], [
            '/books' => 'Create New Book',
        ]);

        $this->post('/books', [
            'name'        => 'test book',
            'description' => 'book desc',
        ])->assertRedirect('/books/test-book');
    }

    public function test_books_edit_own_permission()
    {
        /** @var Book $otherBook */
        $otherBook = Book::query()->take(1)->get()->first();
        $ownBook = $this->entities->createChainBelongingToUser($this->user)['book'];
        $this->checkAccessPermission('book-update-own', [
            $ownBook->getUrl() . '/edit',
        ], [
            $ownBook->getUrl() => 'Edit',
        ]);

        $resp = $this->get($otherBook->getUrl());
        $this->withHtml($resp)->assertElementNotContains('.action-buttons', 'Edit');
        $this->get($otherBook->getUrl('/edit'))->assertRedirect('/');
    }

    public function test_books_edit_all_permission()
    {
        /** @var Book $otherBook */
        $otherBook = Book::query()->take(1)->get()->first();
        $this->checkAccessPermission('book-update-all', [
            $otherBook->getUrl() . '/edit',
        ], [
            $otherBook->getUrl() => 'Edit',
        ]);
    }

    public function test_books_delete_own_permission()
    {
        $this->permissions->grantUserRolePermissions($this->user, ['book-update-all']);
        /** @var Book $otherBook */
        $otherBook = Book::query()->take(1)->get()->first();
        $ownBook = $this->entities->createChainBelongingToUser($this->user)['book'];
        $this->checkAccessPermission('book-delete-own', [
            $ownBook->getUrl() . '/delete',
        ], [
            $ownBook->getUrl() => 'Delete',
        ]);

        $resp = $this->get($otherBook->getUrl());
        $this->withHtml($resp)->assertElementNotContains('.action-buttons', 'Delete');
        $this->get($otherBook->getUrl('/delete'))->assertRedirect('/');
        $this->get($ownBook->getUrl());
        $this->delete($ownBook->getUrl())->assertRedirect('/books');
        $this->get('/books')->assertDontSee($ownBook->name);
    }

    public function test_books_delete_all_permission()
    {
        $this->permissions->grantUserRolePermissions($this->user, ['book-update-all']);
        /** @var Book $otherBook */
        $otherBook = Book::query()->take(1)->get()->first();
        $this->checkAccessPermission('book-delete-all', [
            $otherBook->getUrl() . '/delete',
        ], [
            $otherBook->getUrl() => 'Delete',
        ]);

        $this->get($otherBook->getUrl());
        $this->delete($otherBook->getUrl())->assertRedirect('/books');
        $this->get('/books')->assertDontSee($otherBook->name);
    }

    public function test_chapter_create_own_permissions()
    {
        /** @var Book $book */
        $book = Book::query()->take(1)->get()->first();
        $ownBook = $this->entities->createChainBelongingToUser($this->user)['book'];
        $this->checkAccessPermission('chapter-create-own', [
            $ownBook->getUrl('/create-chapter'),
        ], [
            $ownBook->getUrl() => 'New Chapter',
        ]);

        $this->post($ownBook->getUrl('/create-chapter'), [
            'name'        => 'test chapter',
            'description' => 'chapter desc',
        ])->assertRedirect($ownBook->getUrl('/chapter/test-chapter'));

        $resp = $this->get($book->getUrl());
        $this->withHtml($resp)->assertElementNotContains('.action-buttons', 'New Chapter');
        $this->get($book->getUrl('/create-chapter'))->assertRedirect('/');
    }

    public function test_chapter_create_all_permissions()
    {
        $book = $this->entities->book();
        $this->checkAccessPermission('chapter-create-all', [
            $book->getUrl('/create-chapter'),
        ], [
            $book->getUrl() => 'New Chapter',
        ]);

        $this->post($book->getUrl('/create-chapter'), [
            'name'        => 'test chapter',
            'description' => 'chapter desc',
        ])->assertRedirect($book->getUrl('/chapter/test-chapter'));
    }

    public function test_chapter_edit_own_permission()
    {
        /** @var Chapter $otherChapter */
        $otherChapter = Chapter::query()->first();
        $ownChapter = $this->entities->createChainBelongingToUser($this->user)['chapter'];
        $this->checkAccessPermission('chapter-update-own', [
            $ownChapter->getUrl() . '/edit',
        ], [
            $ownChapter->getUrl() => 'Edit',
        ]);

        $resp = $this->get($otherChapter->getUrl());
        $this->withHtml($resp)->assertElementNotContains('.action-buttons', 'Edit');
        $this->get($otherChapter->getUrl('/edit'))->assertRedirect('/');
    }

    public function test_chapter_edit_all_permission()
    {
        /** @var Chapter $otherChapter */
        $otherChapter = Chapter::query()->take(1)->get()->first();
        $this->checkAccessPermission('chapter-update-all', [
            $otherChapter->getUrl() . '/edit',
        ], [
            $otherChapter->getUrl() => 'Edit',
        ]);
    }

    public function test_chapter_delete_own_permission()
    {
        $this->permissions->grantUserRolePermissions($this->user, ['chapter-update-all']);
        /** @var Chapter $otherChapter */
        $otherChapter = Chapter::query()->first();
        $ownChapter = $this->entities->createChainBelongingToUser($this->user)['chapter'];
        $this->checkAccessPermission('chapter-delete-own', [
            $ownChapter->getUrl() . '/delete',
        ], [
            $ownChapter->getUrl() => 'Delete',
        ]);

        $bookUrl = $ownChapter->book->getUrl();
        $resp = $this->get($otherChapter->getUrl());
        $this->withHtml($resp)->assertElementNotContains('.action-buttons', 'Delete');
        $this->get($otherChapter->getUrl('/delete'))->assertRedirect('/');
        $this->get($ownChapter->getUrl());
        $this->delete($ownChapter->getUrl())->assertRedirect($bookUrl);
        $resp = $this->get($bookUrl);
        $this->withHtml($resp)->assertElementNotContains('.book-content', $ownChapter->name);
    }

    public function test_chapter_delete_all_permission()
    {
        $this->permissions->grantUserRolePermissions($this->user, ['chapter-update-all']);
        /** @var Chapter $otherChapter */
        $otherChapter = Chapter::query()->first();
        $this->checkAccessPermission('chapter-delete-all', [
            $otherChapter->getUrl() . '/delete',
        ], [
            $otherChapter->getUrl() => 'Delete',
        ]);

        $bookUrl = $otherChapter->book->getUrl();
        $this->get($otherChapter->getUrl());
        $this->delete($otherChapter->getUrl())->assertRedirect($bookUrl);
        $resp = $this->get($bookUrl);
        $this->withHtml($resp)->assertElementNotContains('.book-content', $otherChapter->name);
    }

    public function test_page_create_own_permissions()
    {
        $book = $this->entities->book();
        $chapter = $this->entities->chapter();

        $entities = $this->entities->createChainBelongingToUser($this->user);
        $ownBook = $entities['book'];
        $ownChapter = $entities['chapter'];

        $createUrl = $ownBook->getUrl('/create-page');
        $createUrlChapter = $ownChapter->getUrl('/create-page');
        $accessUrls = [$createUrl, $createUrlChapter];

        foreach ($accessUrls as $url) {
            $this->actingAs($this->user)->get($url)->assertRedirect('/');
        }

        $this->checkAccessPermission('page-create-own', [], [
            $ownBook->getUrl()    => 'New Page',
            $ownChapter->getUrl() => 'New Page',
        ]);

        $this->permissions->grantUserRolePermissions($this->user, ['page-create-own']);

        foreach ($accessUrls as $index => $url) {
            $resp = $this->actingAs($this->user)->get($url);
            $expectedUrl = Page::query()->where('draft', '=', true)->orderBy('id', 'desc')->first()->getUrl();
            $resp->assertRedirect($expectedUrl);
        }

        $this->get($createUrl);
        /** @var Page $draft */
        $draft = Page::query()->where('draft', '=', true)->orderBy('id', 'desc')->first();
        $this->post($draft->getUrl(), [
            'name' => 'test page',
            'html' => 'page desc',
        ])->assertRedirect($ownBook->getUrl('/page/test-page'));

        $resp = $this->get($book->getUrl());
        $this->withHtml($resp)->assertElementNotContains('.action-buttons', 'New Page');
        $this->get($book->getUrl('/create-page'))->assertRedirect('/');

        $resp = $this->get($chapter->getUrl());
        $this->withHtml($resp)->assertElementNotContains('.action-buttons', 'New Page');
        $this->get($chapter->getUrl('/create-page'))->assertRedirect('/');
    }

    public function test_page_create_all_permissions()
    {
        $book = $this->entities->book();
        $chapter = $this->entities->chapter();
        $createUrl = $book->getUrl('/create-page');

        $createUrlChapter = $chapter->getUrl('/create-page');
        $accessUrls = [$createUrl, $createUrlChapter];

        foreach ($accessUrls as $url) {
            $this->actingAs($this->user)->get($url)->assertRedirect('/');
        }

        $this->checkAccessPermission('page-create-all', [], [
            $book->getUrl()    => 'New Page',
            $chapter->getUrl() => 'New Page',
        ]);

        $this->permissions->grantUserRolePermissions($this->user, ['page-create-all']);

        foreach ($accessUrls as $index => $url) {
            $resp = $this->actingAs($this->user)->get($url);
            $expectedUrl = Page::query()->where('draft', '=', true)->orderBy('id', 'desc')->first()->getUrl();
            $resp->assertRedirect($expectedUrl);
        }

        $this->get($createUrl);
        /** @var Page $draft */
        $draft = Page::query()->where('draft', '=', true)->orderByDesc('id')->first();
        $this->post($draft->getUrl(), [
            'name' => 'test page',
            'html' => 'page desc',
        ])->assertRedirect($book->getUrl('/page/test-page'));

        $this->get($chapter->getUrl('/create-page'));
        /** @var Page $draft */
        $draft = Page::query()->where('draft', '=', true)->orderByDesc('id')->first();
        $this->post($draft->getUrl(), [
            'name' => 'new test page',
            'html' => 'page desc',
        ])->assertRedirect($book->getUrl('/page/new-test-page'));
    }

    public function test_page_edit_own_permission()
    {
        /** @var Page $otherPage */
        $otherPage = Page::query()->first();
        $ownPage = $this->entities->createChainBelongingToUser($this->user)['page'];
        $this->checkAccessPermission('page-update-own', [
            $ownPage->getUrl() . '/edit',
        ], [
            $ownPage->getUrl() => 'Edit',
        ]);

        $resp = $this->get($otherPage->getUrl());
        $this->withHtml($resp)->assertElementNotContains('.action-buttons', 'Edit');
        $this->get($otherPage->getUrl() . '/edit')->assertRedirect('/');
    }

    public function test_page_edit_all_permission()
    {
        /** @var Page $otherPage */
        $otherPage = Page::query()->first();
        $this->checkAccessPermission('page-update-all', [
            $otherPage->getUrl('/edit'),
        ], [
            $otherPage->getUrl() => 'Edit',
        ]);
    }

    public function test_page_delete_own_permission()
    {
        $this->permissions->grantUserRolePermissions($this->user, ['page-update-all']);
        /** @var Page $otherPage */
        $otherPage = Page::query()->first();
        $ownPage = $this->entities->createChainBelongingToUser($this->user)['page'];
        $this->checkAccessPermission('page-delete-own', [
            $ownPage->getUrl() . '/delete',
        ], [
            $ownPage->getUrl() => 'Delete',
        ]);

        $parent = $ownPage->chapter ?? $ownPage->book;
        $resp = $this->get($otherPage->getUrl());
        $this->withHtml($resp)->assertElementNotContains('.action-buttons', 'Delete');
        $this->get($otherPage->getUrl('/delete'))->assertRedirect('/');
        $this->get($ownPage->getUrl());
        $this->delete($ownPage->getUrl())->assertRedirect($parent->getUrl());
        $resp = $this->get($parent->getUrl());
        $this->withHtml($resp)->assertElementNotContains('.book-content', $ownPage->name);
    }

    public function test_page_delete_all_permission()
    {
        $this->permissions->grantUserRolePermissions($this->user, ['page-update-all']);
        /** @var Page $otherPage */
        $otherPage = Page::query()->first();

        $this->checkAccessPermission('page-delete-all', [
            $otherPage->getUrl() . '/delete',
        ], [
            $otherPage->getUrl() => 'Delete',
        ]);

        /** @var Entity $parent */
        $parent = $otherPage->chapter ?? $otherPage->book;
        $this->get($otherPage->getUrl());

        $this->delete($otherPage->getUrl())->assertRedirect($parent->getUrl());
        $this->get($parent->getUrl())->assertDontSee($otherPage->name);
    }


    public function test_image_delete_own_permission()
    {
        $this->permissions->grantUserRolePermissions($this->user, ['image-update-all']);
        $page = $this->entities->page();
        $image = Image::factory()->create([
            'uploaded_to' => $page->id,
            'created_by'  => $this->user->id,
            'updated_by'  => $this->user->id,
        ]);

        $this->actingAs($this->user)->json('delete', '/images/' . $image->id)->assertStatus(403);

        $this->permissions->grantUserRolePermissions($this->user, ['image-delete-own']);

        $this->actingAs($this->user)->json('delete', '/images/' . $image->id)->assertOk();
        $this->assertDatabaseMissing('images', ['id' => $image->id]);
    }

    public function test_image_delete_all_permission()
    {
        $this->permissions->grantUserRolePermissions($this->user, ['image-update-all']);
        $admin = $this->users->admin();
        $page = $this->entities->page();
        $image = Image::factory()->create(['uploaded_to' => $page->id, 'created_by' => $admin->id, 'updated_by' => $admin->id]);

        $this->actingAs($this->user)->json('delete', '/images/' . $image->id)->assertStatus(403);

        $this->permissions->grantUserRolePermissions($this->user, ['image-delete-own']);

        $this->actingAs($this->user)->json('delete', '/images/' . $image->id)->assertStatus(403);

        $this->permissions->grantUserRolePermissions($this->user, ['image-delete-all']);

        $this->actingAs($this->user)->json('delete', '/images/' . $image->id)->assertOk();
        $this->assertDatabaseMissing('images', ['id' => $image->id]);
    }

    public function test_empty_state_actions_not_visible_without_permission()
    {
        $admin = $this->users->admin();
        // Book links
        $book = Book::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);
        $this->permissions->regenerateForEntity($book);
        $this->actingAs($this->users->viewer())->get($book->getUrl())
            ->assertDontSee('Create a new page')
            ->assertDontSee('Add a chapter');

        // Chapter links
        $chapter = Chapter::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id, 'book_id' => $book->id]);
        $this->permissions->regenerateForEntity($chapter);
        $this->actingAs($this->users->viewer())->get($chapter->getUrl())
            ->assertDontSee('Create a new page')
            ->assertDontSee('Sort the current book');
    }

    public function test_comment_create_permission()
    {
        $ownPage = $this->entities->createChainBelongingToUser($this->user)['page'];

        $this->actingAs($this->user)
            ->addComment($ownPage)
            ->assertStatus(403);

        $this->permissions->grantUserRolePermissions($this->user, ['comment-create-all']);

        $this->actingAs($this->user)
            ->addComment($ownPage)
            ->assertOk();
    }

    public function test_comment_update_own_permission()
    {
        $ownPage = $this->entities->createChainBelongingToUser($this->user)['page'];
        $this->permissions->grantUserRolePermissions($this->user, ['comment-create-all']);
        $this->actingAs($this->user)->addComment($ownPage);
        /** @var Comment $comment */
        $comment = $ownPage->comments()->latest()->first();

        // no comment-update-own
        $this->actingAs($this->user)->updateComment($comment)->assertStatus(403);

        $this->permissions->grantUserRolePermissions($this->user, ['comment-update-own']);

        // now has comment-update-own
        $this->actingAs($this->user)->updateComment($comment)->assertOk();
    }

    public function test_comment_update_all_permission()
    {
        /** @var Page $ownPage */
        $ownPage = $this->entities->createChainBelongingToUser($this->user)['page'];
        $this->asAdmin()->addComment($ownPage);
        /** @var Comment $comment */
        $comment = $ownPage->comments()->latest()->first();

        // no comment-update-all
        $this->actingAs($this->user)->updateComment($comment)->assertStatus(403);

        $this->permissions->grantUserRolePermissions($this->user, ['comment-update-all']);

        // now has comment-update-all
        $this->actingAs($this->user)->updateComment($comment)->assertOk();
    }

    public function test_comment_delete_own_permission()
    {
        /** @var Page $ownPage */
        $ownPage = $this->entities->createChainBelongingToUser($this->user)['page'];
        $this->permissions->grantUserRolePermissions($this->user, ['comment-create-all']);
        $this->actingAs($this->user)->addComment($ownPage);

        /** @var Comment $comment */
        $comment = $ownPage->comments()->latest()->first();

        // no comment-delete-own
        $this->actingAs($this->user)->deleteComment($comment)->assertStatus(403);

        $this->permissions->grantUserRolePermissions($this->user, ['comment-delete-own']);

        // now has comment-update-own
        $this->actingAs($this->user)->deleteComment($comment)->assertOk();
    }

    public function test_comment_delete_all_permission()
    {
        /** @var Page $ownPage */
        $ownPage = $this->entities->createChainBelongingToUser($this->user)['page'];
        $this->asAdmin()->addComment($ownPage);
        /** @var Comment $comment */
        $comment = $ownPage->comments()->latest()->first();

        // no comment-delete-all
        $this->actingAs($this->user)->deleteComment($comment)->assertStatus(403);

        $this->permissions->grantUserRolePermissions($this->user, ['comment-delete-all']);

        // now has comment-delete-all
        $this->actingAs($this->user)->deleteComment($comment)->assertOk();
    }

    private function addComment(Page $page): TestResponse
    {
        $comment = Comment::factory()->make();

        return $this->postJson("/comment/$page->id", $comment->only('text', 'html'));
    }

    private function updateComment(Comment $comment): TestResponse
    {
        $commentData = Comment::factory()->make();

        return $this->putJson("/comment/{$comment->id}", $commentData->only('text', 'html'));
    }

    private function deleteComment(Comment $comment): TestResponse
    {
        return $this->json('DELETE', '/comment/' . $comment->id);
    }
}
