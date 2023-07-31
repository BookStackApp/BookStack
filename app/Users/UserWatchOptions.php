<?php

namespace BookStack\Users;

class UserWatchOptions
{
    protected static array $levelByOption = [
        'default' => -1,
        'ignore' => 0,
        'new' => 1,
        'updates' => 2,
        'comments' => 3,
    ];

    /**
     * @return string[]
     */
    public static function getAvailableOptionNames(): array
    {
        return array_keys(static::$levelByOption);
    }
}
