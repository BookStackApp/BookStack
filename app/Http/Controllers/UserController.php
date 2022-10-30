<?php

namespace BookStack\Http\Controllers;

use BookStack\Auth\Access\SocialAuthService;
use BookStack\Auth\Queries\UsersAllPaginatedAndSorted;
use BookStack\Auth\Role;
use BookStack\Auth\User;
use BookStack\Auth\UserRepo;
use BookStack\Exceptions\ImageUploadException;
use BookStack\Exceptions\UserUpdateException;
use BookStack\Uploads\ImageRepo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    protected UserRepo $userRepo;
    protected ImageRepo $imageRepo;

    /**
     * UserController constructor.
     */
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
        $listDetails = [
            'search' => $request->get('search', ''),
            'sort'   => setting()->getForCurrentUser('users_sort', 'name'),
            'order'  => setting()->getForCurrentUser('users_sort_order', 'asc'),
        ];

        $users = (new UsersAllPaginatedAndSorted())->run(20, $listDetails);

        $this->setPageTitle(trans('settings.users'));
        $users->appends(['search' => $listDetails['search']]);

        return view('users.index', [
            'users'       => $users,
            'listDetails' => $listDetails,
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

        /** @var User $user */
        $user = User::query()->with(['apiTokens', 'mfaValues'])->findOrFail($id);

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
        $newOwnerId = $request->get('new_owner_id', null);

        $this->userRepo->destroy($user, $newOwnerId);

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
        $validSortTypes = ['books', 'bookshelves', 'shelf_books', 'users', 'roles', 'webhooks'];
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

        $user = $this->userRepo->getById($id);
        setting()->putUser($user, 'section_expansion#' . $key, $newState);

        return response('', 204);
    }

    public function updateCodeLanguageFavourite(Request $request)
    {
        $validated = $this->validate($request, [
            'language' => ['required', 'string', 'max:20'],
            'active'   => ['required', 'bool'],
        ]);

        $currentFavoritesStr = setting()->getForCurrentUser('code-language-favourites', '');
        $currentFavorites = array_filter(explode(',', $currentFavoritesStr));

        $isFav = in_array($validated['language'], $currentFavorites);
        if (!$isFav && $validated['active']) {
            $currentFavorites[] = $validated['language'];
        } elseif ($isFav && !$validated['active']) {
            $index = array_search($validated['language'], $currentFavorites);
            array_splice($currentFavorites, $index, 1);
        }

        setting()->putUser(user(), 'code-language-favourites', implode(',', $currentFavorites));
    }

    /**
     * Changed the stored preference for a list sort order.
     */
    protected function changeListSort(int $userId, Request $request, string $listName)
    {
        $this->checkPermissionOrCurrentUser('users-manage', $userId);

        $sort = $request->get('sort');
        // TODO - Need to find a better way to validate sort options
        //   Probably better to do a simple validation here then validate at usage.
        $validSorts = [
            'name', 'created_at', 'updated_at', 'default', 'email', 'last_activity_at', 'display_name',
            'users_count', 'permissions_count', 'endpoint', 'active',
        ];
        if (!in_array($sort, $validSorts)) {
            $sort = 'name';
        }

        $order = $request->get('order');
        if (!in_array($order, ['asc', 'desc'])) {
            $order = 'asc';
        }

        $user = $this->userRepo->getById($userId);
        $sortKey = $listName . '_sort';
        $orderKey = $listName . '_sort_order';
        setting()->putUser($user, $sortKey, $sort);
        setting()->putUser($user, $orderKey, $order);

        return redirect()->back(302, [], "/settings/users/$userId");
    }
}
