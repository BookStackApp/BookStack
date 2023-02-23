<?php

namespace BookStack\Settings;

use BookStack\Auth\User;

/**
 * Class SettingService
 * The settings are a simple key-value database store.
 * For non-authenticated users, user settings are stored via the session instead.
 * A local array-based cache is used to for setting accesses across a request.
 */
class SettingService
{
    protected array $localCache = [];

    /**
     * Gets a setting from the database,
     * If not found, Returns default, Which is false by default.
     */
    public function get(string $key, $default = null): mixed
    {
        if (is_null($default)) {
            $default = config('setting-defaults.' . $key, false);
        }

        $value = $this->getValueFromStore($key) ?? $default;
        return $this->formatValue($value, $default);
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
     * Gets a setting value from the local cache.
     * Will load the local cache if not previously loaded.
     */
    protected function getValueFromStore(string $key): mixed
    {
        $cacheCategory = $this->localCacheCategory($key);
        if (!isset($this->localCache[$cacheCategory])) {
            $this->loadToLocalCache($cacheCategory);
        }

        return $this->localCache[$cacheCategory][$key] ?? null;
    }

    /**
     * Put the given value into the local cached under the given key.
     */
    protected function putValueIntoLocalCache(string $key, mixed $value): void
    {
        $cacheCategory = $this->localCacheCategory($key);
        if (!isset($this->localCache[$cacheCategory])) {
            $this->loadToLocalCache($cacheCategory);
        }

        $this->localCache[$cacheCategory][$key] = $value;
    }

    /**
     * Get the category for the given setting key.
     * Will return 'app' for a general app setting otherwise 'user:<user_id>' for a user setting.
     */
    protected function localCacheCategory(string $key): string
    {
        if (str_starts_with($key, 'user:')) {
            return implode(':', array_slice(explode(':', $key), 0, 2));
        }

        return 'app';
    }

    /**
     * For the given category, load the relevant settings from the database into the local cache.
     */
    protected function loadToLocalCache(string $cacheCategory): void
    {
        $query = Setting::query();

        if ($cacheCategory === 'app') {
            $query->where('setting_key', 'not like', 'user:%');
        } else {
            $query->where('setting_key', 'like', $cacheCategory . ':%');
        }
        $settings = $query->toBase()->get();

        if (!isset($this->localCache[$cacheCategory])) {
            $this->localCache[$cacheCategory] = [];
        }

        foreach ($settings as $setting) {
            $value = $setting->value;

            if ($setting->type === 'array') {
                $value = json_decode($value, true) ?? [];
            }

            $this->localCache[$cacheCategory][$setting->setting_key] = $value;
        }
    }

    /**
     * Format a settings value.
     */
    protected function formatValue(mixed $value, mixed $default): mixed
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
    public function put(string $key, mixed $value): bool
    {
        $setting = Setting::query()->firstOrNew([
            'setting_key' => $key,
        ]);

        $setting->type = 'string';
        $setting->value = $value;

        if (is_array($value)) {
            $setting->type = 'array';
            $setting->value = $this->formatArrayValue($value);
        }

        $setting->save();
        $this->putValueIntoLocalCache($key, $value);

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
    public function putForCurrentUser(string $key, string $value): bool
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

        $cacheCategory = $this->localCacheCategory($key);
        if (isset($this->localCache[$cacheCategory])) {
            unset($this->localCache[$cacheCategory][$key]);
        }
    }

    /**
     * Delete settings for a given user id.
     */
    public function deleteUserSettings(string $userId): void
    {
        Setting::query()
            ->where('setting_key', 'like', $this->userKey($userId) . '%')
            ->delete();
    }

    /**
     * Gets a setting model from the database for the given key.
     */
    protected function getSettingObjectByKey(string $key): ?Setting
    {
        return Setting::query()
            ->where('setting_key', '=', $key)
            ->first();
    }

    /**
     * Empty the local setting value cache used by this service.
     */
    public function flushCache(): void
    {
        $this->localCache = [];
    }
}
