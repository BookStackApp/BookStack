<?php namespace BookStack\Services;

use BookStack\Setting;
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
        $value = $this->getValueFromStore($key, $default);
        return $this->formatValue($value, $default);
    }

    /**
     * Gets a setting value from the cache or database.
     * @param $key
     * @param $default
     * @return mixed
     */
    protected function getValueFromStore($key, $default)
    {
        $overrideValue = $this->getOverrideValue($key);
        if ($overrideValue !== null) return $overrideValue;

        $cacheKey = $this->cachePrefix . $key;
        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

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
        if ($value === 'true') $value = true;
        if ($value === 'false') $value = false;

        // Set to default if empty
        if ($value === '') $value = $default;
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
        if ($key === 'registration-enabled' && config('auth.method') === 'ldap') return false;
        return null;
    }

}