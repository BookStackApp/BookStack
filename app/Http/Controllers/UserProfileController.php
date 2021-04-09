<?php namespace BookStack\Http\Controllers;

use BookStack\Auth\UserRepo;

class UserProfileController extends Controller
{
    /**
     * Show the user profile page
     */
    public function show(UserRepo $repo, string $slug)
    {
        $user = $repo->getBySlug($slug);

        $userActivity = $repo->getActivity($user);
        $recentlyCreated = $repo->getRecentlyCreated($user, 5);
        $assetCounts = $repo->getAssetCounts($user);

        return view('users.profile', [
            'user' => $user,
            'activity' => $userActivity,
            'recentlyCreated' => $recentlyCreated,
            'assetCounts' => $assetCounts
        ]);
    }
}
