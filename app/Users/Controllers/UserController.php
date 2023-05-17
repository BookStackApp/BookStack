<?php

namespace BookStack\Users\Controllers;

use BookStack\Access\SocialAuthService;
use BookStack\Exceptions\ImageUploadException;
use BookStack\Exceptions\UserUpdateException;
use BookStack\Http\Controllers\Controller;
use BookStack\Uploads\ImageRepo;
use BookStack\Users\Models\Role;
use BookStack\Users\Queries\UsersAllPaginatedAndSorted;
use BookStack\Users\UserRepo;
use BookStack\Util\SimpleListOptions;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    protected UserRepo $userRepo;
    protected ImageRepo $imageRepo;

    public function __construct(UserRepo $userRepo, ImageRepo $imageRepo)
    {
        $this->userRepo = $userRepo;
        $this->imageRepo = $imageRepo;
    }

    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $this->checkPermission('users-manage');

        $listOptions = SimpleListOptions::fromRequest($request, 'users')->withSortOptions([
            'name' => trans('common.sort_name'),
            'email' => trans('auth.email'),
            'created_at' => trans('common.sort_created_at'),
            'updated_at' => trans('common.sort_updated_at'),
            'last_activity_at' => trans('settings.users_latest_activity'),
        ]);

        $users = (new UsersAllPaginatedAndSorted())->run(20, $listOptions);

        $this->setPageTitle(trans('settings.users'));
        $users->appends($listOptions->getPaginationAppends());

        return view('users.index', [
            'users'       => $users,
            'listOptions' => $listOptions,
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $this->checkPermission('users-manage');
        $authMethod = config('auth.method');
        $roles = Role::query()->orderBy('display_name', 'asc')->get();
        $this->setPageTitle(trans('settings.users_add_new'));

        return view('users.create', ['authMethod' => $authMethod, 'roles' => $roles]);
    }

    /**
     * Store a new user in storage.
     *
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $this->checkPermission('users-manage');

        $authMethod = config('auth.method');
        $sendInvite = ($request->get('send_invite', 'false') === 'true');
        $externalAuth = $authMethod === 'ldap' || $authMethod === 'saml2' || $authMethod === 'oidc';
        $passwordRequired = ($authMethod === 'standard' && !$sendInvite);

        $validationRules = [
            'name'             => ['required', 'max:100'],
            'email'            => ['required', 'email', 'unique:users,email'],
            'language'         => ['string', 'max:15', 'alpha_dash'],
            'roles'            => ['array'],
            'roles.*'          => ['integer'],
            'password'         => $passwordRequired ? ['required', Password::default()] : null,
            'password-confirm' => $passwordRequired ? ['required', 'same:password'] : null,
            'external_auth_id' => $externalAuth ? ['required'] : null,
        ];

        $validated = $this->validate($request, array_filter($validationRules));

        DB::transaction(function () use ($validated, $sendInvite) {
            $this->userRepo->create($validated, $sendInvite);
        });

        return redirect('/settings/users');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(int $id, SocialAuthService $socialAuthService)
    {
        $this->checkPermissionOrCurrentUser('users-manage', $id);

        $user = $this->userRepo->getById($id);
        $user->load(['apiTokens', 'mfaValues']);
        $authMethod = ($user->system_name) ? 'system' : config('auth.method');

        $activeSocialDrivers = $socialAuthService->getActiveDrivers();
        $mfaMethods = $user->mfaValues->groupBy('method');
        $this->setPageTitle(trans('settings.user_profile'));
        $roles = Role::query()->orderBy('display_name', 'asc')->get();

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

        $validated = $this->validate($request, [
            'name'             => ['min:2', 'max:100'],
            'email'            => ['min:2', 'email', 'unique:users,email,' . $id],
            'password'         => ['required_with:password_confirm', Password::default()],
            'password-confirm' => ['same:password', 'required_with:password'],
            'language'         => ['string', 'max:15', 'alpha_dash'],
            'roles'            => ['array'],
            'roles.*'          => ['integer'],
            'external_auth_id' => ['string'],
            'profile_image'    => array_merge(['nullable'], $this->getImageValidationRules()),
        ]);

        $user = $this->userRepo->getById($id);
        $this->userRepo->update($user, $validated, userCan('users-manage'));

        // Save profile image if in request
        if ($request->hasFile('profile_image')) {
            $imageUpload = $request->file('profile_image');
            $this->imageRepo->destroyImage($user->avatar);
            $image = $this->imageRepo->saveNew($imageUpload, 'user', $user->id);
            $user->image_id = $image->id;
            $user->save();
        }

        // Delete the profile image if reset option is in request
        if ($request->has('profile_image_reset')) {
            $this->imageRepo->destroyImage($user->avatar);
            $user->image_id = 0;
            $user->save();
        }

        $redirectUrl = userCan('users-manage') ? '/settings/users' : "/settings/users/{$user->id}";

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
        $newOwnerId = intval($request->get('new_owner_id')) ?: null;

        $this->userRepo->destroy($user, $newOwnerId);

        return redirect('/settings/users');
    }
}
