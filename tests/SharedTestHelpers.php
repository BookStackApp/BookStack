<?php namespace Tests;

use BookStack\Auth\User;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Repos\BookRepo;
use BookStack\Entities\Repos\BookshelfRepo;
use BookStack\Entities\Repos\ChapterRepo;
use BookStack\Auth\Permissions\PermissionsRepo;
use BookStack\Auth\Role;
use BookStack\Auth\Permissions\PermissionService;
use BookStack\Entities\Repos\PageRepo;
use BookStack\Settings\SettingService;
use BookStack\Uploads\HttpFetcher;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Log;
use Mockery;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Illuminate\Foundation\Testing\Assert as PHPUnit;

trait SharedTestHelpers
{

    protected $admin;
    protected $editor;

    /**
     * Set the current user context to be an admin.
     */
    public function asAdmin()
    {
        return $this->actingAs($this->getAdmin());
    }

    /**
     * Get the current admin user.
     */
    public function getAdmin(): User
    {
        if (is_null($this->admin)) {
            $adminRole = Role::getSystemRole('admin');
            $this->admin = $adminRole->users->first();
        }

        return $this->admin;
    }

    /**
     * Set the current user context to be an editor.
     */
    public function asEditor()
    {
        return $this->actingAs($this->getEditor());
    }


    /**
     * Get a editor user.
     */
    protected function getEditor(): User
    {
        if ($this->editor === null) {
            $editorRole = Role::getRole('editor');
            $this->editor = $editorRole->users->first();
        }
        return $this->editor;
    }

    /**
     * Get an instance of a user with 'viewer' permissions.
     */
    protected function getViewer(array $attributes = []): User
    {
        $user = Role::getRole('viewer')->users()->first();
        if (!empty($attributes)) {
            $user->forceFill($attributes)->save();
        }
        return $user;
    }

    /**
     * Regenerate the permission for an entity.
     */
    protected function regenEntityPermissions(Entity $entity): void
    {
        $entity->rebuildPermissions();
        $entity->load('jointPermissions');
    }

    /**
     * Create and return a new bookshelf.
     */
    public function newShelf(array $input = ['name' => 'test shelf', 'description' => 'My new test shelf']): Bookshelf
    {
        return app(BookshelfRepo::class)->create($input, []);
    }

    /**
     * Create and return a new book.
     */
    public function newBook(array $input = ['name' => 'test book', 'description' => 'My new test book']): Book
    {
        return app(BookRepo::class)->create($input);
    }

    /**
     * Create and return a new test chapter
     */
    public function newChapter(array $input = ['name' => 'test chapter', 'description' => 'My new test chapter'], Book $book): Chapter
    {
        return app(ChapterRepo::class)->create($input, $book);
    }

    /**
     * Create and return a new test page
     */
    public function newPage(array $input = ['name' => 'test page', 'html' => 'My new test page']): Page
    {
        $book = Book::query()->first();
        $pageRepo = app(PageRepo::class);
        $draftPage = $pageRepo->getNewDraftPage($book);
        return $pageRepo->publishDraft($draftPage, $input);
    }

    /**
     * Quickly sets an array of settings.
     */
    protected function setSettings(array $settingsArray): void
    {
        $settings = app(SettingService::class);
        foreach ($settingsArray as $key => $value) {
            $settings->put($key, $value);
        }
    }

    /**
     * Manually set some permissions on an entity.
     */
    protected function setEntityRestrictions(Entity $entity, array $actions = [], array $roles = []): void
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
     */
    protected function giveUserPermissions(User $user, array $permissions = []): void
    {
        $newRole = $this->createNewRole($permissions);
        $user->attachRole($newRole);
        $user->load('roles');
        $user->clearPermissionCache();
    }

    /**
     * Create a new basic role for testing purposes.
     */
    protected function createNewRole(array $permissions = []): Role
    {
        $permissionRepo = app(PermissionsRepo::class);
        $roleData = factory(Role::class)->make()->toArray();
        $roleData['permissions'] = array_flip($permissions);
        return $permissionRepo->saveNewRole($roleData);
    }

    /**
     * Mock the HttpFetcher service and return the given data on fetch.
     */
    protected function mockHttpFetch($returnData, int $times = 1)
    {
        $mockHttp = Mockery::mock(HttpFetcher::class);
        $this->app[HttpFetcher::class] = $mockHttp;
        $mockHttp->shouldReceive('fetch')
            ->times($times)
            ->andReturn($returnData);
    }

    /**
     * Run a set test with the given env variable.
     * Remembers the original and resets the value after test.
     */
    protected function runWithEnv(string $name, $value, callable $callback)
    {
        Env::disablePutenv();
        $originalVal = $_SERVER[$name] ?? null;

        if (is_null($value)) {
            unset($_SERVER[$name]);
        } else {
            $_SERVER[$name] = $value;
        }

        $this->refreshApplication();
        $callback();

        if (is_null($originalVal)) {
            unset($_SERVER[$name]);
        } else {
            $_SERVER[$name] = $originalVal;
        }
    }

    /**
     * Check the keys and properties in the given map to include
     * exist, albeit not exclusively, within the map to check.
     */
    protected function assertArrayMapIncludes(array $mapToInclude, array $mapToCheck, string $message = ''): void
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

    /**
     * Assert a permission error has occurred.
     */
    protected function assertPermissionError($response)
    {
        PHPUnit::assertTrue($this->isPermissionError($response->baseResponse ?? $response->response), "Failed asserting the response contains a permission error.");
    }

    /**
     * Assert a permission error has occurred.
     */
    protected function assertNotPermissionError($response)
    {
        PHPUnit::assertFalse($this->isPermissionError($response->baseResponse ?? $response->response), "Failed asserting the response does not contain a permission error.");
    }

    /**
     * Check if the given response is a permission error.
     */
    private function isPermissionError($response): bool
    {
        return $response->status() === 302
            && $response->headers->get('Location') === url('/')
            && strpos(session()->pull('error', ''), 'You do not have permission to access') === 0;
    }

    /**
     * Set a test handler as the logging interface for the application.
     * Allows capture of logs for checking against during tests.
     */
    protected function withTestLogger(): TestHandler
    {
        $monolog = new Logger('testing');
        $testHandler = new TestHandler();
        $monolog->pushHandler($testHandler);

        Log::extend('testing', function () use ($monolog) {
            return $monolog;
        });
        Log::setDefaultDriver('testing');

        return $testHandler;
    }

}