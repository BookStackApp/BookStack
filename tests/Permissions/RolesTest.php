<?php namespace Tests;

use BookStack\Entities\Bookshelf;
use BookStack\Entities\Page;
use BookStack\Auth\Permissions\PermissionsRepo;
use BookStack\Auth\Role;
use Laravel\BrowserKitTesting\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RolesTest extends BrowserKitTest
{
    protected $user;

    public function setUp()
    {
        parent::setUp();
        $this->user = $this->getViewer();
    }

    public function test_admin_can_see_settings()
    {
        $this->asAdmin()->visit('/settings')->see('Settings');
    }

    public function test_cannot_delete_admin_role()
    {
        $adminRole = \BookStack\Auth\Role::getRole('admin');
        $deletePageUrl = '/settings/roles/delete/' . $adminRole->id;
        $this->asAdmin()->visit($deletePageUrl)
            ->press('Confirm')
            ->seePageIs($deletePageUrl)
            ->see('cannot be deleted');
    }

    public function test_role_cannot_be_deleted_if_default()
    {
        $newRole = $this->createNewRole();
        $this->setSettings(['registration-role' => $newRole->id]);

        $deletePageUrl = '/settings/roles/delete/' . $newRole->id;
        $this->asAdmin()->visit($deletePageUrl)
            ->press('Confirm')
            ->seePageIs($deletePageUrl)
            ->see('cannot be deleted');
    }

    public function test_role_create_update_delete_flow()
    {
        $testRoleName = 'Test Role';
        $testRoleDesc = 'a little test description';
        $testRoleUpdateName = 'An Super Updated role';

        // Creation
        $this->asAdmin()->visit('/settings')
            ->click('Roles')
            ->seePageIs('/settings/roles')
            ->click('Create New Role')
            ->type('Test Role', 'display_name')
            ->type('A little test description', 'description')
            ->press('Save Role')
            ->seeInDatabase('roles', ['display_name' => $testRoleName, 'name' => 'test-role', 'description' => $testRoleDesc])
            ->seePageIs('/settings/roles');
        // Updating
        $this->asAdmin()->visit('/settings/roles')
            ->see($testRoleDesc)
            ->click($testRoleName)
            ->type($testRoleUpdateName, '#display_name')
            ->press('Save Role')
            ->seeInDatabase('roles', ['display_name' => $testRoleUpdateName, 'name' => 'test-role', 'description' => $testRoleDesc])
            ->seePageIs('/settings/roles');
        // Deleting
        $this->asAdmin()->visit('/settings/roles')
            ->click($testRoleUpdateName)
            ->click('Delete Role')
            ->see($testRoleUpdateName)
            ->press('Confirm')
            ->seePageIs('/settings/roles')
            ->dontSee($testRoleUpdateName);
    }

    public function test_manage_user_permission()
    {
        $this->actingAs($this->user)->visit('/settings/users')
            ->seePageIs('/');
        $this->giveUserPermissions($this->user, ['users-manage']);
        $this->actingAs($this->user)->visit('/settings/users')
            ->seePageIs('/settings/users');
    }

    public function test_user_roles_manage_permission()
    {
        $this->actingAs($this->user)->visit('/settings/roles')
            ->seePageIs('/')->visit('/settings/roles/1')->seePageIs('/');
        $this->giveUserPermissions($this->user, ['user-roles-manage']);
        $this->actingAs($this->user)->visit('/settings/roles')
            ->seePageIs('/settings/roles')->click('Admin')
            ->see('Edit Role');
    }

    public function test_settings_manage_permission()
    {
        $this->actingAs($this->user)->visit('/settings')
            ->seePageIs('/');
        $this->giveUserPermissions($this->user, ['settings-manage']);
        $this->actingAs($this->user)->visit('/settings')
            ->seePageIs('/settings')->press('Save Settings')->see('Settings Saved');
    }

    public function test_restrictions_manage_all_permission()
    {
        $page = \BookStack\Entities\Page::take(1)->get()->first();
        $this->actingAs($this->user)->visit($page->getUrl())
            ->dontSee('Permissions')
            ->visit($page->getUrl() . '/permissions')
            ->seePageIs('/');
        $this->giveUserPermissions($this->user, ['restrictions-manage-all']);
        $this->actingAs($this->user)->visit($page->getUrl())
            ->see('Permissions')
            ->click('Permissions')
            ->see('Page Permissions')->seePageIs($page->getUrl() . '/permissions');
    }

    public function test_restrictions_manage_own_permission()
    {
        $otherUsersPage = \BookStack\Entities\Page::first();
        $content = $this->createEntityChainBelongingToUser($this->user);
        // Check can't restrict other's content
        $this->actingAs($this->user)->visit($otherUsersPage->getUrl())
            ->dontSee('Permissions')
            ->visit($otherUsersPage->getUrl() . '/permissions')
            ->seePageIs('/');
        // Check can't restrict own content
        $this->actingAs($this->user)->visit($content['page']->getUrl())
            ->dontSee('Permissions')
            ->visit($content['page']->getUrl() . '/permissions')
            ->seePageIs('/');

        $this->giveUserPermissions($this->user, ['restrictions-manage-own']);

        // Check can't restrict other's content
        $this->actingAs($this->user)->visit($otherUsersPage->getUrl())
            ->dontSee('Permissions')
            ->visit($otherUsersPage->getUrl() . '/permissions')
            ->seePageIs('/');
        // Check can restrict own content
        $this->actingAs($this->user)->visit($content['page']->getUrl())
            ->see('Permissions')
            ->click('Permissions')
            ->seePageIs($content['page']->getUrl() . '/permissions');
    }

    /**
     * Check a standard entity access permission
     * @param string $permission
     * @param array $accessUrls Urls that are only accessible after having the permission
     * @param array $visibles Check this text, In the buttons toolbar, is only visible with the permission
     */
    private function checkAccessPermission($permission, $accessUrls = [], $visibles = [])
    {
        foreach ($accessUrls as $url) {
            $this->actingAs($this->user)->visit($url)
                ->seePageIs('/');
        }
        foreach ($visibles as $url => $text) {
            $this->actingAs($this->user)->visit($url)
                ->dontSeeInElement('.action-buttons',$text);
        }

        $this->giveUserPermissions($this->user, [$permission]);

        foreach ($accessUrls as $url) {
            $this->actingAs($this->user)->visit($url)
                ->seePageIs($url);
        }
        foreach ($visibles as $url => $text) {
            $this->actingAs($this->user)->visit($url)
                ->see($text);
        }
    }

    public function test_bookshelves_create_all_permissions()
    {
        $this->checkAccessPermission('bookshelf-create-all', [
            '/create-shelf'
        ], [
            '/shelves' => 'Create New Shelf'
        ]);

        $this->visit('/create-shelf')
            ->type('test shelf', 'name')
            ->type('shelf desc', 'description')
            ->press('Save Shelf')
            ->seePageIs('/shelves/test-shelf');
    }

    public function test_bookshelves_edit_own_permission()
    {
        $otherShelf = Bookshelf::first();
        $ownShelf = $this->newShelf(['name' => 'test-shelf', 'slug' => 'test-shelf']);
        $ownShelf->forceFill(['created_by' => $this->user->id, 'updated_by' => $this->user->id])->save();
        $this->regenEntityPermissions($ownShelf);

        $this->checkAccessPermission('bookshelf-update-own', [
            $ownShelf->getUrl('/edit')
        ], [
            $ownShelf->getUrl() => 'Edit'
        ]);

        $this->visit($otherShelf->getUrl())
            ->dontSeeInElement('.action-buttons', 'Edit')
            ->visit($otherShelf->getUrl('/edit'))
            ->seePageIs('/');
    }

    public function test_bookshelves_edit_all_permission()
    {
        $otherShelf = \BookStack\Entities\Bookshelf::first();
        $this->checkAccessPermission('bookshelf-update-all', [
            $otherShelf->getUrl('/edit')
        ], [
            $otherShelf->getUrl() => 'Edit'
        ]);
    }

    public function test_bookshelves_delete_own_permission()
    {
        $this->giveUserPermissions($this->user, ['bookshelf-update-all']);
        $otherShelf = \BookStack\Entities\Bookshelf::first();
        $ownShelf = $this->newShelf(['name' => 'test-shelf', 'slug' => 'test-shelf']);
        $ownShelf->forceFill(['created_by' => $this->user->id, 'updated_by' => $this->user->id])->save();
        $this->regenEntityPermissions($ownShelf);

        $this->checkAccessPermission('bookshelf-delete-own', [
            $ownShelf->getUrl('/delete')
        ], [
            $ownShelf->getUrl() => 'Delete'
        ]);

        $this->visit($otherShelf->getUrl())
            ->dontSeeInElement('.action-buttons', 'Delete')
            ->visit($otherShelf->getUrl('/delete'))
            ->seePageIs('/');
        $this->visit($ownShelf->getUrl())->visit($ownShelf->getUrl('/delete'))
            ->press('Confirm')
            ->seePageIs('/shelves')
            ->dontSee($ownShelf->name);
    }

    public function test_bookshelves_delete_all_permission()
    {
        $this->giveUserPermissions($this->user, ['bookshelf-update-all']);
        $otherShelf = \BookStack\Entities\Bookshelf::first();
        $this->checkAccessPermission('bookshelf-delete-all', [
            $otherShelf->getUrl('/delete')
        ], [
            $otherShelf->getUrl() => 'Delete'
        ]);

        $this->visit($otherShelf->getUrl())->visit($otherShelf->getUrl('/delete'))
            ->press('Confirm')
            ->seePageIs('/shelves')
            ->dontSee($otherShelf->name);
    }

    public function test_books_create_all_permissions()
    {
        $this->checkAccessPermission('book-create-all', [
            '/create-book'
        ], [
            '/books' => 'Create New Book'
        ]);

        $this->visit('/create-book')
            ->type('test book', 'name')
            ->type('book desc', 'description')
            ->press('Save Book')
            ->seePageIs('/books/test-book');
    }

    public function test_books_edit_own_permission()
    {
        $otherBook = \BookStack\Entities\Book::take(1)->get()->first();
        $ownBook = $this->createEntityChainBelongingToUser($this->user)['book'];
        $this->checkAccessPermission('book-update-own', [
            $ownBook->getUrl() . '/edit'
        ], [
            $ownBook->getUrl() => 'Edit'
        ]);

        $this->visit($otherBook->getUrl())
            ->dontSeeInElement('.action-buttons', 'Edit')
            ->visit($otherBook->getUrl() . '/edit')
            ->seePageIs('/');
    }

    public function test_books_edit_all_permission()
    {
        $otherBook = \BookStack\Entities\Book::take(1)->get()->first();
        $this->checkAccessPermission('book-update-all', [
            $otherBook->getUrl() . '/edit'
        ], [
            $otherBook->getUrl() => 'Edit'
        ]);
    }

    public function test_books_delete_own_permission()
    {
        $this->giveUserPermissions($this->user, ['book-update-all']);
        $otherBook = \BookStack\Entities\Book::take(1)->get()->first();
        $ownBook = $this->createEntityChainBelongingToUser($this->user)['book'];
        $this->checkAccessPermission('book-delete-own', [
            $ownBook->getUrl() . '/delete'
        ], [
            $ownBook->getUrl() => 'Delete'
        ]);

        $this->visit($otherBook->getUrl())
            ->dontSeeInElement('.action-buttons', 'Delete')
            ->visit($otherBook->getUrl() . '/delete')
            ->seePageIs('/');
        $this->visit($ownBook->getUrl())->visit($ownBook->getUrl() . '/delete')
            ->press('Confirm')
            ->seePageIs('/books')
            ->dontSee($ownBook->name);
    }

    public function test_books_delete_all_permission()
    {
        $this->giveUserPermissions($this->user, ['book-update-all']);
        $otherBook = \BookStack\Entities\Book::take(1)->get()->first();
        $this->checkAccessPermission('book-delete-all', [
            $otherBook->getUrl() . '/delete'
        ], [
            $otherBook->getUrl() => 'Delete'
        ]);

        $this->visit($otherBook->getUrl())->visit($otherBook->getUrl() . '/delete')
            ->press('Confirm')
            ->seePageIs('/books')
            ->dontSee($otherBook->name);
    }

    public function test_chapter_create_own_permissions()
    {
        $book = \BookStack\Entities\Book::take(1)->get()->first();
        $ownBook = $this->createEntityChainBelongingToUser($this->user)['book'];
        $this->checkAccessPermission('chapter-create-own', [
            $ownBook->getUrl('/create-chapter')
        ], [
            $ownBook->getUrl() => 'New Chapter'
        ]);

        $this->visit($ownBook->getUrl('/create-chapter'))
            ->type('test chapter', 'name')
            ->type('chapter desc', 'description')
            ->press('Save Chapter')
            ->seePageIs($ownBook->getUrl('/chapter/test-chapter'));

        $this->visit($book->getUrl())
            ->dontSeeInElement('.action-buttons', 'New Chapter')
            ->visit($book->getUrl('/create-chapter'))
            ->seePageIs('/');
    }

    public function test_chapter_create_all_permissions()
    {
        $book = \BookStack\Entities\Book::take(1)->get()->first();
        $this->checkAccessPermission('chapter-create-all', [
            $book->getUrl('/create-chapter')
        ], [
            $book->getUrl() => 'New Chapter'
        ]);

        $this->visit($book->getUrl('/create-chapter'))
            ->type('test chapter', 'name')
            ->type('chapter desc', 'description')
            ->press('Save Chapter')
            ->seePageIs($book->getUrl('/chapter/test-chapter'));
    }

    public function test_chapter_edit_own_permission()
    {
        $otherChapter = \BookStack\Entities\Chapter::take(1)->get()->first();
        $ownChapter = $this->createEntityChainBelongingToUser($this->user)['chapter'];
        $this->checkAccessPermission('chapter-update-own', [
            $ownChapter->getUrl() . '/edit'
        ], [
            $ownChapter->getUrl() => 'Edit'
        ]);

        $this->visit($otherChapter->getUrl())
            ->dontSeeInElement('.action-buttons', 'Edit')
            ->visit($otherChapter->getUrl() . '/edit')
            ->seePageIs('/');
    }

    public function test_chapter_edit_all_permission()
    {
        $otherChapter = \BookStack\Entities\Chapter::take(1)->get()->first();
        $this->checkAccessPermission('chapter-update-all', [
            $otherChapter->getUrl() . '/edit'
        ], [
            $otherChapter->getUrl() => 'Edit'
        ]);
    }

    public function test_chapter_delete_own_permission()
    {
        $this->giveUserPermissions($this->user, ['chapter-update-all']);
        $otherChapter = \BookStack\Entities\Chapter::take(1)->get()->first();
        $ownChapter = $this->createEntityChainBelongingToUser($this->user)['chapter'];
        $this->checkAccessPermission('chapter-delete-own', [
            $ownChapter->getUrl() . '/delete'
        ], [
            $ownChapter->getUrl() => 'Delete'
        ]);

        $bookUrl = $ownChapter->book->getUrl();
        $this->visit($otherChapter->getUrl())
            ->dontSeeInElement('.action-buttons', 'Delete')
            ->visit($otherChapter->getUrl() . '/delete')
            ->seePageIs('/');
        $this->visit($ownChapter->getUrl())->visit($ownChapter->getUrl() . '/delete')
            ->press('Confirm')
            ->seePageIs($bookUrl)
            ->dontSeeInElement('.book-content', $ownChapter->name);
    }

    public function test_chapter_delete_all_permission()
    {
        $this->giveUserPermissions($this->user, ['chapter-update-all']);
        $otherChapter = \BookStack\Entities\Chapter::take(1)->get()->first();
        $this->checkAccessPermission('chapter-delete-all', [
            $otherChapter->getUrl() . '/delete'
        ], [
            $otherChapter->getUrl() => 'Delete'
        ]);

        $bookUrl = $otherChapter->book->getUrl();
        $this->visit($otherChapter->getUrl())->visit($otherChapter->getUrl() . '/delete')
            ->press('Confirm')
            ->seePageIs($bookUrl)
            ->dontSeeInElement('.book-content', $otherChapter->name);
    }

    public function test_page_create_own_permissions()
    {
        $book = \BookStack\Entities\Book::first();
        $chapter = \BookStack\Entities\Chapter::first();

        $entities = $this->createEntityChainBelongingToUser($this->user);
        $ownBook = $entities['book'];
        $ownChapter = $entities['chapter'];

        $createUrl = $ownBook->getUrl('/create-page');
        $createUrlChapter = $ownChapter->getUrl('/create-page');
        $accessUrls = [$createUrl, $createUrlChapter];

        foreach ($accessUrls as $url) {
            $this->actingAs($this->user)->visit($url)
                ->seePageIs('/');
        }

        $this->checkAccessPermission('page-create-own', [], [
            $ownBook->getUrl() => 'New Page',
            $ownChapter->getUrl() => 'New Page'
        ]);

        $this->giveUserPermissions($this->user, ['page-create-own']);

        foreach ($accessUrls as $index => $url) {
            $this->actingAs($this->user)->visit($url);
            $expectedUrl = \BookStack\Entities\Page::where('draft', '=', true)->orderBy('id', 'desc')->first()->getUrl();
            $this->seePageIs($expectedUrl);
        }

        $this->visit($createUrl)
            ->type('test page', 'name')
            ->type('page desc', 'html')
            ->press('Save Page')
            ->seePageIs($ownBook->getUrl('/page/test-page'));

        $this->visit($book->getUrl())
            ->dontSeeInElement('.action-buttons', 'New Page')
            ->visit($book->getUrl() . '/create-page')
            ->seePageIs('/');
        $this->visit($chapter->getUrl())
            ->dontSeeInElement('.action-buttons', 'New Page')
            ->visit($chapter->getUrl() . '/create-page')
            ->seePageIs('/');
    }

    public function test_page_create_all_permissions()
    {
        $book = \BookStack\Entities\Book::take(1)->get()->first();
        $chapter = \BookStack\Entities\Chapter::take(1)->get()->first();
        $baseUrl = $book->getUrl() . '/page';
        $createUrl = $book->getUrl('/create-page');

        $createUrlChapter = $chapter->getUrl('/create-page');
        $accessUrls = [$createUrl, $createUrlChapter];

        foreach ($accessUrls as $url) {
            $this->actingAs($this->user)->visit($url)
                ->seePageIs('/');
        }

        $this->checkAccessPermission('page-create-all', [], [
            $book->getUrl() => 'New Page',
            $chapter->getUrl() => 'New Page'
        ]);

        $this->giveUserPermissions($this->user, ['page-create-all']);

        foreach ($accessUrls as $index => $url) {
            $this->actingAs($this->user)->visit($url);
            $expectedUrl = \BookStack\Entities\Page::where('draft', '=', true)->orderBy('id', 'desc')->first()->getUrl();
            $this->seePageIs($expectedUrl);
        }

        $this->visit($createUrl)
            ->type('test page', 'name')
            ->type('page desc', 'html')
            ->press('Save Page')
            ->seePageIs($book->getUrl('/page/test-page'));

        $this->visit($chapter->getUrl('/create-page'))
            ->type('new test page', 'name')
            ->type('page desc', 'html')
            ->press('Save Page')
            ->seePageIs($book->getUrl('/page/new-test-page'));
    }

    public function test_page_edit_own_permission()
    {
        $otherPage = \BookStack\Entities\Page::take(1)->get()->first();
        $ownPage = $this->createEntityChainBelongingToUser($this->user)['page'];
        $this->checkAccessPermission('page-update-own', [
            $ownPage->getUrl() . '/edit'
        ], [
            $ownPage->getUrl() => 'Edit'
        ]);

        $this->visit($otherPage->getUrl())
            ->dontSeeInElement('.action-buttons', 'Edit')
            ->visit($otherPage->getUrl() . '/edit')
            ->seePageIs('/');
    }

    public function test_page_edit_all_permission()
    {
        $otherPage = \BookStack\Entities\Page::take(1)->get()->first();
        $this->checkAccessPermission('page-update-all', [
            $otherPage->getUrl() . '/edit'
        ], [
            $otherPage->getUrl() => 'Edit'
        ]);
    }

    public function test_page_delete_own_permission()
    {
        $this->giveUserPermissions($this->user, ['page-update-all']);
        $otherPage = \BookStack\Entities\Page::take(1)->get()->first();
        $ownPage = $this->createEntityChainBelongingToUser($this->user)['page'];
        $this->checkAccessPermission('page-delete-own', [
            $ownPage->getUrl() . '/delete'
        ], [
            $ownPage->getUrl() => 'Delete'
        ]);

        $bookUrl = $ownPage->book->getUrl();
        $this->visit($otherPage->getUrl())
            ->dontSeeInElement('.action-buttons', 'Delete')
            ->visit($otherPage->getUrl() . '/delete')
            ->seePageIs('/');
        $this->visit($ownPage->getUrl())->visit($ownPage->getUrl() . '/delete')
            ->press('Confirm')
            ->seePageIs($bookUrl)
            ->dontSeeInElement('.book-content', $ownPage->name);
    }

    public function test_page_delete_all_permission()
    {
        $this->giveUserPermissions($this->user, ['page-update-all']);
        $otherPage = \BookStack\Entities\Page::take(1)->get()->first();
        $this->checkAccessPermission('page-delete-all', [
            $otherPage->getUrl() . '/delete'
        ], [
            $otherPage->getUrl() => 'Delete'
        ]);

        $bookUrl = $otherPage->book->getUrl();
        $this->visit($otherPage->getUrl())->visit($otherPage->getUrl() . '/delete')
            ->press('Confirm')
            ->seePageIs($bookUrl)
            ->dontSeeInElement('.book-content', $otherPage->name);
    }

    public function test_public_role_visible_in_user_edit_screen()
    {
        $user = \BookStack\Auth\User::first();
        $this->asAdmin()->visit('/settings/users/' . $user->id)
            ->seeElement('#roles-admin')
            ->seeElement('#roles-public');
    }

    public function test_public_role_visible_in_role_listing()
    {
        $this->asAdmin()->visit('/settings/roles')
            ->see('Admin')
            ->see('Public');
    }

    public function test_public_role_visible_in_default_role_setting()
    {
        $this->asAdmin()->visit('/settings')
            ->seeElement('[data-role-name="admin"]')
            ->seeElement('[data-role-name="public"]');

    }

    public function test_public_role_not_deleteable()
    {
        $this->asAdmin()->visit('/settings/roles')
            ->click('Public')
            ->see('Edit Role')
            ->click('Delete Role')
            ->press('Confirm')
            ->see('Delete Role')
            ->see('Cannot be deleted');
    }

    public function test_image_delete_own_permission()
    {
        $this->giveUserPermissions($this->user, ['image-update-all']);
        $page = \BookStack\Entities\Page::first();
        $image = factory(\BookStack\Uploads\Image::class)->create(['uploaded_to' => $page->id, 'created_by' => $this->user->id, 'updated_by' => $this->user->id]);

        $this->actingAs($this->user)->json('delete', '/images/' . $image->id)
            ->seeStatusCode(403);

        $this->giveUserPermissions($this->user, ['image-delete-own']);

        $this->actingAs($this->user)->json('delete', '/images/' . $image->id)
            ->seeStatusCode(200)
            ->dontSeeInDatabase('images', ['id' => $image->id]);
    }

    public function test_image_delete_all_permission()
    {
        $this->giveUserPermissions($this->user, ['image-update-all']);
        $admin = $this->getAdmin();
        $page = \BookStack\Entities\Page::first();
        $image = factory(\BookStack\Uploads\Image::class)->create(['uploaded_to' => $page->id, 'created_by' => $admin->id, 'updated_by' => $admin->id]);

        $this->actingAs($this->user)->json('delete', '/images/' . $image->id)
            ->seeStatusCode(403);

        $this->giveUserPermissions($this->user, ['image-delete-own']);

        $this->actingAs($this->user)->json('delete', '/images/' . $image->id)
            ->seeStatusCode(403);

        $this->giveUserPermissions($this->user, ['image-delete-all']);

        $this->actingAs($this->user)->json('delete', '/images/' . $image->id)
            ->seeStatusCode(200)
            ->dontSeeInDatabase('images', ['id' => $image->id]);
    }

    public function test_role_permission_removal()
    {
        // To cover issue fixed in f99c8ff99aee9beb8c692f36d4b84dc6e651e50a.
        $page = Page::first();
        $viewerRole = \BookStack\Auth\Role::getRole('viewer');
        $viewer = $this->getViewer();
        $this->actingAs($viewer)->visit($page->getUrl())->assertResponseStatus(200);

        $this->asAdmin()->put('/settings/roles/' . $viewerRole->id, [
            'display_name' => $viewerRole->display_name,
            'description' => $viewerRole->description,
            'permission' => []
        ])->assertResponseStatus(302);

        $this->expectException(HttpException::class);
        $this->actingAs($viewer)->visit($page->getUrl())->assertResponseStatus(404);
    }

    public function test_empty_state_actions_not_visible_without_permission()
    {
        $admin = $this->getAdmin();
        // Book links
        $book = factory(\BookStack\Entities\Book::class)->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);
        $this->updateEntityPermissions($book);
        $this->actingAs($this->getViewer())->visit($book->getUrl())
            ->dontSee('Create a new page')
            ->dontSee('Add a chapter');

        // Chapter links
        $chapter = factory(\BookStack\Entities\Chapter::class)->create(['created_by' => $admin->id, 'updated_by' => $admin->id, 'book_id' => $book->id]);
        $this->updateEntityPermissions($chapter);
        $this->actingAs($this->getViewer())->visit($chapter->getUrl())
            ->dontSee('Create a new page')
            ->dontSee('Sort the current book');
    }

    public function test_comment_create_permission () {
        $ownPage = $this->createEntityChainBelongingToUser($this->user)['page'];

        $this->actingAs($this->user)->addComment($ownPage);

        $this->assertResponseStatus(403);

        $this->giveUserPermissions($this->user, ['comment-create-all']);

        $this->actingAs($this->user)->addComment($ownPage);
        $this->assertResponseStatus(200);
    }


    public function test_comment_update_own_permission () {
        $ownPage = $this->createEntityChainBelongingToUser($this->user)['page'];
        $this->giveUserPermissions($this->user, ['comment-create-all']);
        $commentId = $this->actingAs($this->user)->addComment($ownPage);

        // no comment-update-own
        $this->actingAs($this->user)->updateComment($commentId);
        $this->assertResponseStatus(403);

        $this->giveUserPermissions($this->user, ['comment-update-own']);

        // now has comment-update-own
        $this->actingAs($this->user)->updateComment($commentId);
        $this->assertResponseStatus(200);
    }

    public function test_comment_update_all_permission () {
        $ownPage = $this->createEntityChainBelongingToUser($this->user)['page'];
        $commentId = $this->asAdmin()->addComment($ownPage);

        // no comment-update-all
        $this->actingAs($this->user)->updateComment($commentId);
        $this->assertResponseStatus(403);

        $this->giveUserPermissions($this->user, ['comment-update-all']);

        // now has comment-update-all
        $this->actingAs($this->user)->updateComment($commentId);
        $this->assertResponseStatus(200);
    }

    public function test_comment_delete_own_permission () {
        $ownPage = $this->createEntityChainBelongingToUser($this->user)['page'];
        $this->giveUserPermissions($this->user, ['comment-create-all']);
        $commentId = $this->actingAs($this->user)->addComment($ownPage);

        // no comment-delete-own
        $this->actingAs($this->user)->deleteComment($commentId);
        $this->assertResponseStatus(403);

        $this->giveUserPermissions($this->user, ['comment-delete-own']);

        // now has comment-update-own
        $this->actingAs($this->user)->deleteComment($commentId);
        $this->assertResponseStatus(200);
    }

    public function test_comment_delete_all_permission () {
        $ownPage = $this->createEntityChainBelongingToUser($this->user)['page'];
        $commentId = $this->asAdmin()->addComment($ownPage);

        // no comment-delete-all
        $this->actingAs($this->user)->deleteComment($commentId);
        $this->assertResponseStatus(403);

        $this->giveUserPermissions($this->user, ['comment-delete-all']);

        // now has comment-delete-all
        $this->actingAs($this->user)->deleteComment($commentId);
        $this->assertResponseStatus(200);
    }

    private function addComment($page) {
        $comment = factory(\BookStack\Actions\Comment::class)->make();
        $url = "/ajax/page/$page->id/comment";
        $request = [
            'text' => $comment->text,
            'html' => $comment->html
        ];

        $this->postJson($url, $request);
        $comment = $page->comments()->first();
        return $comment === null ? null : $comment->id;
    }

    private function updateComment($commentId) {
        $comment = factory(\BookStack\Actions\Comment::class)->make();
        $url = "/ajax/comment/$commentId";
        $request = [
            'text' => $comment->text,
            'html' => $comment->html
        ];

        return $this->putJson($url, $request);
    }

    private function deleteComment($commentId) {
         $url = '/ajax/comment/' . $commentId;
         return $this->json('DELETE', $url);
    }

}
