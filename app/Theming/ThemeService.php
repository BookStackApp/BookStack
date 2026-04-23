<?php

namespace BookStack\Theming;

use BookStack\Access\SocialDriverManager;
use BookStack\Exceptions\ThemeException;
use Illuminate\Console\Application;
use Illuminate\Console\Application as Artisan;
use Illuminate\View\FileViewFinder;
use Symfony\Component\Console\Command\Command;

class ThemeService
{
    /**
     * @var array<string, callable[]>
     */
    protected array $listeners = [];

    /**
     * @var array<string, ThemeModule>
     */
    protected array $modules = [];

    /**
     * Get the currently configured theme.
     * Returns an empty string if not configured.
     */
    public function getTheme(): string
    {
        return config('view.theme') ?? '';
    }

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
     * Read any actions from the 'functions.php' file of the active theme or its modules.
     */
    public function readThemeActions(): void
    {
        $moduleFunctionFiles = array_map(function (ThemeModule $module): string {
            return $module->path('functions.php');
        }, $this->modules);
        $allFunctionFiles = array_merge(array_values($moduleFunctionFiles), [theme_path('functions.php')]);
        $filteredFunctionFiles = array_filter($allFunctionFiles, function (string $file): bool {
            return $file && file_exists($file);
        });

        foreach ($filteredFunctionFiles as $functionFile) {
            try {
                require $functionFile;
            } catch (\Error $exception) {
                throw new ThemeException("Failed loading theme functions file at \"{$functionFile}\" with error: {$exception->getMessage()}");
            }
        }
    }

    /**
     * Read the modules folder and load in any valid theme modules.
     * @throws ThemeModuleException
     */
    public function loadModules(): void
    {
        $modulesFolder = theme_path('modules');
        if (!$modulesFolder) {
            return;
        }

        $this->modules = (new ThemeModuleManager($modulesFolder))->load();
    }

    /**
     * Get all loaded theme modules.
     * @return array<string, ThemeModule>
     */
    public function getModules(): array
    {
        return $this->modules;
    }

    /**
     * Get a hash to represent the currently loaded modules.
     */
    public function getModulesHash(): string
    {
        $key = "";

        foreach ($this->modules as $module) {
            $key .= $module->name . ':' . $module->version . ';';
        }

        return md5($key);
    }

    /**
     * Look for a specific file within the theme or its modules.
     * Returns the first file found or null if not found.
     */
    public function findFirstFile(string $path): ?string
    {
        $themePath = theme_path($path);
        if (file_exists($themePath)) {
            return $themePath;
        }

        foreach ($this->modules as $module) {
            $customizedFile = $module->path($path);
            if (file_exists($customizedFile)) {
                return $customizedFile;
            }
        }

        return null;
    }

    /**
     * @see SocialDriverManager::addSocialDriver
     */
    public function addSocialDriver(string $driverName, array $config, string $socialiteHandler, ?callable $configureForRedirect = null): void
    {
        $driverManager = app()->make(SocialDriverManager::class);
        $driverManager->addSocialDriver($driverName, $config, $socialiteHandler, $configureForRedirect);
    }
}
