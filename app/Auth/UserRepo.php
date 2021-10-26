<?php

namespace BookStack\Auth;

use Activity;
use BookStack\Entities\EntityProvider;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Exceptions\NotFoundException;
use BookStack\Exceptions\UserUpdateException;
use BookStack\Uploads\UserAvatars;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class UserRepo
{
    protected $userAvatar;

    /**
     * UserRepo constructor.
     */
    public function __construct(UserAvatars $userAvatar)
    {
        $this->userAvatar = $userAvatar;
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
     * Get all the users with their permissions.
     */
    public function getAllUsers(): Collection
    {
        return User::query()->with('roles', 'avatar')->orderBy('name', 'asc')->get();
    }

    /**
     * Get all the users with their permissions in a paginated format.
     */
    public function getAllUsersPaginatedAndSorted(int $count, array $sortData): LengthAwarePaginator
    {
        $sort = $sortData['sort'];

        $query = User::query()->select(['*'])
            ->withLastActivityAt()
            ->with(['roles', 'avatar'])
            ->withCount('mfaValues')
            ->orderBy($sort, $sortData['order']);

        if ($sortData['search']) {
            $term = '%' . $sortData['search'] . '%';
            $query->where(function ($query) use ($term) {
                $query->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term);
            });
        }

        return $query->paginate($count);
    }

    /**
     * Creates a new user and attaches a role to them.
     */
    public function registerNew(array $data, bool $emailConfirmed = false): User
    {
        $user = $this->create($data, $emailConfirmed);
        $user->attachDefaultRole();
        $this->downloadAndAssignUserAvatar($user);

        return $user;
    }

    /**
     * Assign a user to a system-level role.
     *
     * @throws NotFoundException
     */
    public function attachSystemRole(User $user, string $systemRoleName)
    {
        $role = Role::getSystemRole($systemRoleName);
        if (is_null($role)) {
            throw new NotFoundException("Role '{$systemRoleName}' not found");
        }
        $user->attachRole($role);
    }

    /**
     * Checks if the give user is the only admin.
     */
    public function isOnlyAdmin(User $user): bool
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
    public function setUserRoles(User $user, array $roles)
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

    /**
     * Create a new basic instance of user.
     */
    public function create(array $data, bool $emailConfirmed = false): User
    {
        $details = [
            'name'             => $data['name'],
            'email'            => $data['email'],
            'password'         => bcrypt($data['password']),
            'email_confirmed'  => $emailConfirmed,
            'external_auth_id' => $data['external_auth_id'] ?? '',
        ];

        $user = new User();
        $user->forceFill($details);
        $user->refreshSlug();
        $user->save();

        return $user;
    }

    /**
     * Remove the given user from storage, Delete all related content.
     *
     * @throws Exception
     */
    public function destroy(User $user, ?int $newOwnerId = null)
    {
        $user->socialAccounts()->delete();
        $user->apiTokens()->delete();
        $user->favourites()->delete();
        $user->mfaValues()->delete();
        $user->delete();

        // Delete user profile images
        $this->userAvatar->destroyAllForUser($user);

        if (!empty($newOwnerId)) {
            $newOwner = User::query()->find($newOwnerId);
            if (!is_null($newOwner)) {
                $this->migrateOwnership($user, $newOwner);
            }
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
     * Get the latest activity for a user.
     */
    public function getActivity(User $user, int $count = 20, int $page = 0): array
    {
        return Activity::userActivity($user, $count, $page);
    }

    /**
     * Get the recently created content for this given user.
     */
    public function getRecentlyCreated(User $user, int $count = 20): array
    {
        $query = function (Builder $query) use ($user, $count) {
            return $query->orderBy('created_at', 'desc')
                ->where('created_by', '=', $user->id)
                ->take($count)
                ->get();
        };

        return [
            'pages'    => $query(Page::visible()->where('draft', '=', false)),
            'chapters' => $query(Chapter::visible()),
            'books'    => $query(Book::visible()),
            'shelves'  => $query(Bookshelf::visible()),
        ];
    }

    /**
     * Get asset created counts for the give user.
     */
    public function getAssetCounts(User $user): array
    {
        $createdBy = ['created_by' => $user->id];

        return [
            'pages'       => Page::visible()->where($createdBy)->count(),
            'chapters'    => Chapter::visible()->where($createdBy)->count(),
            'books'       => Book::visible()->where($createdBy)->count(),
            'shelves'     => Bookshelf::visible()->where($createdBy)->count(),
        ];
    }

    /**
     * Get the roles in the system that are assignable to a user.
     */
    public function getAllRoles(): Collection
    {
        return Role::query()->orderBy('display_name', 'asc')->get();
    }

    /**
     * Get an avatar image for a user and set it as their avatar.
     * Returns early if avatars disabled or not set in config.
     */
    public function downloadAndAssignUserAvatar(User $user): void
    {
        try {
            $this->userAvatar->fetchAndAssignToUser($user);
        } catch (Exception $e) {
            Log::error('Failed to save user avatar image');
        }
    }
}
