# Logical Theme System

BookStack allows logical customization via the theme system enables you to add, or extend, functionality within the PHP side of the system without needing to alter the core application files.

WARNING: This system is currently in alpha so may incur changes. Once we've gathered some feedback on usage we'll look to removing this warning. This system will be considered semi-stable in the future. The `Theme::` system will be kept maintained but specific customizations or deeper app/framework usage using this system will not be supported nor considered in any way stable. Customizations using this system should be checked after updates.

### Getting Started

This makes use of the theme system. Create a folder for your theme within your BookStack `themes` directory. As an example we'll use `my_theme`, so we'd create a `themes/my_theme` folder.
You'll need to tell BookStack to use your theme via the `APP_THEME` option in your `.env` file. For example: `APP_THEME=my_theme`.

Within your theme folder create a `functions.php` file. BookStack will look for this and run this during app boot-up. Within this file you can use the `Theme` facade API, described below, to hook into certain app events.

### `Theme` Facade API

Below details the public methods of the `Theme` facade that are considered stable:

#### `Theme::listen`

This method listens to a system event and runs the given action when that event occurs. The arguments passed to the action depend on the event. Event names are exposed as static properties on the `\BookStack\Theming\ThemeEvents` class. 

It is possible to listen to a single event using multiple actions. When dispatched, BookStack will loop over and run each action for that event.
If an action returns a non-null value then BookStack will stop cycling through actions at that point and make use of the non-null return value if possible (Depending on the event).

**Arguments**
- string $event
- callback $action

**Example**

```php
Theme::listen(
    \BookStack\Theming\ThemeEvents::AUTH_LOGIN,
    function($service, $user) {
        \Log::info("Login by {$user->name} via {$service}");
    }
);
```

#### `Theme::addSocialDriver`

This method allows you to register a custom social authentication driver within the system. This is primarily intended to use with [Socialite Providers](https://socialiteproviders.com/).

**Arguments**
- string $driverName
- array $config
- string $socialiteHandler

**Example**

*See "Custom Socialite Service Example" below.*

### Available Events

All available events dispatched by BookStack are exposed as static properties on the `\BookStack\Theming\ThemeEvents` class, which can be found within the file `app/Theming/ThemeEvents.php` relative to your root BookStack folder. Alternatively, the events for the latest release can be [seen on GitHub here](https://github.com/BookStackApp/BookStack/blob/release/app/Theming/ThemeEvents.php).

The comments above each constant with the `ThemeEvents.php` file describe the dispatch conditions of the event, in addition to the arguments the action will receive. The comments may also describe any ways the return value of the action may be used. 

### Example `functions.php` file

```php
<?php

use BookStack\Facades\Theme;
use BookStack\Theming\ThemeEvents;

// Log custom message on user login
Theme::listen(ThemeEvents::AUTH_LOGIN, function($method, $user) {
    Log::info("Login via {$method} for {$user->name}");
});

// Add a `/info` public URL endpoint that emits php debug details
Theme::listen(ThemeEvents::APP_BOOT, function($app) {
    \Route::get('info', function() {
        phpinfo(); // Don't do this on a production instance
    });
});
```

### Custom Socialite Service Example

The below shows an example of adding a custom reddit socialite service to BookStack. 
BookStack exposes a helper function for this via `Theme::addSocialDriver` which sets the required config and event listeners in the platform.

The require statements reference composer installed dependencies within the theme folder. They are required manually since they are not auto-loaded like other app files due to being outside the main BookStack dependency list. 

```php
require "vendor/socialiteproviders/reddit/Provider.php";
require "vendor/socialiteproviders/reddit/RedditExtendSocialite.php";

Theme::listen(ThemeEvents::APP_BOOT, function($app) {
    Theme::addSocialDriver('reddit', [
        'client_id' => 'abc123',
        'client_secret' => 'def456789',
        'name' => 'Reddit',
    ], '\SocialiteProviders\Reddit\RedditExtendSocialite@handle');
});
```