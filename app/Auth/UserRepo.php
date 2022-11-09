<?php

namespace BookStack\Auth;

use BookStack\Actions\ActivityType;
use BookStack\Auth\Access\UserInviteService;
use BookStack\Entities\EntityProvider;
use BookStack\Exceptions\NotifyException;
use BookStack\Exceptions\UserUpdateException;
use BookStack\Facades\Activity;
use BookStack\Uploads\UserAvatars;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserRepo
{
    protected UserAvatars $userAvatar;
    protected UserInviteService $inviteService;

    /**
     * UserRepo constructor.
     */
    public function __construct(UserAvatars $userAvatar, UserInviteService $inviteService)
    {
        $this->userAvatar = $userAvatar;
        $this->inviteService = $inviteService;
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
     * Update the given user with the given data.
     *
     * @param array{name: ?string, email: ?string, external_auth_id: ?string, password: ?string, roles: ?array<int>, language: ?string} $data
     *
     * @throws UserUpdateException
     */
    public function update(User $user, array $data, bool $manageUsersAllowed): User
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
        Activity::add(ActivityType::USER_UPDATE, $user);

        return $user;
    }

    /**
     * Remove the given user from storage, Delete all related content.
     *
     * @throws Exception
     */
    public function destroy(User $user, ?int $newOwnerId = null)
    {
        $this->ensureDeletable($user);

        $user->socialAccounts()->delete();
        $user->apiTokens()->delete();
        $user->favourites()->delete();
        $user->mfaValues()->delete();
        $user->delete();

        // Delete user profile images
        $this->userAvatar->destroyAllForUser($user);

        // Delete related activities
        setting()->deleteUserSettings($user->id);

        if (!empty($newOwnerId)) {
            $newOwner = User::query()->find($newOwnerId);
            if (!is_null($newOwner)) {
                $this->migrateOwnership($user, $newOwner);
            }
        }

        Activity::add(ActivityType::USER_DELETE, $user);
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
    protected function migrateOwnership(User $fromUser, User $toUser)
    {
        $entities = (new EntityProvider())->all();
        foreach ($entities as $instance) {
            $instance->newQuery()->where('owned_by', '=', $fromUser->id)
                ->update(['owned_by' => $toUser->id]);
        }
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
    protected function setUserRoles(User $user, array $roles)
    {
        if ($this->demotingLastAdmin($user, $roles)) {
            throw new UserUpdateException(trans('errors.role_cannot_remove_only_admin'), $user->getEditUrl());
        }

        $user->roles()->sync($roles);
    }

    /**
     * Check if the given user is the last admin and their new roles no longer
     * contains the admin role.
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
