<?php

namespace BookStack\Settings;

use BookStack\Auth\User;
use Illuminate\Contracts\Cache\Repository as Cache;

/**
 * Class SettingService
 * The settings are a simple key-value database store.
 * For non-authenticated users, user settings are stored via the session instead.
 */
class SettingService
{
    protected $setting;
    protected $cache;
    protected $localCache = [];

    protected $cachePrefix = 'setting-';

    /**
     * SettingService constructor.
     */
    public function __construct(Setting $setting, Cache $cache)
    {
        $this->setting = $setting;
        $this->cache = $cache;
    }

    /**
     * Gets a setting from the database,
     * If not found, Returns default, Which is false by default.
     */
    public function get(string $key, $default = null)
    {
        if (is_null($default)) {
            $default = config('setting-defaults.' . $key, false);
        }

        if (isset($this->localCache[$key])) {
            return $this->localCache[$key];
        }

        $value = $this->getValueFromStore($key) ?? $default;
        $formatted = $this->formatValue($value, $default);
        $this->localCache[$key] = $formatted;

        return $formatted;
    }

    /**
     * Get a value from the session instead of the main store option.
     */
    protected function getFromSession(string $key, $default = false)
    {
        $value = session()->get($key, $default);

        return $this->formatValue($value, $default);
    }

    /**
     * Get a user-specific setting from the database or cache.
     */
    public function getUser(User $user, string $key, $default = null)
    {
        if (is_null($default)) {
            $default = config('setting-defaults.user.' . $key, false);
        }

        if ($user->isDefault()) {
            return $this->getFromSession($key, $default);
        }

        return $this->get($this->userKey($user->id, $key), $default);
    }

    /**
     * Get a value for the current logged-in user.
     */
    public function getForCurrentUser(string $key, $default = null)
    {
        return $this->getUser(user(), $key, $default);
    }

    /**
     * Gets a setting value from the cache or database.
     * Looks at the system defaults if not cached or in database.
     * Returns null if nothing is found.
     */
    protected function getValueFromStore(string $key)
    {
        // Check the cache
        $cacheKey = $this->cachePrefix . $key;
        $cacheVal = $this->cache->get($cacheKey, null);
        if ($cacheVal !== null) {
            return $cacheVal;
        }

        // Check the database
        $settingObject = $this->getSettingObjectByKey($key);
        if ($settingObject !== null) {
            $value = $settingObject->value;

            if ($settingObject->type === 'array') {
                $value = json_decode($value, true) ?? [];
            }

            $this->cache->forever($cacheKey, $value);

            return $value;
        }

        return null;
    }

    /**
     * Clear an item from the cache completely.
     */
    protected function clearFromCache(string $key)
    {
        $cacheKey = $this->cachePrefix . $key;
        $this->cache->forget($cacheKey);
        if (isset($this->localCache[$key])) {
            unset($this->localCache[$key]);
        }
    }

    /**
     * Format a settings value.
     */
    protected function formatValue($value, $default)
    {
        // Change string booleans to actual booleans
        if ($value === 'true') {
            $value = true;
        } elseif ($value === 'false') {
            $value = false;
        }

        // Set to default if empty
        if ($value === '') {
            $value = $default;
        }

        return $value;
    }

    /**
     * Checks if a setting exists.
     */
    public function has(string $key): bool
    {
        $setting = $this->getSettingObjectByKey($key);

        return $setting !== null;
    }

    /**
     * Add a setting to the database.
     * Values can be an array or a string.
     */
    public function put(string $key, $value): bool
    {
        $setting = $this->setting->newQuery()->firstOrNew([
            'setting_key' => $key,
        ]);
        $setting->type = 'string';

        if (is_array($value)) {
            $setting->type = 'array';
            $value = $this->formatArrayValue($value);
        }

        $setting->value = $value;
        $setting->save();
        $this->clearFromCache($key);

        return true;
    }

    /**
     * Format an array to be stored as a setting.
     * Array setting types are expected to be a flat array of child key=>value array items.
     * This filters out any child items that are empty.
     */
    protected function formatArrayValue(array $value): string
    {
        $values = collect($value)->values()->filter(function (array $item) {
            return count(array_filter($item)) > 0;
        });

        return json_encode($values);
    }

    /**
     * Put a user-specific setting into the database.
     * Can only take string value types since this may use
     * the session which is less flexible to data types.
     */
    public function putUser(User $user, string $key, string $value): bool
    {
        if ($user->isDefault()) {
            session()->put($key, $value);

            return true;
        }

        return $this->put($this->userKey($user->id, $key), $value);
    }

    /**
     * Put a user-specific setting into the database for the current access user.
     * Can only take string value types since this may use
     * the session which is less flexible to data types.
     */
    public function putForCurrentUser(string $key, string $value)
    {
        return $this->putUser(user(), $key, $value);
    }

    /**
     * Convert a setting key into a user-specific key.
     */
    protected function userKey(string $userId, string $key = ''): string
    {
        return 'user:' . $userId . ':' . $key;
    }

    /**
     * Removes a setting from the database.
     */
    public function remove(string $key): void
    {
        $setting = $this->getSettingObjectByKey($key);
        if ($setting) {
            $setting->delete();
        }
        $this->clearFromCache($key);
    }

    /**
     * Delete settings for a given user id.
     */
    public function deleteUserSettings(string $userId)
    {
        return $this->setting->newQuery()
            ->where('setting_key', 'like', $this->userKey($userId) . '%')
            ->delete();
    }

    /**
     * Gets a setting model from the database for the given key.
     */
    protected function getSettingObjectByKey(string $key): ?Setting
    {
        return $this->setting->newQuery()
            ->where('setting_key', '=', $key)->first();
    }
}
