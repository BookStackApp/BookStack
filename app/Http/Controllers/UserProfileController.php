<?php

namespace BookStack\Http\Controllers;

use BookStack\Actions\ActivityQueries;
use BookStack\Auth\Queries\UserContentCounts;
use BookStack\Auth\Queries\UserRecentlyCreatedContent;
use BookStack\Auth\UserRepo;

class UserProfileController extends Controller
{
    /**
     * Show the user profile page.
     */
    public function show(UserRepo $repo, ActivityQueries $activities, string $slug)
    {
        $user = $repo->getBySlug($slug);

        $userActivity = $activities->userActivity($user);
        $recentlyCreated = (new UserRecentlyCreatedContent())->run($user, 5);
        $assetCounts = (new UserContentCounts())->run($user);

        $this->setPageTitle($user->name);

        return view('users.profile', [
            'user'            => $user,
            'activity'        => $userActivity,
            'recentlyCreated' => $recentlyCreated,
            'assetCounts'     => $assetCounts,
        ]);
    }
}
