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

    protected $printHidden = [
        'email', 'created_at', 'updated_at', 'last_activity_at'
    ];

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
     * Get a listing of users
     */
    public function list()
    {
        $this->checkPermission('users-manage');

        $users = $this->userRepo->getUsersBuilder();

        return $this->apiListingResponse($users, [
            'id', 'name', 'slug', 'email',
            'created_at', 'updated_at', 'last_activity_at',
        ], $this->printHidden);
    }

    /**
     * View the details of a single user
     */
    public function read(string $id)
    {
        $this->checkPermission('users-manage');

        $singleUser = $this->userRepo->getById($id);
        $singleUser = $singleUser->makeVisible($this->printHidden);

        return response()->json($singleUser);
    }
}
