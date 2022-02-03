<?php

namespace BookStack\Http\Controllers\Api;

use BookStack\Auth\User;
use BookStack\Auth\UserRepo;
use Closure;
use Illuminate\Http\Request;

class UserApiController extends ApiController
{
    protected $userRepo;

    protected $fieldsToExpose = [
        'email', 'created_at', 'updated_at', 'last_activity_at', 'external_auth_id'
    ];

    protected $rules = [
        'create' => [
        ],
        'update' => [
        ],
        'delete' => [
            'migrate_ownership_id' => ['integer', 'exists:users,id'],
        ],
    ];

    public function __construct(UserRepo $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * Get a listing of users in the system.
     * Requires permission to manage users.
     */
    public function list()
    {
        $this->checkPermission('users-manage');

        $users = $this->userRepo->getApiUsersBuilder();

        return $this->apiListingResponse($users, [
            'id', 'name', 'slug', 'email', 'external_auth_id',
            'created_at', 'updated_at', 'last_activity_at',
        ], [Closure::fromCallable([$this, 'listFormatter'])]);
    }

    /**
     * View the details of a single user.
     * Requires permission to manage users.
     */
    public function read(string $id)
    {
        $this->checkPermission('users-manage');

        $singleUser = $this->userRepo->getById($id);
        $this->singleFormatter($singleUser);

        return response()->json($singleUser);
    }

    /**
     * Delete a user from the system.
     * Can optionally accept a user id via `migrate_ownership_id` to indicate
     * who should be the new owner of their related content.
     * Requires permission to manage users.
     */
    public function delete(Request $request, string $id)
    {
        $this->checkPermission('users-manage');

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
