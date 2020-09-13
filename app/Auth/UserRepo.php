<?php namespace BookStack\Auth;

use Activity;
use BookStack\Entities\Book;
use BookStack\Entities\Bookshelf;
use BookStack\Entities\Chapter;
use BookStack\Entities\Page;
use BookStack\Exceptions\NotFoundException;
use BookStack\Exceptions\UserUpdateException;
use BookStack\Uploads\Image;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Images;
use Log;

class UserRepo
{

    protected $user;
    protected $role;

    /**
     * UserRepo constructor.
     */
    public function __construct(User $user, Role $role)
    {
        $this->user = $user;
        $this->role = $role;
    }

    /**
     * Get a user by their email address.
     */
    public function getByEmail(string $email): ?User
    {
        return $this->user->where('email', '=', $email)->first();
    }

    /**
     * @param int $id
     * @return User
     */
    public function getById($id)
    {
        return $this->user->newQuery()->findOrFail($id);
    }

    /**
     * Get all the users with their permissions.
     * @return Builder|static
     */
    public function getAllUsers()
    {
        return $this->user->with('roles', 'avatar')->orderBy('name', 'asc')->get();
    }

    /**
     * Get all the users with their permissions in a paginated format.
     * @param int $count
     * @param $sortData
     * @return Builder|static
     */
    public function getAllUsersPaginatedAndSorted($count, $sortData)
    {
        $query = $this->user->with('roles', 'avatar')->orderBy($sortData['sort'], $sortData['order']);

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
     * @param User $user
     * @param $systemRoleName
     * @throws NotFoundException
     */
    public function attachSystemRole(User $user, $systemRoleName)
    {
        $role = $this->role->newQuery()->where('system_name', '=', $systemRoleName)->first();
        if ($role === null) {
            throw new NotFoundException("Role '{$systemRoleName}' not found");
        }
        $user->attachRole($role);
    }

    /**
     * Checks if the give user is the only admin.
     * @param User $user
     * @return bool
     */
    public function isOnlyAdmin(User $user)
    {
        if (!$user->hasSystemRole('admin')) {
            return false;
        }

        $adminRole = $this->role->getSystemRole('admin');
        if ($adminRole->users->count() > 1) {
            return false;
        }
        return true;
    }

    /**
     * Set the assigned user roles via an array of role IDs.
     * @param User $user
     * @param array $roles
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
     * @param User $user
     * @param array $newRoles
     * @return bool
     */
    protected function demotingLastAdmin(User $user, array $newRoles) : bool
    {
        if ($this->isOnlyAdmin($user)) {
            $adminRole = $this->role->getSystemRole('admin');
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
        return $this->user->forceCreate([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => bcrypt($data['password']),
            'email_confirmed' => $emailConfirmed,
            'external_auth_id' => $data['external_auth_id'] ?? '',
        ]);
    }

    /**
     * Remove the given user from storage, Delete all related content.
     * @param User $user
     * @throws Exception
     */
    public function destroy(User $user)
    {
        $user->socialAccounts()->delete();
        $user->apiTokens()->delete();
        $user->delete();
        
        // Delete user profile images
        $profileImages = Image::where('type', '=', 'user')->where('uploaded_to', '=', $user->id)->get();
        foreach ($profileImages as $image) {
            Images::destroy($image);
        }
    }

    /**
     * Get the latest activity for a user.
     * @param User $user
     * @param int $count
     * @param int $page
     * @return array
     */
    public function getActivity(User $user, $count = 20, $page = 0)
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
            'pages'    =>  Page::visible()->where($createdBy)->count(),
            'chapters'    =>  Chapter::visible()->where($createdBy)->count(),
            'books'    =>  Book::visible()->where($createdBy)->count(),
            'shelves'    =>  Bookshelf::visible()->where($createdBy)->count(),
        ];
    }

    /**
     * Get the roles in the system that are assignable to a user.
     * @return mixed
     */
    public function getAllRoles()
    {
        return $this->role->newQuery()->orderBy('display_name', 'asc')->get();
    }

    /**
     * Get an avatar image for a user and set it as their avatar.
     * Returns early if avatars disabled or not set in config.
     * @param User $user
     * @return bool
     */
    public function downloadAndAssignUserAvatar(User $user)
    {
        if (!Images::avatarFetchEnabled()) {
            return false;
        }

        try {
            $avatar = Images::saveUserAvatar($user);
            $user->avatar()->associate($avatar);
            $user->save();
            return true;
        } catch (Exception $e) {
            Log::error('Failed to save user avatar image');
            return false;
        }
    }
}
