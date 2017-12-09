<p align="center"><img src="https://laravel.com/assets/img/components/logo-socialite.svg"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/socialite"><img src="https://travis-ci.org/laravel/socialite.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/socialite"><img src="https://poser.pugx.org/laravel/socialite/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/socialite"><img src="https://poser.pugx.org/laravel/socialite/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/socialite"><img src="https://poser.pugx.org/laravel/socialite/license.svg" alt="License"></a>
</p>

## Introduction

Laravel Socialite provides an expressive, fluent interface to OAuth authentication with Facebook, Twitter, Google, LinkedIn, GitHub and Bitbucket. It handles almost all of the boilerplate social authentication code you are dreading writing.

**We are not accepting new adapters.**

Adapters for other platforms are listed at the community driven [Socialite Providers](https://socialiteproviders.github.io/) website.

## License

Laravel Socialite is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

## Official Documentation

In addition to typical, form based authentication, Laravel also provides a simple, convenient way to authenticate with OAuth providers using [Laravel Socialite](https://github.com/laravel/socialite). Socialite currently supports authentication with Facebook, Twitter, LinkedIn, Google, GitHub and Bitbucket.

To get started with Socialite, use Composer to add the package to your project's dependencies:

    composer require laravel/socialite

### Configuration

After installing the Socialite library, register the `Laravel\Socialite\SocialiteServiceProvider` in your `config/app.php` configuration file:

```php
'providers' => [
    // Other service providers...

    Laravel\Socialite\SocialiteServiceProvider::class,
],
```

Also, add the `Socialite` facade to the `aliases` array in your `app` configuration file:

```php
'Socialite' => Laravel\Socialite\Facades\Socialite::class,
```

You will also need to add credentials for the OAuth services your application utilizes. These credentials should be placed in your `config/services.php` configuration file, and should use the key `facebook`, `twitter`, `linkedin`, `google`, `github` or `bitbucket`, depending on the providers your application requires. For example:
```php
'github' => [
    'client_id' => 'your-github-app-id',
    'client_secret' => 'your-github-app-secret',
    'redirect' => 'http://your-callback-url',
],
```
### Basic Usage

Next, you are ready to authenticate users! You will need two routes: one for redirecting the user to the OAuth provider, and another for receiving the callback from the provider after authentication. We will access Socialite using the `Socialite` facade:

```php
<?php

namespace App\Http\Controllers\Auth;

use Socialite;

class AuthController extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('github')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return Response
     */
    public function handleProviderCallback()
    {
        $user = Socialite::driver('github')->user();

        // $user->token;
    }
}
```

The `redirect` method takes care of sending the user to the OAuth provider, while the `user` method will read the incoming request and retrieve the user's information from the provider. Before redirecting the user, you may also set "scopes" on the request using the `scope` method. This method will overwrite all existing scopes:

```php
return Socialite::driver('github')
            ->scopes(['scope1', 'scope2'])->redirect();
```

Of course, you will need to define routes to your controller methods:

```php
Route::get('auth/github', 'Auth\AuthController@redirectToProvider');
Route::get('auth/github/callback', 'Auth\AuthController@handleProviderCallback');
```

A number of OAuth providers support optional parameters in the redirect request. To include any optional parameters in the request, call the `with` method with an associative array:

```php
return Socialite::driver('google')
            ->with(['hd' => 'example.com'])->redirect();
```

When using the `with` method, be careful not to pass any reserved keywords such as `state` or `response_type`.

#### Stateless Authentication

The `stateless` method may be used to disable session state verification. This is useful when adding social authentication to an API:

```php
return Socialite::driver('google')->stateless()->user();
```


#### Retrieving User Details

Once you have a user instance, you can grab a few more details about the user:

```php
$user = Socialite::driver('github')->user();

// OAuth Two Providers
$token = $user->token;
$refreshToken = $user->refreshToken; // not always provided
$expiresIn = $user->expiresIn;

// OAuth One Providers
$token = $user->token;
$tokenSecret = $user->tokenSecret;

// All Providers
$user->getId();
$user->getNickname();
$user->getName();
$user->getEmail();
$user->getAvatar();
```

#### Retrieving User Details From Token

If you already have a valid access token for a user, you can retrieve their details using the `userFromToken` method:

```php
$user = Socialite::driver('github')->userFromToken($token);
```
