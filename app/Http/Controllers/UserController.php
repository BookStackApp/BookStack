<?php namespace BookStack\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use BookStack\Repos\UserRepo;
use BookStack\Services\SocialAuthService;
use BookStack\User;

class UserController extends Controller
{

    protected $user;
    protected $userRepo;

    /**
     * UserController constructor.
     * @param User     $user
     * @param UserRepo $userRepo
     */
    public function __construct(User $user, UserRepo $userRepo)
    {
        $this->user = $user;
        $this->userRepo = $userRepo;
        parent::__construct();
    }

    /**
     * Display a listing of the users.
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->checkPermission('users-manage');
        $listDetails = [
            'order' => $request->get('order', 'asc'),
            'search' => $request->get('search', ''),
            'sort' => $request->get('sort', 'name'),
        ];
        $users = $this->userRepo->getAllUsersPaginatedAndSorted(20, $listDetails);
        $this->setPageTitle(trans('settings.users'));
        $users->appends($listDetails);
        return view('users/index', ['users' => $users, 'listDetails' => $listDetails]);
    }

    /**
     * Show the form for creating a new user.
     * @return Response
     */
    public function create()
    {
        $this->checkPermission('users-manage');
        $authMethod = config('auth.method');
        $roles = $this->userRepo->getAllRoles();
        return view('users/create', ['authMethod' => $authMethod, 'roles' => $roles]);
    }

    /**
     * Store a newly created user in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->checkPermission('users-manage');
        $validationRules = [
            'name'             => 'required',
            'email'            => 'required|email|unique:users,email'
        ];

        $authMethod = config('auth.method');
        if ($authMethod === 'standard') {
            $validationRules['password'] = 'required|min:5';
            $validationRules['password-confirm'] = 'required|same:password';
        } elseif ($authMethod === 'ldap') {
            $validationRules['external_auth_id'] = 'required';
        }
        $this->validate($request, $validationRules);

        $user = $this->user->fill($request->all());

        if ($authMethod === 'standard') {
            $user->password = bcrypt($request->get('password'));
        } elseif ($authMethod === 'ldap') {
            $user->external_auth_id = $request->get('external_auth_id');
        }

        $user->save();

        if ($request->filled('roles')) {
            $roles = $request->get('roles');
            $user->roles()->sync($roles);
        }

        // Get avatar from gravatar and save
        if (!config('services.disable_services')) {
            try {
                $avatar = \Images::saveUserGravatar($user);
                $user->avatar()->associate($avatar);
                $user->save();
            } catch (Exception $e) {
                \Log::error('Failed to save user gravatar image');
            }

        }

        return redirect('/settings/users');
    }

    /**
     * Show the form for editing the specified user.
     * @param  int              $id
     * @param SocialAuthService $socialAuthService
     * @return Response
     */
    public function edit($id, SocialAuthService $socialAuthService)
    {
        $this->checkPermissionOr('users-manage', function () use ($id) {
            return $this->currentUser->id == $id;
        });

        $user = $this->user->findOrFail($id);

        $authMethod = ($user->system_name) ? 'system' : config('auth.method');

        $activeSocialDrivers = $socialAuthService->getActiveDrivers();
        $this->setPageTitle(trans('settings.user_profile'));
        $roles = $this->userRepo->getAllRoles();
        return view('users/edit', ['user' => $user, 'activeSocialDrivers' => $activeSocialDrivers, 'authMethod' => $authMethod, 'roles' => $roles]);
    }

    /**
     * Update the specified user in storage.
     * @param  Request $request
     * @param  int     $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $this->preventAccessForDemoUsers();
        $this->checkPermissionOr('users-manage', function () use ($id) {
            return $this->currentUser->id == $id;
        });

        $this->validate($request, [
            'name'             => 'min:2',
            'email'            => 'min:2|email|unique:users,email,' . $id,
            'password'         => 'min:5|required_with:password_confirm',
            'password-confirm' => 'same:password|required_with:password',
            'setting'          => 'array'
        ]);

        $user = $this->user->findOrFail($id);
        $user->fill($request->all());

        // Role updates
        if (userCan('users-manage') && $request->filled('roles')) {
            $roles = $request->get('roles');
            $user->roles()->sync($roles);
        }

        // Password updates
        if ($request->filled('password')) {
            $password = $request->get('password');
            $user->password = bcrypt($password);
        }

        // External auth id updates
        if ($this->currentUser->can('users-manage') && $request->filled('external_auth_id')) {
            $user->external_auth_id = $request->get('external_auth_id');
        }

        // Save an user-specific settings
        if ($request->filled('setting')) {
            foreach ($request->get('setting') as $key => $value) {
                setting()->putUser($user, $key, $value);
            }
        }

        $user->save();
        session()->flash('success', trans('settings.users_edit_success'));

        $redirectUrl = userCan('users-manage') ? '/settings/users' : '/settings/users/' . $user->id;
        return redirect($redirectUrl);
    }

    /**
     * Show the user delete page.
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function delete($id)
    {
        $this->checkPermissionOr('users-manage', function () use ($id) {
            return $this->currentUser->id == $id;
        });

        $user = $this->user->findOrFail($id);
        $this->setPageTitle(trans('settings.users_delete_named', ['userName' => $user->name]));
        return view('users/delete', ['user' => $user]);
    }

    /**
     * Remove the specified user from storage.
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        $this->preventAccessForDemoUsers();
        $this->checkPermissionOr('users-manage', function () use ($id) {
            return $this->currentUser->id == $id;
        });

        $user = $this->userRepo->getById($id);

        if ($this->userRepo->isOnlyAdmin($user)) {
            session()->flash('error', trans('errors.users_cannot_delete_only_admin'));
            return redirect($user->getEditUrl());
        }

        if ($user->system_name === 'public') {
            session()->flash('error', trans('errors.users_cannot_delete_guest'));
            return redirect($user->getEditUrl());
        }

        $this->userRepo->destroy($user);
        session()->flash('success', trans('settings.users_delete_success'));

        return redirect('/settings/users');
    }

    /**
     * Show the user profile page
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showProfilePage($id)
    {
        $user = $this->userRepo->getById($id);
        $userActivity = $this->userRepo->getActivity($user);
        $recentlyCreated = $this->userRepo->getRecentlyCreated($user, 5, 0);
        $assetCounts = $this->userRepo->getAssetCounts($user);
        return view('users/profile', [
            'user' => $user,
            'activity' => $userActivity,
            'recentlyCreated' => $recentlyCreated,
            'assetCounts' => $assetCounts
        ]);
    }
}
