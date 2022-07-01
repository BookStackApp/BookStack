<?php

namespace BookStack\Http\Controllers\Api;

use BookStack\Actions\View;
use BookStack\Auth\UserRepo;
use BookStack\Entities\Repos\PageRepo;

class ProfileApiController extends ApiController
{
    protected $userRepo;
    protected $pageRepo;

    protected $fieldsToExpose = [
        'email', 'created_at', 'updated_at', 'last_activity_at', 'external_auth_id', 'roles'
    ];

    public function __construct(UserRepo $userRepo, PageRepo $pageRepo)
    {
        $this->userRepo = $userRepo;
        $this->pageRepo = $pageRepo;
    }

    /**
     * View the details of a single page.
     *
     * Pages will always have HTML content. They may have markdown content
     * if the markdown editor was used to last update the page.
     */
    public function addPageView(string $id)
    {
        $page = $this->pageRepo->getById($id, []);

        View::incrementFor($page);
    }

    /**
     * Get profile information of the current user
     */
    public function profile()
    {
        $user = \user();
        $permissions = $user->can('page-update-all') ? ['page-update-all'] : [];
        return [
            'id' => $user->id,
            'name' => $user->name,
            'slug' => $user->slug,
            'roles' => array_map(function ($array_item) {
                return $array_item->system_name;
            }, $user->roles->all()),
            'permissions' => $permissions,
        ];
    }
}
