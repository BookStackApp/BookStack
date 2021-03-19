# Logic Theme Service


#### Custom Socialite Service Example

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