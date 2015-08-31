<?php namespace Oxbow\Services;

use Oxbow\Setting;

/**
 * Class SettingService
 *
 * The settings are a simple key-value database store.
 *
 * @package Oxbow\Services
 */
class SettingService
{

    protected $setting;

    /**
     * SettingService constructor.
     * @param $setting
     */
    public function __construct(Setting $setting)
    {
        $this->setting = $setting;
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
        $setting = $this->getSettingObjectByKey($key);
        return $setting === null ? $default : $setting->value;
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
        return true;
    }

    /**
     * Gets a setting model from the database for the given key.
     * @param $key
     * @return mixed
     */
    private function getSettingObjectByKey($key)
    {
        return $this->setting->where('setting_key', '=', $key)->first();
    }

}