<?php

namespace BookStack\Users;

use BookStack\Access\UserInviteException;
use BookStack\Access\UserInviteService;
use BookStack\Activity\ActivityType;
use BookStack\Exceptions\NotifyException;
use BookStack\Exceptions\UserUpdateException;
use BookStack\Facades\Activity;
use BookStack\Uploads\UserAvatars;
use BookStack\Users\Models\Role;
use BookStack\Users\Models\User;
use DB;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserRepo
{
    public function __construct(
        protected UserAvatars $userAvatar,
        protected UserInviteService $inviteService
    ) {
    }

    /**
     * Get a user by their email address.
     */
    public function getByEmail(string $email): ?User
    {
        return User::query()->where('email', '=', $email)->first();
    }

    /**
     * Get a user by their ID.
     */
    public function getById(int $id): User
    {
        return User::query()->findOrFail($id);
    }

    /**
     * Get a user by their slug.
     */
    public function getBySlug(string $slug): User
    {
        return User::query()->where('slug', '=', $slug)->firstOrFail();
    }

    /**
     * Create a new basic instance of user with the given pre-validated data.
     *
     * @param array{name: string, email: string, password: ?string, external_auth_id: ?string, language: ?string, roles: ?array} $data
     */
    public function createWithoutActivity(array $data, bool $emailConfirmed = false): User
    {
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make(empty($data['password']) ? Str::random(32) : $data['password']);
        $user->email_confirmed = $emailConfirmed;
        $user->external_auth_id = $data['external_auth_id'] ?? '';

        $user->refreshSlug();
        $user->save();

        if (!empty($data['language'])) {
            setting()->putUser($user, 'language', $data['language']);
        }

        if (isset($data['roles'])) {
            $this->setUserRoles($user, $data['roles']);
        }

        $this->downloadAndAssignUserAvatar($user);

        return $user;
    }

    /**
     * As per "createWithoutActivity" but records a "create" activity.
     *
     * @param array{name: string, email: string, password: ?string, external_auth_id: ?string, language: ?string, roles: ?array} $data
     * @throws UserInviteException
     */
    public function create(array $data, bool $sendInvite = false): User
    {
        $user = $this->createWithoutActivity($data, true);

        if ($sendInvite) {
            $this->inviteService->sendInvitation($user);
        }

        Activity::add(ActivityType::USER_CREATE, $user);

        return $user;
    }

    /**
     * Update the given user with the given data, but do not create an activity.
     *
     * @param array{name: ?string, email: ?string, external_auth_id: ?string, password: ?string, roles: ?array<int>, language: ?string} $data
     *
     * @throws UserUpdateException
     */
    public function updateWithoutActivity(User $user, array $data, bool $manageUsersAllowed): User
    {
        if (!empty($data['name'])) {
            $user->name = $data['name'];
            $user->refreshSlug();
        }

        if (!empty($data['email']) && $manageUsersAllowed) {
            $user->email = $data['email'];
        }

        if (!empty($data['external_auth_id']) && $manageUsersAllowed) {
            $user->external_auth_id = $data['external_auth_id'];
        }

        if (isset($data['roles']) && $manageUsersAllowed) {
            $this->setUserRoles($user, $data['roles']);
        }

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        if (!empty($data['language'])) {
            setting()->putUser($user, 'language', $data['language']);
        }

        $user->save();

        return $user;
    }

    /**
     * Update the given user with the given data.
     *
     * @param array{name: ?string, email: ?string, external_auth_id: ?string, password: ?string, roles: ?array<int>, language: ?string} $data
     *
     * @throws UserUpdateException
     */
    public function update(User $user, array $data, bool $manageUsersAllowed): User
    {
        $user = $this->updateWithoutActivity($user, $data, $manageUsersAllowed);

        Activity::add(ActivityType::USER_UPDATE, $user);

        return $user;
    }

    /**
     * Remove the given user from storage, Delete all related content.
     *
     * @throws Exception
     */
    public function destroy(User $user, ?int $newOwnerId = null): void
    {
        $this->ensureDeletable($user);

        $this->removeUserDependantRelations($user);
        $this->nullifyUserNonDependantRelations($user);
        $user->delete();

        // Delete user profile images
        $this->userAvatar->destroyAllForUser($user);

        // Delete related activities
        setting()->deleteUserSettings($user->id);

        // Migrate or nullify ownership
        $newOwner = null;
        if (!empty($newOwnerId)) {
            $newOwner = User::query()->find($newOwnerId);
        }
        $this->migrateOwnership($user, $newOwner);

        Activity::add(ActivityType::USER_DELETE, $user);
    }

    protected function removeUserDependantRelations(User $user): void
    {
        $user->apiTokens()->delete();
        $user->socialAccounts()->delete();
        $user->favourites()->delete();
        $user->mfaValues()->delete();
        $user->watches()->delete();

        $tables = ['email_confirmations', 'user_invites', 'views'];
        foreach ($tables as $table) {
            DB::table($table)->where('user_id', '=', $user->id)->delete();
        }
    }
    protected function nullifyUserNonDependantRelations(User $user): void
    {
        $toNullify = [
            'attachments' => ['created_by', 'updated_by'],
            'comments' => ['created_by', 'updated_by'],
            'deletions' => ['deleted_by'],
            'entities' => ['created_by', 'updated_by'],
            'images' => ['created_by', 'updated_by'],
            'imports' => ['created_by'],
            'joint_permissions' => ['owner_id'],
            'page_revisions' => ['created_by'],
            'sessions' => ['user_id'],
        ];

        foreach ($toNullify as $table => $columns) {
            foreach ($columns as $column) {
                DB::table($table)
                    ->where($column, '=', $user->id)
                    ->update([$column => null]);
            }
        }
    }

    /**
     * @throws NotifyException
     */
    protected function ensureDeletable(User $user): void
    {
        if ($this->isOnlyAdmin($user)) {
            throw new NotifyException(trans('errors.users_cannot_delete_only_admin'), $user->getEditUrl());
        }

        if ($user->system_name === 'public') {
            throw new NotifyException(trans('errors.users_cannot_delete_guest'), $user->getEditUrl());
        }
    }

    /**
     * Migrate ownership of items in the system from one user to another.
     */
    protected function migrateOwnership(User $fromUser, User|null $toUser): void
    {
        $newOwnerValue = $toUser ? $toUser->id : null;
        DB::table('entities')
            ->where('owned_by', '=', $fromUser->id)
            ->update(['owned_by' => $newOwnerValue]);
    }

    /**
     * Get an avatar image for a user and set it as their avatar.
     * Returns early if avatars disabled or not set in config.
     */
    protected function downloadAndAssignUserAvatar(User $user): void
    {
        try {
            $this->userAvatar->fetchAndAssignToUser($user);
        } catch (Exception $e) {
            Log::error('Failed to save user avatar image');
        }
    }

    /**
     * Checks if the give user is the only admin.
     */
    protected function isOnlyAdmin(User $user): bool
    {
        if (!$user->hasSystemRole('admin')) {
            return false;
        }

        $adminRole = Role::getSystemRole('admin');
        if ($adminRole->users()->count() > 1) {
            return false;
        }

        return true;
    }

    /**
     * Set the assigned user roles via an array of role IDs.
     *
     * @throws UserUpdateException
     */
    protected function setUserRoles(User $user, array $roles): void
    {
        $roles = array_filter(array_values($roles));

        if ($this->demotingLastAdmin($user, $roles)) {
            throw new UserUpdateException(trans('errors.role_cannot_remove_only_admin'), $user->getEditUrl());
        }

        $user->roles()->sync($roles);
    }

    /**
     * Check if the given user is the last admin and their new roles no longer
     * contain the admin role.
     */
    protected function demotingLastAdmin(User $user, array $newRoles): bool
    {
        if ($this->isOnlyAdmin($user)) {
            $adminRole = Role::getSystemRole('admin');
            if (!in_array(strval($adminRole->id), $newRoles)) {
                return true;
            }
        }

        return false;
    }
}
