<?php

namespace BookStack\Activity;

use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;

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
     * @returns array<string, int>
     */
    public static function all(): array
    {
        $options = [];
        foreach ((new \ReflectionClass(static::class))->getConstants() as $name => $value) {
            $options[strtolower($name)] = $value;
        }

        return $options;
    }

    /**
     * Get the watch options suited for the given entity.
     * @returns array<string, int>
     */
    public static function allSuitedFor(Entity $entity): array
    {
        $options = static::all();

        if ($entity instanceof Page) {
            unset($options['new']);
        } elseif ($entity instanceof Bookshelf) {
            return [];
        }

        return $options;
    }

    /**
     * Convert the given name to a level value.
     * Defaults to default value if the level does not exist.
     */
    public static function levelNameToValue(string $level): int
    {
        return static::all()[$level] ?? static::DEFAULT;
    }

    /**
     * Convert the given int level value to a level name.
     * Defaults to 'default' level name if not existing.
     */
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
