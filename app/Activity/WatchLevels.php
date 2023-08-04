<?php

namespace BookStack\Activity;

class WatchLevels
{
    /**
     * Default level, No specific option set
     * Typically not a stored status
     */
    const DEFAULT = -1;

    /**
     * Ignore all notifications.
     */
    const IGNORE = 0;

    /**
     * Watch for new content.
     */
    const NEW = 1;

    /**
     * Watch for updates and new content
     */
    const UPDATES = 2;

    /**
     * Watch for comments, updates and new content.
     */
    const COMMENTS = 3;

    /**
     * Get all the possible values as an option_name => value array.
     */
    public static function all(): array
    {
        $options = [];
        foreach ((new \ReflectionClass(static::class))->getConstants() as $name => $value) {
            $options[strtolower($name)] = $value;
        }

        return $options;
    }

    public static function levelNameToValue(string $level): int
    {
        return static::all()[$level] ?? -1;
    }

    public static function levelValueToName(int $level): string
    {
        foreach (static::all() as $name => $value) {
            if ($level === $value) {
                return $name;
            }
        }

        return 'default';
    }
}
