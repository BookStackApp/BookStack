<?php

namespace Tests\Helpers;

use BookStack\Permissions\PermissionsRepo;
use BookStack\Users\Models\Role;
use BookStack\Users\Models\User;

class UserRoleProvider
{
    protected ?User $admin = null;
    protected ?User $editor = null;

    /**
     * Get a typical "Admin" user.
     */
    public function admin(): User
    {
        if (is_null($this->admin)) {
            $adminRole = Role::getSystemRole('admin');
            $this->admin = $adminRole->users()->first();
        }

        return $this->admin;
    }

    /**
     * Get a typical "Editor" user.
     */
    public function editor(): User
    {
        if ($this->editor === null) {
            $editorRole = Role::getRole('editor');
            $this->editor = $editorRole->users->first();
        }

        return $this->editor;
    }

    /**
     * Get a typical "Viewer" user.
     */
    public function viewer(array $attributes = []): User
    {
        $user = Role::getRole('viewer')->users()->first();
        if (!empty($attributes)) {
            $user->forceFill($attributes)->save();
        }

        return $user;
    }

    /**
     * Get the system "guest" user.
     */
    public function guest(): User
    {
        return User::getDefault();
    }

    /**
     * Create a new fresh user without any relations.
     */
    public function newUser(array $attrs = []): User
    {
        return User::factory()->create($attrs);
    }

    /**
     * Create a new fresh user, with the given attrs, that has assigned a fresh role
     * that has the given role permissions.
     * Intended as a helper to create a blank slate baseline user and role.
     * @return array{0: User, 1: Role}
     */
    public function newUserWithRole(array $userAttrs = [], array $rolePermissions = []): array
    {
        $user = $this->newUser($userAttrs);
        $role = $this->attachNewRole($user, $rolePermissions);

        return [$user, $role];
    }

    /**
     * Attach a new role, with the given role permissions, to the given user
     * and return that role.
     */
    public function attachNewRole(User $user, array $rolePermissions = []): Role
    {
        $role = $this->createRole($rolePermissions);
        $user->attachRole($role);
        return $role;
    }

    /**
     * Create a new basic role with the given role permissions.
     */
    public function createRole(array $rolePermissions = []): Role
    {
        $permissionRepo = app(PermissionsRepo::class);
        $roleData = Role::factory()->make()->toArray();
        $roleData['permissions'] = $rolePermissions;

        return $permissionRepo->saveNewRole($roleData);
    }
}
