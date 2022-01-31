<?php

namespace BookStack\Http\Controllers;

use BookStack\Actions\ActivityType;
use BookStack\Auth\Access\SocialAuthService;
use BookStack\Auth\Access\UserInviteService;
use BookStack\Auth\User;
use BookStack\Auth\UserRepo;
use BookStack\Exceptions\ImageUploadException;
use BookStack\Exceptions\UserUpdateException;
use BookStack\Uploads\ImageRepo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    protected $user;
    protected $userRepo;
    protected $inviteService;
    protected $imageRepo;

    /**
     * UserController constructor.
     */
    public function __construct(User $user, UserRepo $userRepo, UserInviteService $inviteService, ImageRepo $imageRepo)
    {
        $this->user = $user;
        $this->userRepo = $userRepo;
        $this->inviteService = $inviteService;
        $this->imageRepo = $imageRepo;
    }

    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $this->checkPermission('users-manage');
        $listDetails = [
            'order'  => $request->get('order', 'asc'),
            'search' => $request->get('search', ''),
            'sort'   => $request->get('sort', 'name'),
        ];
        $users = $this->userRepo->getAllUsersPaginatedAndSorted(20, $listDetails);

        $this->setPageTitle(trans('settings.users'));
        $users->appends($listDetails);

        return view('users.index', ['users' => $users, 'listDetails' => $listDetails]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $this->checkPermission('users-manage');
        $authMethod = config('auth.method');
        $roles = $this->userRepo->getAllRoles();
        $this->setPageTitle(trans('settings.users_add_new'));

        return view('users.create', ['authMethod' => $authMethod, 'roles' => $roles]);
    }

    /**
     * Store a newly created user in storage.
     *
     * @throws UserUpdateException
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $this->checkPermission('users-manage');
        $validationRules = [
            'name'  => ['required'],
            'email' => ['required', 'email', 'unique:users,email'],
            'setting' => ['array'],
        ];

        $authMethod = config('auth.method');
        $sendInvite = ($request->get('send_invite', 'false') === 'true');

        if ($authMethod === 'standard' && !$sendInvite) {
            $validationRules['password'] = ['required', Password::default()];
            $validationRules['password-confirm'] = ['required', 'same:password'];
        } elseif ($authMethod === 'ldap' || $authMethod === 'saml2' || $authMethod === 'openid') {
            $validationRules['external_auth_id'] = ['required'];
        }
        $this->validate($request, $validationRules);

        $user = $this->user->fill($request->all());

        if ($authMethod === 'standard') {
            $user->password = bcrypt($request->get('password', Str::random(32)));
        } elseif ($authMethod === 'ldap' || $authMethod === 'saml2' || $authMethod === 'openid') {
            $user->external_auth_id = $request->get('external_auth_id');
        }

        $user->refreshSlug();

        DB::transaction(function () use ($user, $sendInvite, $request) {
            $user->save();

            // Save user-specific settings
            if ($request->filled('setting')) {
                foreach ($request->get('setting') as $key => $value) {
                    setting()->putUser($user, $key, $value);
                }
            }

            if ($sendInvite) {
                $this->inviteService->sendInvitation($user);
            }

            if ($request->filled('roles')) {
                $roles = $request->get('roles');
                $this->userRepo->setUserRoles($user, $roles);
            }

            $this->userRepo->downloadAndAssignUserAvatar($user);

            $this->logActivity(ActivityType::USER_CREATE, $user);
        });

        return redirect('/settings/users');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(int $id, SocialAuthService $socialAuthService)
    {
        $this->checkPermissionOrCurrentUser('users-manage', $id);

        /** @var User $user */
        $user = $this->user->newQuery()->with(['apiTokens', 'mfaValues'])->findOrFail($id);

        $authMethod = ($user->system_name) ? 'system' : config('auth.method');

        $activeSocialDrivers = $socialAuthService->getActiveDrivers();
        $mfaMethods = $user->mfaValues->groupBy('method');
        $this->setPageTitle(trans('settings.user_profile'));
        $roles = $this->userRepo->getAllRoles();

        return view('users.edit', [
            'user'                => $user,
            'activeSocialDrivers' => $activeSocialDrivers,
            'mfaMethods'          => $mfaMethods,
            'authMethod'          => $authMethod,
            'roles'               => $roles,
        ]);
    }

    /**
     * Update the specified user in storage.
     *
     * @throws UserUpdateException
     * @throws ImageUploadException
     * @throws ValidationException
     */
    public function update(Request $request, int $id)
    {
        $this->preventAccessInDemoMode();
        $this->checkPermissionOrCurrentUser('users-manage', $id);

        $this->validate($request, [
            'name'             => ['min:2'],
            'email'            => ['min:2', 'email', 'unique:users,email,' . $id],
            'password'         => ['required_with:password_confirm', Password::default()],
            'password-confirm' => ['same:password', 'required_with:password'],
            'setting'          => ['array'],
            'profile_image'    => array_merge(['nullable'], $this->getImageValidationRules()),
        ]);

        $user = $this->userRepo->getById($id);
        $user->fill($request->except(['email']));

        // Email updates
        if (userCan('users-manage') && $request->filled('email')) {
            $user->email = $request->get('email');
        }

        // Refresh the slug if the user's name has changed
        if ($user->isDirty('name')) {
            $user->refreshSlug();
        }

        // Role updates
        if (userCan('users-manage') && $request->filled('roles')) {
            $roles = $request->get('roles');
            $this->userRepo->setUserRoles($user, $roles);
        }

        // Password updates
        if ($request->filled('password')) {
            $password = $request->get('password');
            $user->password = bcrypt($password);
        }

        // External auth id updates
        if (user()->can('users-manage') && $request->filled('external_auth_id')) {
            $user->external_auth_id = $request->get('external_auth_id');
        }

        // Save user-specific settings
        if ($request->filled('setting')) {
            foreach ($request->get('setting') as $key => $value) {
                setting()->putUser($user, $key, $value);
            }
        }

        // Save profile image if in request
        if ($request->hasFile('profile_image')) {
            $imageUpload = $request->file('profile_image');
            $this->imageRepo->destroyImage($user->avatar);
            $image = $this->imageRepo->saveNew($imageUpload, 'user', $user->id);
            $user->image_id = $image->id;
        }

        // Delete the profile image if reset option is in request
        if ($request->has('profile_image_reset')) {
            $this->imageRepo->destroyImage($user->avatar);
        }

        $user->save();
        $this->showSuccessNotification(trans('settings.users_edit_success'));
        $this->logActivity(ActivityType::USER_UPDATE, $user);

        $redirectUrl = userCan('users-manage') ? '/settings/users' : ('/settings/users/' . $user->id);

        return redirect($redirectUrl);
    }

    /**
     * Show the user delete page.
     */
    public function delete(int $id)
    {
        $this->checkPermissionOrCurrentUser('users-manage', $id);

        $user = $this->userRepo->getById($id);
        $this->setPageTitle(trans('settings.users_delete_named', ['userName' => $user->name]));

        return view('users.delete', ['user' => $user]);
    }

    /**
     * Remove the specified user from storage.
     *
     * @throws Exception
     */
    public function destroy(Request $request, int $id)
    {
        $this->preventAccessInDemoMode();
        $this->checkPermissionOrCurrentUser('users-manage', $id);

        $user = $this->userRepo->getById($id);
        $newOwnerId = $request->get('new_owner_id', null);

        if ($this->userRepo->isOnlyAdmin($user)) {
            $this->showErrorNotification(trans('errors.users_cannot_delete_only_admin'));

            return redirect($user->getEditUrl());
        }

        if ($user->system_name === 'public') {
            $this->showErrorNotification(trans('errors.users_cannot_delete_guest'));

            return redirect($user->getEditUrl());
        }

        $this->userRepo->destroy($user, $newOwnerId);
        $this->showSuccessNotification(trans('settings.users_delete_success'));
        $this->logActivity(ActivityType::USER_DELETE, $user);

        return redirect('/settings/users');
    }

    /**
     * Update the user's preferred book-list display setting.
     */
    public function switchBooksView(Request $request, int $id)
    {
        return $this->switchViewType($id, $request, 'books');
    }

    /**
     * Update the user's preferred shelf-list display setting.
     */
    public function switchShelvesView(Request $request, int $id)
    {
        return $this->switchViewType($id, $request, 'bookshelves');
    }

    /**
     * Update the user's preferred shelf-view book list display setting.
     */
    public function switchShelfView(Request $request, int $id)
    {
        return $this->switchViewType($id, $request, 'bookshelf');
    }

    /**
     * For a type of list, switch with stored view type for a user.
     */
    protected function switchViewType(int $userId, Request $request, string $listName)
    {
        $this->checkPermissionOrCurrentUser('users-manage', $userId);

        $viewType = $request->get('view_type');
        if (!in_array($viewType, ['grid', 'list'])) {
            $viewType = 'list';
        }

        $user = $this->userRepo->getById($userId);
        $key = $listName . '_view_type';
        setting()->putUser($user, $key, $viewType);

        return redirect()->back(302, [], "/settings/users/$userId");
    }

    /**
     * Change the stored sort type for a particular view.
     */
    public function changeSort(Request $request, string $id, string $type)
    {
        $validSortTypes = ['books', 'bookshelves', 'shelf_books'];
        if (!in_array($type, $validSortTypes)) {
            return redirect()->back(500);
        }

        return $this->changeListSort($id, $request, $type);
    }

    /**
     * Toggle dark mode for the current user.
     */
    public function toggleDarkMode()
    {
        $enabled = setting()->getForCurrentUser('dark-mode-enabled', false);
        setting()->putUser(user(), 'dark-mode-enabled', $enabled ? 'false' : 'true');

        return redirect()->back();
    }

    /**
     * Update the stored section expansion preference for the given user.
     */
    public function updateExpansionPreference(Request $request, string $id, string $key)
    {
        $this->checkPermissionOrCurrentUser('users-manage', $id);
        $keyWhitelist = ['home-details'];
        if (!in_array($key, $keyWhitelist)) {
            return response('Invalid key', 500);
        }

        $newState = $request->get('expand', 'false');

        $user = $this->user->findOrFail($id);
        setting()->putUser($user, 'section_expansion#' . $key, $newState);

        return response('', 204);
    }

    /**
     * Changed the stored preference for a list sort order.
     */
    protected function changeListSort(int $userId, Request $request, string $listName)
    {
        $this->checkPermissionOrCurrentUser('users-manage', $userId);

        $sort = $request->get('sort');
        if (!in_array($sort, ['name', 'created_at', 'updated_at', 'default'])) {
            $sort = 'name';
        }

        $order = $request->get('order');
        if (!in_array($order, ['asc', 'desc'])) {
            $order = 'asc';
        }

        $user = $this->user->findOrFail($userId);
        $sortKey = $listName . '_sort';
        $orderKey = $listName . '_sort_order';
        setting()->putUser($user, $sortKey, $sort);
        setting()->putUser($user, $orderKey, $order);

        return redirect()->back(302, [], "/settings/users/$userId");
    }
}
