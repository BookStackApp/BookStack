<?php

use BookStack\App\AppVersion;
use BookStack\App\Model;
use BookStack\Facades\Theme;
use BookStack\Permissions\Permission;
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
    $version = AppVersion::get();

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
 * Get the enabled authentication methods in configured priority order.
 *
 * @return array<int, string>
 */
function auth_methods(): array
{
    $validMethods = ['standard', 'ldap', 'saml2', 'oidc'];
    $methodsConfig = config('auth.methods', '');
    $singleMethod = config('auth.method', 'standard');

    $methods = is_string($methodsConfig)
        ? array_map('trim', explode(',', $methodsConfig))
        : (array) $methodsConfig;

    $methods = array_values(array_unique(array_filter($methods, function (mixed $method) use ($validMethods) {
        return is_string($method) && in_array($method, $validMethods);
    })));

    if (count($methods) === 0 && in_array($singleMethod, $validMethods)) {
        $methods[] = $singleMethod;
    }

    return $methods;
}

/**
 * Check if the given authentication method is enabled.
 */
function auth_method_enabled(string $method): bool
{
    return in_array($method, auth_methods());
}

/**
 * Get the primary configured authentication method.
 */
function auth_primary_method(): string
{
    $primaryMethod = config('auth.primary_method', '');
    if (is_string($primaryMethod) && auth_method_enabled($primaryMethod)) {
        return $primaryMethod;
    }

    $singleMethod = config('auth.method', 'standard');
    if (is_string($singleMethod) && auth_method_enabled($singleMethod)) {
        return $singleMethod;
    }

    return auth_methods()[0] ?? 'standard';
}

/**
 * Get the authentication method used for the current session, where known.
 */
function auth_session_method(): string
{
    $sessionMethod = session()->get('auth-login-method');
    if (is_string($sessionMethod) && auth_method_enabled($sessionMethod)) {
        return $sessionMethod;
    }

    foreach (['standard', 'ldap', 'oidc', 'saml2'] as $guard) {
        if (auth_method_enabled($guard) && auth($guard)->check()) {
            return $guard;
        }
    }

    return auth_primary_method();
}

/**
 * Check if the current user has a permission. If an ownable element
 * is passed in the jointPermissions are checked against that particular item.
 */
function userCan(string|Permission $permission, ?Model $ownable = null): bool
{
    if (is_null($ownable)) {
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
function userCanOnAny(string|Permission $action, string $entityClass = ''): bool
{
    $permissions = app()->make(PermissionApplicator::class);

    return $permissions->checkUserHasEntityPermissionOnAny($action, $entityClass);
}

/**
 * Helper to access system settings.
 *
 * @return mixed|SettingService
 */
function setting(?string $key = null, mixed $default = null): mixed
{
    $settingService = app()->make(SettingService::class);

    if (is_null($key)) {
        return $settingService;
    }

    return $settingService->get($key, $default);
}

/**
 * Get a path to a theme resource.
 * Returns null if a theme is not configured, and therefore a full path is not available for use.
 */
function theme_path(string $path = ''): ?string
{
    $theme = Theme::getTheme();
    if (!$theme) {
        return null;
    }

    return base_path('themes/' . $theme . ($path ? DIRECTORY_SEPARATOR . $path : $path));
}
