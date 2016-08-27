<?php

use BookStack\Ownable;

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
        return baseUrl($manifest[$file]);
    }

    if (file_exists(public_path($file))) {
        return baseUrl($file);
    }

    throw new InvalidArgumentException("File {$file} not defined in asset manifest.");
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
        return auth()->user() && auth()->user()->can($permission);
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
    $settingService = app('BookStack\Services\SettingService');
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

    if ($isFullUrl && $forceAppDomain) {
        $explodedPath = explode('/', $path);
        $path = implode('/', array_splice($explodedPath, 3));
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