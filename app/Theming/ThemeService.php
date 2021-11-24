<?php

namespace BookStack\Theming;

use BookStack\Auth\Access\SocialAuthService;
use Illuminate\Console\Application;
use Illuminate\Console\Application as Artisan;
use Symfony\Component\Console\Command\Command;

class ThemeService
{
    protected $listeners = [];

    /**
     * Listen to a given custom theme event,
     * setting up the action to be ran when the event occurs.
     */
    public function listen(string $event, callable $action)
    {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }

        $this->listeners[$event][] = $action;
    }

    /**
     * Dispatch the given event name.
     * Runs any registered listeners for that event name,
     * passing all additional variables to the listener action.
     *
     * If a callback returns a non-null value, this method will
     * stop and return that value itself.
     *
     * @return mixed
     */
    public function dispatch(string $event, ...$args)
    {
        foreach ($this->listeners[$event] ?? [] as $action) {
            $result = call_user_func_array($action, $args);
            if (!is_null($result)) {
                return $result;
            }
        }

        return null;
    }

    /**
     * Register a new custom artisan command to be available.
     */
    public function registerCommand(Command $command)
    {
        Artisan::starting(function (Application $application) use ($command) {
            $application->addCommands([$command]);
        });
    }

    /**
     * Read any actions from the set theme path if the 'functions.php' file exists.
     */
    public function readThemeActions()
    {
        $themeActionsFile = theme_path('functions.php');
        if ($themeActionsFile && file_exists($themeActionsFile)) {
            require $themeActionsFile;
        }
    }

    /**
     * @see SocialAuthService::addSocialDriver
     */
    public function addSocialDriver(string $driverName, array $config, string $socialiteHandler, callable $configureForRedirect = null)
    {
        $socialAuthService = app()->make(SocialAuthService::class);
        $socialAuthService->addSocialDriver($driverName, $config, $socialiteHandler, $configureForRedirect);
    }
}
