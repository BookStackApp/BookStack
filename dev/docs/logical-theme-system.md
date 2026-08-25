# Logical Theme System

BookStack allows logical customization via the theme system which enables you to add, or extend, functionality within the PHP side of the system without needing to alter the core application files.

This is part of the theme system alongside the [visual theme system](./visual-theme-system.md).

**Note:** This system is considered semi-stable. The `Theme::` system is kept maintained but specific customizations or deeper app/framework usage using this system will not be supported nor considered in any way stable. Customizations using this system should be checked after updates.

## Getting Started

*[Video Guide](https://www.youtube.com/watch?v=YVbpm_35crQ)*

This makes use of the theme system. Create a folder for your theme within your BookStack `themes` directory. As an example we'll use `my_theme`, so we'd create a `themes/my_theme` folder.
You'll need to tell BookStack to use your theme via the `APP_THEME` option in your `.env` file. For example: `APP_THEME=my_theme`.

Within your theme folder create a `functions.php` file. BookStack will look for this and run it during app boot-up. Within this file you can use the `Theme` facade API, described below, to hook into certain app events.

## `Theme` Facade API

Below details the public methods of the `Theme` facade that are considered stable:

### `Theme::listen`

This method listens to a system event and runs the given action when that event occurs. The arguments passed to the action depend on the event. Event names are exposed as static properties on the `\BookStack\Theming\ThemeEvents` class. 

It is possible to listen to a single event using multiple actions. When dispatched, BookStack will loop over and run each action for that event.
If an action returns a non-null value then BookStack will stop cycling through actions at that point and make use of the non-null return value if possible (Depending on the event).

**Arguments**
- string $event
- callable $action

**Example**

```php
Theme::listen(
    \BookStack\Theming\ThemeEvents::AUTH_LOGIN,
    function($service, $user) {
        \Log::info("Login by {$user->name} via {$service}");
    }
);
```

### `Theme::addSocialDriver`

This method allows you to register a custom social authentication driver within the system. This is primarily intended to use with [Socialite Providers](https://socialiteproviders.com/).

**Arguments**
- string $driverName
- array $config
- string $socialiteHandler
- callable|null $configureForRedirect = null

**Example**

*See "Custom Socialite Service Example" below.*

### `Theme::registerCommand`

This method allows you to register a custom command which can then be used via the artisan console.

**Arguments**
- string $driverName
- array $config
- string $socialiteHandler

**Example**

*See "Custom Command Registration Example" below for a more detailed example.*

```php
Theme::registerCommand(new SayHelloCommand());
```

## Available Events

All available events dispatched by BookStack are exposed as static properties on the `\BookStack\Theming\ThemeEvents` class, which can be found within the file `app/Theming/ThemeEvents.php` relative to your root BookStack folder. Alternatively, the events for the latest release can be [seen on Codeberg here](https://codeberg.org/bookstack/bookstack/src/branch/release/app/Theming/ThemeEvents.php).

The comments above each constant with the `ThemeEvents.php` file describe the dispatch conditions of the event, in addition to the arguments the action will receive. The comments may also describe any ways the return value of the action may be used. 

## Example `functions.php` file

```php
<?php

use BookStack\Facades\Theme;
use BookStack\Theming\ThemeEvents;

// Logs custom message on user login
Theme::listen(ThemeEvents::AUTH_LOGIN, function($method, $user) {
    Log::info("Login via {$method} for {$user->name}");
});

// Adds a `/info` public URL endpoint that emits php debug details
Theme::listen(ThemeEvents::APP_BOOT, function($app) {
    \Route::get('info', function() {
        phpinfo(); // Don't do this on a production instance!
    });
});
```

## Custom View Registration Example

Using the logical theme system, you can register custom views to be rendered before/after other existing views, providing a flexible way to add content without needing to override and/or replicate existing content. This is done by listening to the `THEME_REGISTER_VIEWS`.

**Note:** You don't need to use this to override existing views, or register whole new main views to use, since that's done automatically based on their existence. This is just for advanced capabilities like inserting before/after existing views.

This event provides a `ThemeViews` instance which has the following methods made available:

- `renderBefore(string $targetView, string $localView, int $priority)`
- `renderAfter(string $targetView, string $localView, int $priority)`

The target view is the name of that which we want to insert our custom view relative to.
The local view is the name of the view we want to add and render.
The priority provides a suggestion to the ordering of view display, with lower numbers being shown first. This defaults to 50 if not provided.

Here's an example of this in use:

```php
<?php

use BookStack\Facades\Theme;
use BookStack\Theming\ThemeEvents;
use BookStack\Theming\ThemeViews;

Theme::listen(ThemeEvents::THEME_REGISTER_VIEWS, function (ThemeViews $themeViews) {
    $themeViews->renderBefore('layouts.parts.header', 'welcome-banner', 4);
    $themeViews->renderAfter('layouts.parts.header', 'information-alert');
    $themeViews->renderAfter('layouts.parts.header', 'additions.password-notice', 20);
});
```

In this example, we're inserting custom views before and after the main header bar.
BookStack will look for a `welcome-banner.blade.php` file within our theme folder (or a theme module view folder) to render before the header. It'll look for the `information-alert.blade.php` and `additions/password-notice.blade.php` views to render afterwards.
The password notice will be shown above the information alert view, since it has a specified priority of 20, whereas the information alert view would default to a priority of 50.

## Custom Command Registration Example

The logical theme system supports adding custom [artisan commands](https://laravel.com/docs/8.x/artisan) to BookStack.
These can be registered in your `functions.php` file by calling `Theme::registerCommand($command)`, where `$command` is an instance of `\Symfony\Component\Console\Command\Command`. 

Below is an example of registering a command that could then be ran using `php artisan bookstack:meow` on the command line.

```php
<?php

use BookStack\Facades\Theme;
use Illuminate\Console\Command;

class MeowCommand extends Command
{
    protected $signature = 'bookstack:meow';
    protected $description = 'Say meow on the command line';

    public function handle()
    {
        $this->line('Meow there!');
    }
}

Theme::registerCommand(new MeowCommand);
```

## Custom Socialite Service Example

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

In some cases you may need to customize the driver before it performs a redirect. 
This can be done by providing a callback as a fourth parameter like so:

```php
Theme::addSocialDriver('reddit', [
    'client_id' => 'abc123',
    'client_secret' => 'def456789',
    'name' => 'Reddit',
], '\SocialiteProviders\Reddit\RedditExtendSocialite@handle', function($driver) {
    $driver->with(['prompt' => 'select_account']);
    $driver->scopes(['open_id']);
});
```

## Custom View Layout Block Example

By listening to the `ThemeEvents::VIEW_BLOCKS_REGISTER` event, it's possible to register
custom view blocks which can be shown in a layout in the application. View blocks are typically the sections shown in the sidebar,
or the cards shown on the home view.

As an example of using this system, we'll define a custom view block which will show on the default home view to state how many
books there are in the system.
To start, we'll need to create a custom class to represent our view block. This must implement `\BookStack\View\ViewBlockInterface`:

```php
use BookStack\Entities\Queries\BookQueries;
use BookStack\View\ViewBlockInterface;
use BookStack\View\ViewBlockManager;

class BookTotalBlock implements ViewBlockInterface {

    public function __construct(
        protected BookQueries $bookQueries
    ) {
    }

    public static function getId(): string
    {
        return 'custom_book_total_block';
    }

    public static function getLabel(): string
    {
        return 'Total books displays';
    }

    public function getView(array $viewData): string
    {
        return 'blocks.total-blocks';
    }

    public function withData(array $viewData): array
    {
        $totalBooks = $this->bookQueries->visibleForList()->count();
        return [
            'totalBooks' => $totalBooks,
        ];
    }
}
```

The interface has a few required methods:

- The `getId` method must provide a unique per-block-type string ID.
- The `getLabel` method must provide a general string label for the block.
- The `getView` method provides a string path to a view file (Can be one custom registered).
  - This is provided the available view data at time of render, so it can be dynamic based on context. 
- The `withData` method is called when block is being rendered, and it should return an array of data which will be merged with existing view data.
  - This is also provided available view data, for use as context.

In this example, we're also making use of the `BookQueries` internal BookStack class.
You're able to inject any other dependent classes/services via the constructor like this, and BookStack will attempt to auto-resolve them.  

We can then register this block class using the logical theme system like so:

```php
use BookStack\Facades\Theme;
use BookStack\Theming\ThemeEvents;
use BookStack\View\ViewBlockManager;

Theme::listen(ThemeEvents::VIEW_BLOCKS_REGISTER, function (ViewBlockManager $manager) {
    $manager->register(
        'home-default',       // The location/layout where this block will be displayed
        'right',              // The default position for the block within the location
        BookTotalBlock::class // The block class to register
    );
});
```

The above registration code would typically be within your `functions.php` theme file.
You could also define the above block class in the same file or separate it into its own file
and include it from the `functions.php` via a `require_once()` call.

Lastly, we'll need to create the view for the registered block.
In our example we return `blocks.total-blocks` from the `getView` method, so our view needs to be located at
`blocks/total-blocks.blade.php` from a view providing directory. 
In a theme folder, we can just create it at `blocks/total-blocks.blade.php` within the folder path.
If we were building in a module, we'd need to create this at `views/blocks/total-blocks.blade.php` within our module folder.
For our example, we'll use this content to make use of the data we're passing to the view:

```html
<div class="card mb-xl">
  <h3 class="card-title">Total Books</h3>
  <div class="px-m pb-xs">
    <p>
      There are currently {{ $totalBooks }} books in the system!
    </p>
  </div>
</div>
```

This will then show up on the default home grid view, in the right column.
The exact position within that location may change depending on user preferences, but the block will be limited
to the location provided during registration unless it has also been registered for other locations.
