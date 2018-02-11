<?php

namespace BookStack\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        SocialiteWasCalled::class => [
            'SocialiteProviders\Slack\SlackExtendSocialite@handle',
            'SocialiteProviders\Azure\AzureExtendSocialite@handle',
            'SocialiteProviders\Okta\OktaExtendSocialite@handle',
            'SocialiteProviders\GitLab\GitLabExtendSocialite@handle',
            'SocialiteProviders\Twitch\TwitchExtendSocialite@handle',
        ],
    ];

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
