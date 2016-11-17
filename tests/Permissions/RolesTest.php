<?php

class RolesTest extends TestCase
{
    protected $user;

    public function setUp()
    {
        parent::setUp();
        $this->user = $this->getViewer();
    }

    protected function getViewer()
    {
        $role = \BookStack\Role::getRole('viewer');
        $viewer = $this->getNewBlankUser();
        $viewer->attachRole($role);;
        return $viewer;
    }

    /**
     * Give the given user some permissions.
     * @param \BookStack\User $user
     * @param array $permissions
     */
    protected function giveUserPermissions(\BookStack\User $user, $permissions = [])
    {
        $newRole = $this->createNewRole($permissions);
        $user->attachRole($newRole);
        $user->load('roles');
        $user->permissions(false);
    }

    /**
     * Create a new basic role for testing purposes.
     * @param array $permissions
     * @return static
     */
    protected function createNewRole($permissions = [])
    {
        $permissionRepo = app('BookStack\Repos\PermissionsRepo');
        $roleData = factory(\BookStack\Role::class)->make()->toArray();
        $roleData['permissions'] = array_flip($permissions);
        return $permissionRepo->saveNewRole($roleData);
    }

    public function test_admin_can_see_settings()
    {
        $this->asAdmin()->visit('/settings')->see('Settings');
    }

    public function test_cannot_delete_admin_role()
    {
        $adminRole = \BookStack\Role::getRole('admin');
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
            ->click('Add new role')
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
        $this->actingAs($this->user)->visit('/')->visit('/settings/users')
            ->seePageIs('/');
        $this->giveUserPermissions($this->user, ['users-manage']);
        $this->actingAs($this->user)->visit('/')->visit('/settings/users')
            ->seePageIs('/settings/users');
    }

    public function test_user_roles_manage_permission()
    {
        $this->actingAs($this->user)->visit('/')->visit('/settings/roles')
            ->seePageIs('/')->visit('/settings/roles/1')->seePageIs('/');
        $this->giveUserPermissions($this->user, ['user-roles-manage']);
        $this->actingAs($this->user)->visit('/settings/roles')
            ->seePageIs('/settings/roles')->click('Admin')
            ->see('Edit Role');
    }

    public function test_settings_manage_permission()
    {
        $this->actingAs($this->user)->visit('/')->visit('/settings')
            ->seePageIs('/');
        $this->giveUserPermissions($this->user, ['settings-manage']);
        $this->actingAs($this->user)->visit('/')->visit('/settings')
            ->seePageIs('/settings')->press('Save Settings')->see('Settings Saved');
    }

    public function test_restrictions_manage_all_permission()
    {
        $page = \BookStack\Page::take(1)->get()->first();
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
        $otherUsersPage = \BookStack\Page::first();
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
     * @param null $callback
     */
    private function checkAccessPermission($permission, $accessUrls = [], $visibles = [])
    {
        foreach ($accessUrls as $url) {
            $this->actingAs($this->user)->visit('/')->visit($url)
                ->seePageIs('/');
        }
        foreach ($visibles as $url => $text) {
            $this->actingAs($this->user)->visit('/')->visit($url)
                ->dontSeeInElement('.action-buttons',$text);
        }

        $this->giveUserPermissions($this->user, [$permission]);

        foreach ($accessUrls as $url) {
            $this->actingAs($this->user)->visit('/')->visit($url)
                ->seePageIs($url);
        }
        foreach ($visibles as $url => $text) {
            $this->actingAs($this->user)->visit('/')->visit($url)
                ->see($text);
        }
    }

    public function test_books_create_all_permissions()
    {
        $this->checkAccessPermission('book-create-all', [
            '/books/create'
        ], [
            '/books' => 'Create New Book'
        ]);

        $this->visit('/books/create')
            ->type('test book', 'name')
            ->type('book desc', 'description')
            ->press('Save Book')
            ->seePageIs('/books/test-book');
    }

    public function test_books_edit_own_permission()
    {
        $otherBook = \BookStack\Book::take(1)->get()->first();
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
        $otherBook = \BookStack\Book::take(1)->get()->first();
        $this->checkAccessPermission('book-update-all', [
            $otherBook->getUrl() . '/edit'
        ], [
            $otherBook->getUrl() => 'Edit'
        ]);
    }

    public function test_books_delete_own_permission()
    {
        $this->giveUserPermissions($this->user, ['book-update-all']);
        $otherBook = \BookStack\Book::take(1)->get()->first();
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
        $otherBook = \BookStack\Book::take(1)->get()->first();
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
        $book = \BookStack\Book::take(1)->get()->first();
        $ownBook = $this->createEntityChainBelongingToUser($this->user)['book'];
        $baseUrl = $ownBook->getUrl() . '/chapter';
        $this->checkAccessPermission('chapter-create-own', [
            $baseUrl . '/create'
        ], [
            $ownBook->getUrl() => 'New Chapter'
        ]);

        $this->visit($baseUrl . '/create')
            ->type('test chapter', 'name')
            ->type('chapter desc', 'description')
            ->press('Save Chapter')
            ->seePageIs($baseUrl . '/test-chapter');

        $this->visit($book->getUrl())
            ->dontSeeInElement('.action-buttons', 'New Chapter')
            ->visit($book->getUrl() . '/chapter/create')
            ->seePageIs('/');
    }

    public function test_chapter_create_all_permissions()
    {
        $book = \BookStack\Book::take(1)->get()->first();
        $baseUrl = $book->getUrl() . '/chapter';
        $this->checkAccessPermission('chapter-create-all', [
            $baseUrl . '/create'
        ], [
            $book->getUrl() => 'New Chapter'
        ]);

        $this->visit($baseUrl . '/create')
            ->type('test chapter', 'name')
            ->type('chapter desc', 'description')
            ->press('Save Chapter')
            ->seePageIs($baseUrl . '/test-chapter');
    }

    public function test_chapter_edit_own_permission()
    {
        $otherChapter = \BookStack\Chapter::take(1)->get()->first();
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
        $otherChapter = \BookStack\Chapter::take(1)->get()->first();
        $this->checkAccessPermission('chapter-update-all', [
            $otherChapter->getUrl() . '/edit'
        ], [
            $otherChapter->getUrl() => 'Edit'
        ]);
    }

    public function test_chapter_delete_own_permission()
    {
        $this->giveUserPermissions($this->user, ['chapter-update-all']);
        $otherChapter = \BookStack\Chapter::take(1)->get()->first();
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
        $otherChapter = \BookStack\Chapter::take(1)->get()->first();
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
        $book = \BookStack\Book::take(1)->get()->first();
        $chapter = \BookStack\Chapter::take(1)->get()->first();

        $entities = $this->createEntityChainBelongingToUser($this->user);
        $ownBook = $entities['book'];
        $ownChapter = $entities['chapter'];

        $baseUrl = $ownBook->getUrl() . '/page';

        $createUrl = $baseUrl . '/create';
        $createUrlChapter = $ownChapter->getUrl() . '/create-page';
        $accessUrls = [$createUrl, $createUrlChapter];

        foreach ($accessUrls as $url) {
            $this->actingAs($this->user)->visit('/')->visit($url)
                ->seePageIs('/');
        }

        $this->checkAccessPermission('page-create-own', [], [
            $ownBook->getUrl() => 'New Page',
            $ownChapter->getUrl() => 'New Page'
        ]);

        $this->giveUserPermissions($this->user, ['page-create-own']);

        foreach ($accessUrls as $index => $url) {
            $this->actingAs($this->user)->visit('/')->visit($url);
            $expectedUrl = \BookStack\Page::where('draft', '=', true)->orderBy('id', 'desc')->first()->getUrl();
            $this->seePageIs($expectedUrl);
        }

        $this->visit($baseUrl . '/create')
            ->type('test page', 'name')
            ->type('page desc', 'html')
            ->press('Save Page')
            ->seePageIs($baseUrl . '/test-page');

        $this->visit($book->getUrl())
            ->dontSeeInElement('.action-buttons', 'New Page')
            ->visit($book->getUrl() . '/page/create')
            ->seePageIs('/');
        $this->visit($chapter->getUrl())
            ->dontSeeInElement('.action-buttons', 'New Page')
            ->visit($chapter->getUrl() . '/create-page')
            ->seePageIs('/');
    }

    public function test_page_create_all_permissions()
    {
        $book = \BookStack\Book::take(1)->get()->first();
        $chapter = \BookStack\Chapter::take(1)->get()->first();
        $baseUrl = $book->getUrl() . '/page';
        $createUrl = $baseUrl . '/create';

        $createUrlChapter = $chapter->getUrl() . '/create-page';
        $accessUrls = [$createUrl, $createUrlChapter];

        foreach ($accessUrls as $url) {
            $this->actingAs($this->user)->visit('/')->visit($url)
                ->seePageIs('/');
        }

        $this->checkAccessPermission('page-create-all', [], [
            $book->getUrl() => 'New Page',
            $chapter->getUrl() => 'New Page'
        ]);

        $this->giveUserPermissions($this->user, ['page-create-all']);

        foreach ($accessUrls as $index => $url) {
            $this->actingAs($this->user)->visit('/')->visit($url);
            $expectedUrl = \BookStack\Page::where('draft', '=', true)->orderBy('id', 'desc')->first()->getUrl();
            $this->seePageIs($expectedUrl);
        }

        $this->visit($baseUrl . '/create')
            ->type('test page', 'name')
            ->type('page desc', 'html')
            ->press('Save Page')
            ->seePageIs($baseUrl . '/test-page');

        $this->visit($chapter->getUrl() . '/create-page')
            ->type('new test page', 'name')
            ->type('page desc', 'html')
            ->press('Save Page')
            ->seePageIs($baseUrl . '/new-test-page');
    }

    public function test_page_edit_own_permission()
    {
        $otherPage = \BookStack\Page::take(1)->get()->first();
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
        $otherPage = \BookStack\Page::take(1)->get()->first();
        $this->checkAccessPermission('page-update-all', [
            $otherPage->getUrl() . '/edit'
        ], [
            $otherPage->getUrl() => 'Edit'
        ]);
    }

    public function test_page_delete_own_permission()
    {
        $this->giveUserPermissions($this->user, ['page-update-all']);
        $otherPage = \BookStack\Page::take(1)->get()->first();
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
        $otherPage = \BookStack\Page::take(1)->get()->first();
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
        $user = \BookStack\User::first();
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

}
