<?php namespace BookStack\Theming;

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
     * Read any actions from the set theme path if the 'functions.php' file exists.
     */
    public function readThemeActions()
    {
        $themeActionsFile = theme_path('functions.php');
        if (file_exists($themeActionsFile)) {
            require $themeActionsFile;
        }
    }
}