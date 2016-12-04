<?php namespace BookStack\Repos;

use BookStack\Role;
use BookStack\User;
use Exception;

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
    public function getAllUsersPaginatedAndSorted($count = 20, $sortData)
    {
        $query = $this->user->with('roles', 'avatar')->orderBy($sortData['sort'], $sortData['order']);

        if ($sortData['search']) {
            $term = '%' . $sortData['search'] . '%';
            $query->where(function($query) use ($term) {
                $query->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term);
            });
        }

        return $query->paginate($count);
    }

    /**
     * Creates a new user and attaches a role to them.
     * @param array $data
     * @return User
     */
    public function registerNew(array $data)
    {
        $user = $this->create($data);
        $this->attachDefaultRole($user);

        // Get avatar from gravatar and save
        if (!config('services.disable_services')) {
            try {
                $avatar = \Images::saveUserGravatar($user);
                $user->avatar()->associate($avatar);
                $user->save();
            } catch (Exception $e) {
                $user->save();
                \Log::error('Failed to save user gravatar image');
            }
        }

        return $user;
    }

    /**
     * Give a user the default role. Used when creating a new user.
     * @param $user
     */
    public function attachDefaultRole($user)
    {
        $roleId = setting('registration-role');
        if ($roleId === false) $roleId = $this->role->first()->id;
        $user->attachRoleId($roleId);
    }

    /**
     * Checks if the give user is the only admin.
     * @param User $user
     * @return bool
     */
    public function isOnlyAdmin(User $user)
    {
        if (!$user->roles->pluck('name')->contains('admin')) return false;

        $adminRole = $this->role->getRole('admin');
        if ($adminRole->users->count() > 1) return false;
        return true;
    }

    /**
     * Create a new basic instance of user.
     * @param array $data
     * @return User
     */
    public function create(array $data)
    {
        return $this->user->forceCreate([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => bcrypt($data['password']),
            'email_confirmed' => false
        ]);
    }

    /**
     * Remove the given user from storage, Delete all related content.
     * @param User $user
     */
    public function destroy(User $user)
    {
        $user->socialAccounts()->delete();
        $user->delete();
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
        return \Activity::userActivity($user, $count, $page);
    }

    /**
     * Get the recently created content for this given user.
     * @param User $user
     * @param int $count
     * @return mixed
     */
    public function getRecentlyCreated(User $user, $count = 20)
    {
        return [
            'pages'    => $this->entityRepo->getRecentlyCreatedPages($count, 0, function ($query) use ($user) {
                $query->where('created_by', '=', $user->id);
            }),
            'chapters' => $this->entityRepo->getRecentlyCreatedChapters($count, 0, function ($query) use ($user) {
                $query->where('created_by', '=', $user->id);
            }),
            'books'    => $this->entityRepo->getRecentlyCreatedBooks($count, 0, function ($query) use ($user) {
                $query->where('created_by', '=', $user->id);
            })
        ];
    }

    /**
     * Get asset created counts for the give user.
     * @param User $user
     * @return array
     */
    public function getAssetCounts(User $user)
    {
        return [
            'pages'    => $this->entityRepo->page->where('created_by', '=', $user->id)->count(),
            'chapters' => $this->entityRepo->chapter->where('created_by', '=', $user->id)->count(),
            'books'    => $this->entityRepo->book->where('created_by', '=', $user->id)->count(),
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

}