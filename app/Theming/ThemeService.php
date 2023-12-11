<?php

namespace BookStack\Theming;

use BookStack\Access\SocialDriverManager;
use BookStack\Exceptions\ThemeException;
use Illuminate\Console\Application;
use Illuminate\Console\Application as Artisan;
use Symfony\Component\Console\Command\Command;

class ThemeService
{
    /**
     * @var array<string, callable[]>
     */
    protected array $listeners = [];

    /**
     * Listen to a given custom theme event,
     * setting up the action to be ran when the event occurs.
     */
    public function listen(string $event, callable $action): void
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
     */
    public function dispatch(string $event, ...$args): mixed
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
     * Check if there are listeners registered for the given event name.
     */
    public function hasListeners(string $event): bool
    {
        return count($this->listeners[$event] ?? []) > 0;
    }

    /**
     * Register a new custom artisan command to be available.
     */
    public function registerCommand(Command $command): void
    {
        Artisan::starting(function (Application $application) use ($command) {
            $application->addCommands([$command]);
        });
    }

    /**
     * Read any actions from the set theme path if the 'functions.php' file exists.
     */
    public function readThemeActions(): void
    {
        $themeActionsFile = theme_path('functions.php');
        if ($themeActionsFile && file_exists($themeActionsFile)) {
            try {
                require $themeActionsFile;
            } catch (\Error $exception) {
                throw new ThemeException("Failed loading theme functions file at \"{$themeActionsFile}\" with error: {$exception->getMessage()}");
            }
        }
    }

    /**
     * @see SocialDriverManager::addSocialDriver
     */
    public function addSocialDriver(string $driverName, array $config, string $socialiteHandler, callable $configureForRedirect = null): void
    {
        $driverManager = app()->make(SocialDriverManager::class);
        $driverManager->addSocialDriver($driverName, $config, $socialiteHandler, $configureForRedirect);
    }
}
