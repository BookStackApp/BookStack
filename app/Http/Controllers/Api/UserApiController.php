<?php

namespace BookStack\Http\Controllers\Api;

use BookStack\Auth\User;
use BookStack\Auth\UserRepo;
use BookStack\Exceptions\UserUpdateException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rules\Unique;

class UserApiController extends ApiController
{
    protected $userRepo;

    protected $fieldsToExpose = [
        'email', 'created_at', 'updated_at', 'last_activity_at', 'external_auth_id',
    ];

    public function __construct(UserRepo $userRepo)
    {
        $this->userRepo = $userRepo;

        // Checks for all endpoints in this controller
        $this->middleware(function ($request, $next) {
            $this->checkPermission('users-manage');
            $this->preventAccessInDemoMode();

            return $next($request);
        });
    }

    protected function rules(int $userId = null): array
    {
        return [
            'create' => [
                'name'  => ['required', 'min:2'],
                'email' => [
                    'required', 'min:2', 'email', new Unique('users', 'email'),
                ],
                'external_auth_id' => ['string'],
                'language'         => ['string'],
                'password'         => [Password::default()],
                'roles'            => ['array'],
                'roles.*'          => ['integer'],
                'send_invite'      => ['boolean'],
            ],
            'update' => [
                'name'  => ['min:2'],
                'email' => [
                    'min:2',
                    'email',
                    (new Unique('users', 'email'))->ignore($userId ?? null),
                ],
                'external_auth_id' => ['string'],
                'language'         => ['string'],
                'password'         => [Password::default()],
                'roles'            => ['array'],
                'roles.*'          => ['integer'],
            ],
            'delete' => [
                'migrate_ownership_id' => ['integer', 'exists:users,id'],
            ],
        ];
    }

    /**
     * Get a listing of users in the system.
     * Requires permission to manage users.
     */
    public function list()
    {
        $users = User::query()->select(['*'])
            ->scopes('withLastActivityAt')
            ->with(['avatar']);

        return $this->apiListingResponse($users, [
            'id', 'name', 'slug', 'email', 'external_auth_id',
            'created_at', 'updated_at', 'last_activity_at',
        ], [Closure::fromCallable([$this, 'listFormatter'])]);
    }

    /**
     * Create a new user in the system.
     * Requires permission to manage users.
     */
    public function create(Request $request)
    {
        $data = $this->validate($request, $this->rules()['create']);
        $sendInvite = ($data['send_invite'] ?? false) === true;

        $user = null;
        DB::transaction(function () use ($data, $sendInvite, &$user) {
            $user = $this->userRepo->create($data, $sendInvite);
        });

        $this->singleFormatter($user);

        return response()->json($user);
    }

    /**
     * View the details of a single user.
     * Requires permission to manage users.
     */
    public function read(string $id)
    {
        $user = $this->userRepo->getById($id);
        $this->singleFormatter($user);

        return response()->json($user);
    }

    /**
     * Update an existing user in the system.
     * Requires permission to manage users.
     *
     * @throws UserUpdateException
     */
    public function update(Request $request, string $id)
    {
        $data = $this->validate($request, $this->rules($id)['update']);
        $user = $this->userRepo->getById($id);
        $this->userRepo->update($user, $data, userCan('users-manage'));
        $this->singleFormatter($user);

        return response()->json($user);
    }

    /**
     * Delete a user from the system.
     * Can optionally accept a user id via `migrate_ownership_id` to indicate
     * who should be the new owner of their related content.
     * Requires permission to manage users.
     */
    public function delete(Request $request, string $id)
    {
        $user = $this->userRepo->getById($id);
        $newOwnerId = $request->get('migrate_ownership_id', null);

        $this->userRepo->destroy($user, $newOwnerId);

        return response('', 204);
    }

    /**
     * Format the given user model for single-result display.
     */
    protected function singleFormatter(User $user)
    {
        $this->listFormatter($user);
        $user->load('roles:id,display_name');
        $user->makeVisible(['roles']);
    }

    /**
     * Format the given user model for a listing multi-result display.
     */
    protected function listFormatter(User $user)
    {
        $user->makeVisible($this->fieldsToExpose);
        $user->setAttribute('profile_url', $user->getProfileUrl());
        $user->setAttribute('edit_url', $user->getEditUrl());
        $user->setAttribute('avatar_url', $user->getAvatar());
    }
}
