<?php

use BookStack\Auth\Permissions\PermissionService;
use BookStack\Entities\Entity;
use BookStack\Ownable;

/**
 * Get the path to a versioned file.
 *
 * @param  string $file
 * @return string
 * @throws Exception
 */
function versioned_asset($file = '')
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
    return baseUrl($path);
}

/**
 * Helper method to get the current User.
 * Defaults to public 'Guest' user if not logged in.
 * @return \BookStack\Auth\User
 */
function user()
{
    return auth()->user() ?: \BookStack\Auth\User::getDefault();
}

/**
 * Check if current user is a signed in user.
 * @return bool
 */
function signedInUser() : bool
{
    return auth()->user() && !auth()->user()->isDefault();
}

/**
 * Check if the current user has general access.
 * @return bool
 */
function hasAppAccess() : bool
{
    return !auth()->guest() || setting('app-public');
}

/**
 * Check if the current user has a permission.
 * If an ownable element is passed in the jointPermissions are checked against
 * that particular item.
 * @param string $permission
 * @param Ownable $ownable
 * @return mixed
 */
function userCan(string $permission, Ownable $ownable = null)
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
 * @param string $permission
 * @param string|null $entityClass
 * @return bool
 */
function userCanOnAny(string $permission, string $entityClass = null)
{
    $permissionService = app(PermissionService::class);
    return $permissionService->checkUserHasPermissionOnAnything($permission, $entityClass);
}

/**
 * Helper to access system settings.
 * @param $key
 * @param bool $default
 * @return bool|string|\BookStack\Settings\SettingService
 */
function setting($key = null, $default = false)
{
    $settingService = resolve(\BookStack\Settings\SettingService::class);
    if (is_null($key)) {
        return $settingService;
    }
    return $settingService->get($key, $default);
}

/**
 * Helper to create url's relative to the applications root path.
 * @param string $path
 * @param bool $forceAppDomain
 * @return string
 */
function baseUrl($path, $forceAppDomain = false)
{
    return url($path);
    $isFullUrl = strpos($path, 'http') === 0;
    if ($isFullUrl && !$forceAppDomain) {
        return $path;
    }

    $path = trim($path, '/');
    $base = rtrim(config('app.url'), '/');

    // Remove non-specified domain if forced and we have a domain
    if ($isFullUrl && $forceAppDomain) {
        if (!empty($base) && strpos($path, $base) === 0) {
            $path = mb_substr($path, mb_strlen($base));
        } else {
            $explodedPath = explode('/', $path);
            $path = implode('/', array_splice($explodedPath, 3));
        }
    }

    // Return normal url path if not specified in config
    if (config('app.url') === '') {
        return url($path);
    }

    return $base . '/' . ltrim($path, '/');
}

/**
 * Get an instance of the redirector.
 * Overrides the default laravel redirect helper.
 * Ensures it redirects even when the app is in a subdirectory.
 *
 * @param  string|null  $to
 * @param  int     $status
 * @param  array   $headers
 * @param  bool    $secure
 * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
 */
function redirect($to = null, $status = 302, $headers = [], $secure = null)
{
    if (is_null($to)) {
        return app('redirect');
    }

    $to = baseUrl($to);

    return app('redirect')->to($to, $status, $headers, $secure);
}

/**
 * Get a path to a theme resource.
 * @param string $path
 * @return string|boolean
 */
function theme_path($path = '')
{
    $theme = config('view.theme');
    if (!$theme) {
        return false;
    }

    return base_path('themes/' . $theme .($path ? DIRECTORY_SEPARATOR.$path : $path));
}

/**
 * Get fetch an SVG icon as a string.
 * Checks for icons defined within a custom theme before defaulting back
 * to the 'resources/assets/icons' folder.
 *
 * Returns an empty string if icon file not found.
 * @param $name
 * @param array $attrs
 * @return mixed
 */
function icon($name, $attrs = [])
{
    $attrs = array_merge([
        'class' => 'svg-icon',
        'data-icon' => $name
    ], $attrs);
    $attrString = ' ';
    foreach ($attrs as $attrName => $attr) {
        $attrString .=  $attrName . '="' . $attr . '" ';
    }

    $iconPath = resource_path('assets/icons/' . $name . '.svg');
    $themeIconPath = theme_path('icons/' . $name . '.svg');
    if ($themeIconPath && file_exists($themeIconPath)) {
        $iconPath = $themeIconPath;
    } else if (!file_exists($iconPath)) {
        return '';
    }

    $fileContents = file_get_contents($iconPath);
    return  str_replace('<svg', '<svg' . $attrString, $fileContents);
}

/**
 * Generate a url with multiple parameters for sorting purposes.
 * Works out the logic to set the correct sorting direction
 * Discards empty parameters and allows overriding.
 * @param $path
 * @param array $data
 * @param array $overrideData
 * @return string
 */
function sortUrl($path, $data, $overrideData = [])
{
    $queryStringSections = [];
    $queryData = array_merge($data, $overrideData);

    // Change sorting direction is already sorted on current attribute
    if (isset($overrideData['sort']) && $overrideData['sort'] === $data['sort']) {
        $queryData['order'] = ($data['order'] === 'asc') ? 'desc' : 'asc';
    } else {
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

    return baseUrl($path . '?' . implode('&', $queryStringSections));
}
