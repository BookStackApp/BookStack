<?php

use BookStack\Auth\Permissions\PermissionService;
use BookStack\Auth\User;
use BookStack\Model;
use BookStack\Settings\SettingService;

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
    return auth()->user() ?: User::getDefault();
}

/**
 * Check if current user is a signed in user.
 */
function signedInUser(): bool
{
    return auth()->user() && !auth()->user()->isDefault();
}

/**
 * Check if the current user has general access.
 */
function hasAppAccess(): bool
{
    return !auth()->guest() || setting('app-public');
}

/**
 * Check if the current user has a permission. If an ownable element
 * is passed in the jointPermissions are checked against that particular item.
 */
function userCan(string $permission, Model $ownable = null): bool
{
    if ($ownable === null) {
        return user() && user()->can($permission);
    }

    // Check permission on ownable item
    $permissionService = app(PermissionService::class);

    return $permissionService->checkOwnableUserAccess($ownable, $permission);
}

/**
 * Check if the current user has the given permission
 * on any item in the system.
 */
function userCanOnAny(string $permission, string $entityClass = null): bool
{
    $permissionService = app(PermissionService::class);

    return $permissionService->checkUserHasPermissionOnAnything($permission, $entityClass);
}

/**
 * Helper to access system settings.
 *
 * @return mixed|SettingService
 */
function setting(string $key = null, $default = null)
{
    $settingService = resolve(SettingService::class);

    if (is_null($key)) {
        return $settingService;
    }

    return $settingService->get($key, $default);
}

/**
 * Get a path to a theme resource.
 */
function theme_path(string $path = ''): string
{
    $theme = config('view.theme');

    if (!$theme) {
        return '';
    }

    return base_path('themes/' . $theme . ($path ? DIRECTORY_SEPARATOR . $path : $path));
}

/**
 * Get fetch an SVG icon as a string.
 * Checks for icons defined within a custom theme before defaulting back
 * to the 'resources/assets/icons' folder.
 *
 * Returns an empty string if icon file not found.
 */
function icon(string $name, array $attrs = []): string
{
    $attrs = array_merge([
        'class'     => 'svg-icon',
        'data-icon' => $name,
        'role'      => 'presentation',
    ], $attrs);
    $attrString = ' ';
    foreach ($attrs as $attrName => $attr) {
        $attrString .= $attrName . '="' . $attr . '" ';
    }

    $iconPath = resource_path('icons/' . $name . '.svg');
    $themeIconPath = theme_path('icons/' . $name . '.svg');

    if ($themeIconPath && file_exists($themeIconPath)) {
        $iconPath = $themeIconPath;
    } elseif (!file_exists($iconPath)) {
        return '';
    }

    $fileContents = file_get_contents($iconPath);

    return  str_replace('<svg', '<svg' . $attrString, $fileContents);
}

/**
 * Generate a url with multiple parameters for sorting purposes.
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
        return $path;
    }

    return url($path . '?' . implode('&', $queryStringSections));
}
