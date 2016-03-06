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
 * If an ownable element is passed in the permissions are checked against
 * that particular item.
 * @param $permission
 * @param \BookStack\Ownable $ownable
 * @return mixed
 */
function userCan($permission, \BookStack\Ownable $ownable = null)
{
    if (!auth()->check()) return false;
    if ($ownable === null) {
        return auth()->user() && auth()->user()->can($permission);
    }

    // Check permission on ownable item
    $permissionBaseName = strtolower($permission) . '-';
    $hasPermission = false;
    if (auth()->user()->can($permissionBaseName . 'all')) $hasPermission = true;
    if (auth()->user()->can($permissionBaseName . 'own') && $ownable->createdBy && $ownable->createdBy->id === auth()->user()->id) $hasPermission = true;

    if (!$ownable instanceof \BookStack\Entity) return $hasPermission;

    // Check restrictions on the entitiy
    $restrictionService = app('BookStack\Services\RestrictionService');
    $explodedPermission = explode('-', $permission);
    $action = end($explodedPermission);
    $hasAccess = $restrictionService->checkIfEntityRestricted($ownable, $action);
    return $hasAccess && $hasPermission;
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
