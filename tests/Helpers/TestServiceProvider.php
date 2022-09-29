<?php

namespace Tests\Helpers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\ServiceProvider;

class TestServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Tell Laravel's parallel testing functionality to seed the test
        // databases with the DummyContentSeeder upon creation.
        // This is only done for initial database creation. Seeding
        // won't occur on every run.
        ParallelTesting::setUpTestDatabase(function ($database, $token) {
            Artisan::call('db:seed --class=DummyContentSeeder');
        });
    }
}
