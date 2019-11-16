<?php

namespace BookStack\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;
use Aacotroneo\Saml2\Events\Saml2LoginEvent;

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
            'SocialiteProviders\Discord\DiscordExtendSocialite@handle',
        ],
        Saml2LoginEvent::class => [
            'BookStack\Listeners\Saml2LoginEventListener@handle',
        ]
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
