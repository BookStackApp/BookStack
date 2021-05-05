<?php

namespace BookStack\Http\Controllers\Api;

use BookStack\Exceptions\PermissionsException;
use BookStack\Auth\User;
use BookStack\Auth\UserRepo;
use Exception;
use Illuminate\Http\Request;

class UserApiController extends ApiController
{
    protected $user;
    protected $userRepo;

# TBD: Endpoints to create / update users
#     protected $rules = [
#         'create' => [
#         ],
#         'update' => [
#         ],
#     ];

    public function __construct(User $user, UserRepo $userRepo)
    {
        $this->user = $user;
        $this->userRepo = $userRepo;
    }

    /**
     * Get a listing of pages visible to the user.
     */
    public function list()
    {
        $users = $this->userRepo->getUsersBuilder();

        return $this->apiListingResponse($users, [
            'id', 'name', 'slug',
            'email', 'created_at', 'updated_at',
        ]);
    }
}
