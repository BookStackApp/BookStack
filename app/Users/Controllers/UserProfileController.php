<?php

namespace BookStack\Users\Controllers;

use BookStack\Activity\ActivityQueries;
use BookStack\Http\Controller;
use BookStack\Users\Queries\UserContentCounts;
use BookStack\Users\Queries\UserRecentlyCreatedContent;
use BookStack\Users\UserRepo;

class UserProfileController extends Controller
{
    public function __construct(
        protected UserRepo $userRepo,
        protected ActivityQueries $activityQueries,
        protected UserContentCounts $contentCounts,
        protected UserRecentlyCreatedContent $recentlyCreatedContent
    ) {
    }


    /**
     * Show the user profile page.
     */
    public function show(string $slug)
    {
        $user = $this->userRepo->getBySlug($slug);

        $userActivity = $this->activityQueries->userActivity($user);
        $recentlyCreated = $this->recentlyCreatedContent->run($user, 5);
        $assetCounts = $this->contentCounts->run($user);

        $this->setPageTitle($user->name);

        return view('users.profile', [
            'user'            => $user,
            'activity'        => $userActivity,
            'recentlyCreated' => $recentlyCreated,
            'assetCounts'     => $assetCounts,
        ]);
    }
}
