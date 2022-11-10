<?php

namespace BookStack\Settings;

class UserShortcutMap
{
    protected const DEFAULTS = [
        // Header actions
        "home_view" => "1",
        "shelves_view" => "2",
        "books_view" => "3",
        "settings_view" => "4",
        "favourites_view" => "5",
        "profile_view" => "6",
        "global_search" => "/",
        "logout" => "0",

        // Common actions
        "edit" => "e",
        "new" => "n",
        "copy" => "c",
        "delete" => "d",
        "favourite" => "f",
        "export" => "x",
        "sort" => "s",
        "permissions" => "p",
        "move" => "m",
        "revisions" => "r",

        // Navigation
        "next" => "ArrowRight",
        "previous" => "ArrowLeft",
    ];

    /**
     * @var array<string, string>
     */
    protected array $mapping;

    public function __construct(array $map)
    {
        $this->mapping = static::DEFAULTS;
        $this->merge($map);
    }

    /**
     * Merge the given map into the current shortcut mapping.
     */
    protected function merge(array $map): void
    {
        foreach ($map as $key => $value) {
            if (is_string($value) && isset($this->mapping[$key])) {
                $this->mapping[$key] = $value;
            }
        }
    }

    /**
     * Get the shortcut defined for the given ID.
     */
    public function getShortcut(string $id): string
    {
        return $this->mapping[$id] ?? '';
    }

    /**
     * Convert this mapping to JSON.
     */
    public function toJson(): string
    {
        return json_encode($this->mapping);
    }

    /**
     * Create a new instance from the current user's preferences.
     */
    public static function fromUserPreferences(): self
    {
        $userKeyMap = setting()->getForCurrentUser('ui-shortcuts');
        return new self(json_decode($userKeyMap, true) ?: []);
    }
}
