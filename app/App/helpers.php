<?php

use BookStack\App\Model;
use BookStack\Permissions\PermissionApplicator;
use BookStack\Settings\SettingService;
use BookStack\Users\Models\User;

/**
 * Get the path to a versioned file.
 *
 * @throws Exception
 */
function versioned_asset(string $file = ''): string
{
    static $version = null;

    if (is_null($version)) {
        $versionFile = base_path('version');
        $version = trim(file_get_contents($versionFile));
    }

    $additional = '';
    if (config('app.env') === 'development') {
        $additional = sha1_file(public_path($file));
    }

    $path = $file . '?version=' . urlencode($version) . $additional;

    return url($path);
}

/**
 * Helper method to get the current User.
 * Defaults to public 'Guest' user if not logged in.
 */
function user(): User
{
    return auth()->user() ?: User::getGuest();
}

/**
 * Check if the current user has a permission. If an ownable element
 * is passed in the jointPermissions are checked against that particular item.
 */
function userCan(string $permission, Model $ownable = null): bool
{
    if ($ownable === null) {
        return user()->can($permission);
    }

    // Check permission on ownable item
    $permissions = app()->make(PermissionApplicator::class);

    return $permissions->checkOwnableUserAccess($ownable, $permission);
}

/**
 * Check if the current user can perform the given action on any items in the system.
 * Can be provided the class name of an entity to filter ability to that specific entity type.
 */
function userCanOnAny(string $action, string $entityClass = ''): bool
{
    $permissions = app()->make(PermissionApplicator::class);

    return $permissions->checkUserHasEntityPermissionOnAny($action, $entityClass);
}

/**
 * Helper to access system settings.
 *
 * @return mixed|SettingService
 */
function setting(string $key = null, $default = null)
{
    $settingService = app()->make(SettingService::class);

    if (is_null($key)) {
        return $settingService;
    }

    return $settingService->get($key, $default);
}

/**
 * Get a path to a theme resource.
 * Returns null if a theme is not configured and
 * therefore a full path is not available for use.
 */
function theme_path(string $path = ''): ?string
{
    $theme = config('view.theme');

    if (!$theme) {
        return null;
    }

    return base_path('themes/' . $theme . ($path ? DIRECTORY_SEPARATOR . $path : $path));
}

/**
 * Generate a URL with multiple parameters for sorting purposes.
 * Works out the logic to set the correct sorting direction
 * Discards empty parameters and allows overriding.
 */
function sortUrl(string $path, array $data, array $overrideData = []): string
{
    $queryStringSections = [];
    $queryData = array_merge($data, $overrideData);

    // Change sorting direction is already sorted on current attribute
    if (isset($overrideData['sort']) && $overrideData['sort'] === $data['sort']) {
        $queryData['order'] = ($data['order'] === 'asc') ? 'desc' : 'asc';
    } elseif (isset($overrideData['sort'])) {
        $queryData['order'] = 'asc';
    }

    foreach ($queryData as $name => $value) {
        $trimmedVal = trim($value);
        if ($trimmedVal === '') {
            continue;
        }
        $queryStringSections[] = urlencode($name) . '=' . urlencode($trimmedVal);
    }

    if (count($queryStringSections) === 0) {
        return url($path);
    }

    return url($path . '?' . implode('&', $queryStringSections));
}
