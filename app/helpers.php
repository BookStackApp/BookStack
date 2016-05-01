<?php

if (!function_exists('versioned_asset')) {
    /**
     * Get the path to a versioned file.
     *
     * @param  string $file
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    function versioned_asset($file)
    {
        static $manifest = null;

        if (is_null($manifest)) {
            $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
        }

        if (isset($manifest[$file])) {
            return '/' . $manifest[$file];
        }

        if (file_exists(public_path($file))) {
            return '/' . $file;
        }

        throw new InvalidArgumentException("File {$file} not defined in asset manifest.");
    }
}

/**
 * Check if the current user has a permission.
 * If an ownable element is passed in the jointPermissions are checked against
 * that particular item.
 * @param $permission
 * @param \BookStack\Ownable $ownable
 * @return mixed
 */
function userCan($permission, \BookStack\Ownable $ownable = null)
{
    if ($ownable === null) {
        return auth()->user() && auth()->user()->can($permission);
    }

    // Check permission on ownable item
    $permissionService = app('BookStack\Services\PermissionService');
    return $permissionService->checkEntityUserAccess($ownable, $permission);
}

/**
 * Helper to access system settings.
 * @param $key
 * @param bool $default
 * @return mixed
 */
function setting($key, $default = false)
{
    $settingService = app('BookStack\Services\SettingService');
    return $settingService->get($key, $default);
}
