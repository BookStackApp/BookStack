<?php namespace Tests;

use BookStack\Book;
use BookStack\Chapter;
use BookStack\Entity;
use BookStack\Repos\EntityRepo;
use BookStack\Role;
use BookStack\Services\PermissionService;
use BookStack\Services\SettingService;

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
        $user = \BookStack\Role::getRole('viewer')->users()->first();
        if (!empty($attributes)) $user->forceFill($attributes)->save();
        return $user;
    }

    /**
     * Create and return a new book.
     * @param array $input
     * @return Book
     */
    public function newBook($input = ['name' => 'test book', 'description' => 'My new test book']) {
        return $this->app[EntityRepo::class]->createFromInput('book', $input, false);
    }

    /**
     * Create and return a new test chapter
     * @param array $input
     * @param Book $book
     * @return Chapter
     */
    public function newChapter($input = ['name' => 'test chapter', 'description' => 'My new test chapter'], Book $book) {
        return $this->app[EntityRepo::class]->createFromInput('chapter', $input, $book);
    }

    /**
     * Create and return a new test page
     * @param array $input
     * @return Chapter
     */
    public function newPage($input = ['name' => 'test page', 'html' => 'My new test page']) {
        $book = Book::first();
        $entityRepo = $this->app[EntityRepo::class];
        $draftPage = $entityRepo->getDraftPage($book);
        return $entityRepo->publishPageDraft($draftPage, $input);
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

}