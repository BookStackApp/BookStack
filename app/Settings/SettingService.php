<?php namespace BookStack\Settings;

use Illuminate\Contracts\Cache\Repository as Cache;

/**
 * Class SettingService
 *
 * The settings are a simple key-value database store.
 *
 * @package BookStack\Services
 */
class SettingService
{

    protected $setting;
    protected $cache;
    protected $localCache = [];

    protected $cachePrefix = 'setting-';

    /**
     * SettingService constructor.
     * @param Setting $setting
     * @param Cache   $cache
     */
    public function __construct(Setting $setting, Cache $cache)
    {
        $this->setting = $setting;
        $this->cache = $cache;
    }

    /**
     * Gets a setting from the database,
     * If not found, Returns default, Which is false by default.
     * @param             $key
     * @param string|bool $default
     * @return bool|string
     */
    public function get($key, $default = false)
    {
        if ($default === false) {
            $default = config('setting-defaults.' . $key, false);
        }

        if (isset($this->localCache[$key])) {
            return $this->localCache[$key];
        }

        $value = $this->getValueFromStore($key, $default);
        $formatted = $this->formatValue($value, $default);
        $this->localCache[$key] = $formatted;
        return $formatted;
    }

    /**
     * Get a user-specific setting from the database or cache.
     * @param \BookStack\Auth\User $user
     * @param $key
     * @param bool $default
     * @return bool|string
     */
    public function getUser($user, $key, $default = false)
    {
        return $this->get($this->userKey($user->id, $key), $default);
    }

    /**
     * Gets a setting value from the cache or database.
     * Looks at the system defaults if not cached or in database.
     * @param $key
     * @param $default
     * @return mixed
     */
    protected function getValueFromStore($key, $default)
    {
        // Check for an overriding value
        $overrideValue = $this->getOverrideValue($key);
        if ($overrideValue !== null) {
            return $overrideValue;
        }

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
            $this->cache->forever($cacheKey, $value);
            return $value;
        }

        return $default;
    }

    /**
     * Clear an item from the cache completely.
     * @param $key
     */
    protected function clearFromCache($key)
    {
        $cacheKey = $this->cachePrefix . $key;
        $this->cache->forget($cacheKey);
        if (isset($this->localCache[$key])) {
            unset($this->localCache[$key]);
        }
    }

    /**
     * Format a settings value
     * @param $value
     * @param $default
     * @return mixed
     */
    protected function formatValue($value, $default)
    {
        // Change string booleans to actual booleans
        if ($value === 'true') {
            $value = true;
        }
        if ($value === 'false') {
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
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        $setting = $this->getSettingObjectByKey($key);
        return $setting !== null;
    }

    /**
     * Check if a user setting is in the database.
     * @param $key
     * @return bool
     */
    public function hasUser($key)
    {
        return $this->has($this->userKey($key));
    }

    /**
     * Add a setting to the database.
     * @param $key
     * @param $value
     * @return bool
     */
    public function put($key, $value)
    {
        $setting = $this->setting->firstOrNew([
            'setting_key' => $key
        ]);
        $setting->value = $value;
        $setting->save();
        $this->clearFromCache($key);
        return true;
    }

    /**
     * Put a user-specific setting into the database.
     * @param \BookStack\Auth\User $user
     * @param $key
     * @param $value
     * @return bool
     */
    public function putUser($user, $key, $value)
    {
        return $this->put($this->userKey($user->id, $key), $value);
    }

    /**
     * Convert a setting key into a user-specific key.
     * @param $key
     * @return string
     */
    protected function userKey($userId, $key = '')
    {
        return 'user:' . $userId . ':' . $key;
    }

    /**
     * Removes a setting from the database.
     * @param $key
     * @return bool
     */
    public function remove($key)
    {
        $setting = $this->getSettingObjectByKey($key);
        if ($setting) {
            $setting->delete();
        }
        $this->clearFromCache($key);
        return true;
    }

    /**
     * Delete settings for a given user id.
     * @param $userId
     * @return mixed
     */
    public function deleteUserSettings($userId)
    {
        return $this->setting->where('setting_key', 'like', $this->userKey($userId) . '%')->delete();
    }

    /**
     * Gets a setting model from the database for the given key.
     * @param $key
     * @return mixed
     */
    protected function getSettingObjectByKey($key)
    {
        return $this->setting->where('setting_key', '=', $key)->first();
    }


    /**
     * Returns an override value for a setting based on certain app conditions.
     * Used where certain configuration options overrule others.
     * Returns null if no override value is available.
     * @param $key
     * @return bool|null
     */
    protected function getOverrideValue($key)
    {
        if ($key === 'registration-enabled' && config('auth.method') === 'ldap') {
            return false;
        }
        return null;
    }
}
