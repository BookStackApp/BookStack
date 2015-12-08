<?php

namespace BookStack\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use BookStack\Http\Requests;
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
     *
     * @return Response
     */
    public function index()
    {
        $users = $this->user->all();
        $this->setPageTitle('Users');
        return view('users/index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new user.
     *
     * @return Response
     */
    public function create()
    {
        $this->checkPermission('user-create');
        return view('users/create');
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->checkPermission('user-create');
        $this->validate($request, [
            'name'             => 'required',
            'email'            => 'required|email',
            'password'         => 'required|min:5',
            'password-confirm' => 'required|same:password',
            'role'             => 'required|exists:roles,id'
        ]);

        $user = $this->user->fill($request->all());
        $user->password = bcrypt($request->get('password'));
        $user->save();

        $user->attachRoleId($request->get('role'));
        return redirect('/users');
    }


    /**
     * Show the form for editing the specified user.
     *
     * @param  int              $id
     * @param SocialAuthService $socialAuthService
     * @return Response
     */
    public function edit($id, SocialAuthService $socialAuthService)
    {
        $this->checkPermissionOr('user-update', function () use ($id) {
            return $this->currentUser->id == $id;
        });

        $user = $this->user->findOrFail($id);
        $activeSocialDrivers = $socialAuthService->getActiveDrivers();
        $this->setPageTitle('User Profile');
        return view('users/edit', ['user' => $user, 'activeSocialDrivers' => $activeSocialDrivers]);
    }

    /**
     * Update the specified user in storage.
     *
     * @param  Request $request
     * @param  int     $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $this->checkPermissionOr('user-update', function () use ($id) {
            return $this->currentUser->id == $id;
        });
        $this->validate($request, [
            'name'             => 'required',
            'email'            => 'required|email|unique:users,email,' . $id,
            'password'         => 'min:5',
            'password-confirm' => 'same:password',
            'role'             => 'exists:roles,id'
        ]);

        $user = $this->user->findOrFail($id);
        $user->fill($request->except('password'));

        if ($this->currentUser->can('user-update') && $request->has('role')) {
            $user->attachRoleId($request->get('role'));
        }

        if ($request->has('password') && $request->get('password') != '') {
            $password = $request->get('password');
            $user->password = bcrypt($password);
        }
        $user->save();
        return redirect('/users');
    }

    /**
     * Show the user delete page.
     * @param $id
     * @return \Illuminate\View\View
     */
    public function delete($id)
    {
        $this->checkPermissionOr('user-delete', function () use ($id) {
            return $this->currentUser->id == $id;
        });
        $user = $this->user->findOrFail($id);
        $this->setPageTitle('Delete User ' . $user->name);
        return view('users/delete', ['user' => $user]);
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        $this->checkPermissionOr('user-delete', function () use ($id) {
            return $this->currentUser->id == $id;
        });
        $user = $this->userRepo->getById($id);
        // Delete social accounts
        if($this->userRepo->isOnlyAdmin($user)) {
            session()->flash('error', 'You cannot delete the only admin');
            return redirect($user->getEditUrl());
        }
        $user->socialAccounts()->delete();
        $user->delete();
        return redirect('/users');
    }
}
