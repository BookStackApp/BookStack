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
    // Don't require css and JS assets for testing
    if (config('app.env') === 'testing') return '';

    static $manifest = null;
    $manifestPath = 'build/manifest.json';

    if (is_null($manifest) && file_exists($manifestPath)) {
        $manifest = json_decode(file_get_contents(public_path($manifestPath)), true);
    } else if (!file_exists($manifestPath)) {
        if (config('app.env') !== 'production') {
            $path = public_path($manifestPath);
            $error = "No {$path} file found, Ensure you have built the css/js assets using gulp.";
        } else {
            $error = "No {$manifestPath} file found, Ensure you are using the release version of BookStack";
        }
        throw new \Exception($error);
    }

    if (isset($manifest[$file])) {
        return baseUrl($manifest[$file]);
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