<?php

namespace BookStack\Providers;

use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
//        Broadcast::routes();
//
//        /*
//         * Authenticate the user's personal channel...
//         */
//        Broadcast::channel('BookStack.User.*', function ($user, $userId) {
//            return (int) $user->id === (int) $userId;
//        });
    }
}
