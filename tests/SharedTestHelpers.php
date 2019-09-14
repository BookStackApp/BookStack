<?php namespace Tests;

use BookStack\Entities\Book;
use BookStack\Entities\Entity;
use BookStack\Entities\Page;
use BookStack\Entities\Repos\EntityRepo;
use BookStack\Auth\Permissions\PermissionsRepo;
use BookStack\Auth\Role;
use BookStack\Auth\Permissions\PermissionService;
use BookStack\Entities\Repos\PageRepo;
use BookStack\Settings\SettingService;
use BookStack\Uploads\HttpFetcher;
use Illuminate\Support\Env;

trait SharedTestHelpers
{

    protected $admin;
    protected $editor;

    /**
     * Set the current user context to be an admin.
     * @return $this
     */
    public function asAdmin()
    {
        return $this->actingAs($this->getAdmin());
    }

    /**
     * Get the current admin user.
     * @return mixed
     */
    public function getAdmin() {
        if($this->admin === null) {
            $adminRole = Role::getSystemRole('admin');
            $this->admin = $adminRole->users->first();
        }
        return $this->admin;
    }

    /**
     * Set the current user context to be an editor.
     * @return $this
     */
    public function asEditor()
    {
        return $this->actingAs($this->getEditor());
    }


    /**
     * Get a editor user.
     * @return mixed
     */
    protected function getEditor() {
        if($this->editor === null) {
            $editorRole = Role::getRole('editor');
            $this->editor = $editorRole->users->first();
        }
        return $this->editor;
    }

    /**
     * Get an instance of a user with 'viewer' permissions
     * @param $attributes
     * @return mixed
     */
    protected function getViewer($attributes = [])
    {
        $user = \BookStack\Auth\Role::getRole('viewer')->users()->first();
        if (!empty($attributes)) $user->forceFill($attributes)->save();
        return $user;
    }

    /**
     * Regenerate the permission for an entity.
     * @param Entity $entity
     * @throws \Throwable
     */
    protected function regenEntityPermissions(Entity $entity)
    {
        app(PermissionService::class)->buildJointPermissionsForEntity($entity);
        $entity->load('jointPermissions');
    }

    /**
     * Create and return a new bookshelf.
     * @param array $input
     * @return \BookStack\Entities\Bookshelf
     */
    public function newShelf($input = ['name' => 'test shelf', 'description' => 'My new test shelf']) {
        return app(EntityRepo::class)->createFromInput('bookshelf', $input, false);
    }

    /**
     * Create and return a new book.
     * @param array $input
     * @return Book
     */
    public function newBook($input = ['name' => 'test book', 'description' => 'My new test book']) {
        return app(EntityRepo::class)->createFromInput('book', $input, false);
    }

    /**
     * Create and return a new test chapter
     * @param array $input
     * @param Book $book
     * @return \BookStack\Entities\Chapter
     */
    public function newChapter($input = ['name' => 'test chapter', 'description' => 'My new test chapter'], Book $book) {
        return app(EntityRepo::class)->createFromInput('chapter', $input, $book);
    }

    /**
     * Create and return a new test page
     * @param array $input
     * @return Page
     * @throws \Throwable
     */
    public function newPage($input = ['name' => 'test page', 'html' => 'My new test page']) {
        $book = Book::first();
        $pageRepo = app(PageRepo::class);
        $draftPage = $pageRepo->getDraftPage($book);
        return $pageRepo->publishPageDraft($draftPage, $input);
    }

    /**
     * Quickly sets an array of settings.
     * @param $settingsArray
     */
    protected function setSettings($settingsArray)
    {
        $settings = app(SettingService::class);
        foreach ($settingsArray as $key => $value) {
            $settings->put($key, $value);
        }
    }

    /**
     * Manually set some permissions on an entity.
     * @param Entity $entity
     * @param array $actions
     * @param array $roles
     */
    protected function setEntityRestrictions(Entity $entity, $actions = [], $roles = [])
    {
        $entity->restricted = true;
        $entity->permissions()->delete();

        $permissions = [];
        foreach ($actions as $action) {
            foreach ($roles as $role) {
                $permissions[] = [
                    'role_id' => $role->id,
                    'action' => strtolower($action)
                ];
            }
        }
        $entity->permissions()->createMany($permissions);

        $entity->save();
        $entity->load('permissions');
        $this->app[PermissionService::class]->buildJointPermissionsForEntity($entity);
        $entity->load('jointPermissions');
    }

    /**
     * Give the given user some permissions.
     * @param \BookStack\Auth\User $user
     * @param array $permissions
     */
    protected function giveUserPermissions(\BookStack\Auth\User $user, $permissions = [])
    {
        $newRole = $this->createNewRole($permissions);
        $user->attachRole($newRole);
        $user->load('roles');
        $user->permissions(false);
    }

    /**
     * Create a new basic role for testing purposes.
     * @param array $permissions
     * @return Role
     */
    protected function createNewRole($permissions = [])
    {
        $permissionRepo = app(PermissionsRepo::class);
        $roleData = factory(Role::class)->make()->toArray();
        $roleData['permissions'] = array_flip($permissions);
        return $permissionRepo->saveNewRole($roleData);
    }

    /**
     * Mock the HttpFetcher service and return the given data on fetch.
     * @param $returnData
     * @param int $times
     */
    protected function mockHttpFetch($returnData, int $times = 1)
    {
        $mockHttp = \Mockery::mock(HttpFetcher::class);
        $this->app[HttpFetcher::class] = $mockHttp;
        $mockHttp->shouldReceive('fetch')
            ->times($times)
            ->andReturn($returnData);
    }

    /**
     * Run a set test with the given env variable.
     * Remembers the original and resets the value after test.
     * @param string $name
     * @param $value
     * @param callable $callback
     */
    protected function runWithEnv(string $name, $value, callable $callback)
    {
        Env::disablePutenv();
        $originalVal = $_ENV[$name] ?? null;

        if (is_null($value)) {
            unset($_ENV[$name]);
            unset($_SERVER[$name]);
        } else {
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }

        $this->refreshApplication();
        $callback();

        if (is_null($originalVal)) {
            unset($_SERVER[$name]);
            unset($_ENV[$name]);
        } else {
            $_SERVER[$name] = $originalVal;
            $_ENV[$name] = $originalVal;
        }
    }

    /**
     * Check the keys and properties in the given map to include
     * exist, albeit not exclusively, within the map to check.
     * @param array $mapToInclude
     * @param array $mapToCheck
     * @param string $message
     */
    protected function assertArrayMapIncludes(array $mapToInclude, array $mapToCheck, string $message = '') : void
    {
        $passed = true;

        foreach ($mapToInclude as $key => $value) {
            if (!isset($mapToCheck[$key]) || $mapToCheck[$key] !== $mapToInclude[$key]) {
                $passed = false;
            }
        }

        $toIncludeStr = print_r($mapToInclude, true);
        $toCheckStr = print_r($mapToCheck, true);
        self::assertThat($passed, self::isTrue(), "Failed asserting that given map:\n\n{$toCheckStr}\n\nincludes:\n\n{$toIncludeStr}");
    }

}