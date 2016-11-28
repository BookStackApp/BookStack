<?php

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
 * @return \BookStack\User
 */
function user()
{
    return auth()->user() ?: \BookStack\User::getDefault();
}

/**
 * Check if the current user has a permission.
 * If an ownable element is passed in the jointPermissions are checked against
 * that particular item.
 * @param $permission
 * @param Ownable $ownable
 * @return mixed
 */
function userCan($permission, Ownable $ownable = null)
{
    if ($ownable === null) {
        return user() && user()->can($permission);
    }

    // Check permission on ownable item
    $permissionService = app(\BookStack\Services\PermissionService::class);
    return $permissionService->checkOwnableUserAccess($ownable, $permission);
}

/**
 * Helper to access system settings.
 * @param $key
 * @param bool $default
 * @return mixed
 */
function setting($key, $default = false)
{
    $settingService = app(\BookStack\Services\SettingService::class);
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
    $isFullUrl = strpos($path, 'http') === 0;
    if ($isFullUrl && !$forceAppDomain) return $path;
    $path = trim($path, '/');

    // Remove non-specified domain if forced and we have a domain
    if ($isFullUrl && $forceAppDomain) {
        $explodedPath = explode('/', $path);
        $path = implode('/', array_splice($explodedPath, 3));
    }

    // Return normal url path if not specified in config
    if (config('app.url') === '') {
        return url($path);
    }

    return rtrim(config('app.url'), '/') . '/' . $path;
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
        if ($trimmedVal === '') continue;
        $queryStringSections[] = urlencode($name) . '=' . urlencode($trimmedVal);
    }

    if (count($queryStringSections) === 0) return $path;

    return baseUrl($path . '?' . implode('&', $queryStringSections));
}
