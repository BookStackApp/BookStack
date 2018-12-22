<?php namespace BookStack\Auth;

use Activity;
use BookStack\Entities\Repos\EntityRepo;
use BookStack\Exceptions\NotFoundException;
use BookStack\Uploads\Image;
use Exception;
use Images;

class UserRepo
{

    protected $user;
    protected $role;
    protected $entityRepo;

    /**
     * UserRepo constructor.
     * @param User $user
     * @param Role $role
     * @param EntityRepo $entityRepo
     */
    public function __construct(User $user, Role $role, EntityRepo $entityRepo)
    {
        $this->user = $user;
        $this->role = $role;
        $this->entityRepo = $entityRepo;
    }

    /**
     * @param string $email
     * @return User|null
     */
    public function getByEmail($email)
    {
        return $this->user->where('email', '=', $email)->first();
    }

    /**
     * @param int $id
     * @return User
     */
    public function getById($id)
    {
        return $this->user->findOrFail($id);
    }

    /**
     * Get all the users with their permissions.
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function getAllUsers()
    {
        return $this->user->with('roles', 'avatar')->orderBy('name', 'asc')->get();
    }

    /**
     * Get all the users with their permissions in a paginated format.
     * @param int $count
     * @param $sortData
     * @return \Illuminate\Database\Eloquent\Builder|static
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
     * @param array $data
     * @param boolean $verifyEmail
     * @return \BookStack\Auth\User
     */
    public function registerNew(array $data, $verifyEmail = false)
    {
        $user = $this->create($data, $verifyEmail);
        $this->attachDefaultRole($user);
        $this->downloadAndAssignUserAvatar($user);

        return $user;
    }

    /**
     * Give a user the default role. Used when creating a new user.
     * @param User $user
     */
    public function attachDefaultRole(User $user)
    {
        $roleId = setting('registration-role');
        if ($roleId !== false && $user->roles()->where('id', '=', $roleId)->count() === 0) {
            $user->attachRoleId($roleId);
        }
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
     * @param \BookStack\Auth\User $user
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
     * Create a new basic instance of user.
     * @param array $data
     * @param boolean $verifyEmail
     * @return \BookStack\Auth\User
     */
    public function create(array $data, $verifyEmail = false)
    {

        return $this->user->forceCreate([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => bcrypt($data['password']),
            'email_confirmed' => $verifyEmail
        ]);
    }

    /**
     * Remove the given user from storage, Delete all related content.
     * @param \BookStack\Auth\User $user
     * @throws Exception
     */
    public function destroy(User $user)
    {
        $user->socialAccounts()->delete();
        $user->delete();
        
        // Delete user profile images
        $profileImages = $images = Image::where('type', '=', 'user')->where('created_by', '=', $user->id)->get();
        foreach ($profileImages as $image) {
            Images::destroy($image);
        }
    }

    /**
     * Get the latest activity for a user.
     * @param \BookStack\Auth\User $user
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
     * @param \BookStack\Auth\User $user
     * @param int $count
     * @return mixed
     */
    public function getRecentlyCreated(User $user, $count = 20)
    {
        return [
            'pages'    => $this->entityRepo->getRecentlyCreated('page', $count, 0, function ($query) use ($user) {
                $query->where('created_by', '=', $user->id);
            }),
            'chapters' => $this->entityRepo->getRecentlyCreated('chapter', $count, 0, function ($query) use ($user) {
                $query->where('created_by', '=', $user->id);
            }),
            'books'    => $this->entityRepo->getRecentlyCreated('book', $count, 0, function ($query) use ($user) {
                $query->where('created_by', '=', $user->id);
            })
        ];
    }

    /**
     * Get asset created counts for the give user.
     * @param \BookStack\Auth\User $user
     * @return array
     */
    public function getAssetCounts(User $user)
    {
        return [
            'pages'    => $this->entityRepo->getUserTotalCreated('page', $user),
            'chapters' => $this->entityRepo->getUserTotalCreated('chapter', $user),
            'books'    => $this->entityRepo->getUserTotalCreated('book', $user),
        ];
    }

    /**
     * Get the roles in the system that are assignable to a user.
     * @return mixed
     */
    public function getAllRoles()
    {
        return $this->role->all();
    }

    /**
     * Get all the roles which can be given restricted access to
     * other entities in the system.
     * @return mixed
     */
    public function getRestrictableRoles()
    {
        return $this->role->where('system_name', '!=', 'admin')->get();
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
            \Log::error('Failed to save user avatar image');
            return false;
        }
    }
}
