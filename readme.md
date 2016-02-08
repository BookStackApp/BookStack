# BookStack

A platform to create documentation/wiki content. General information about BookStack can be found at https://www.bookstackapp.com/

1. [Requirements](#requirements)
2. [Installation](#installation)
  - [Server Rewrite Rules](#url-rewrite-rules)
3. [Updating](#updating-bookstack)
4. [Social Authentication](#social-authentication)
 - [Google](#google)
 - [GitHub](#github)
5. [LDAP Authentication](#ldap-authentication)
6. [Testing](#testing)
7. [License](#license)
8. [Attribution](#attribution)


## Requirements

BookStack has similar requirements to Laravel:

* PHP >= 5.5.9, Will need to be usable from the command line.
* PHP Extensions: `OpenSSL`, `PDO`, `MBstring`, `Tokenizer`, `GD`
* MySQL >= 5.6
* Git (Not strictly required but helps manage updates)
* [Composer](https://getcomposer.org/)

## Installation

Ensure the above requirements are met before installing. Currently BookStack requires its own domain/subdomain and will not work in a site subdirectory.

This project currently uses the `release` branch of this repository as a stable channel for providing updates.

The installation is currently somewhat complicated and will be made simpler in future releases. Some PHP/Laravel experience will currently benefit.

1. Clone the release branch of this repository into a folder.

```
git clone https://github.com/ssddanbrown/BookStack.git --branch release --single-branch
```

2. `cd` into the application folder and run `composer install`.
3. Copy the `.env.example` file to `.env` and fill with your own database and mail details.
4. Ensure the `storage`, `bootstrap/cache` & `public/uploads` folders are writable by the web server.
5. In the application root, Run `php artisan key:generate` to generate a unique application key.
6. If not using apache or if `.htaccess` files are disabled you will have to create some URL rewrite rules as shown below.
7. Run `php artisan migrate` to update the database.
8. Done! You can now login using the default admin details `admin@admin.com` with a password of `password`. It is recommended to change these details directly after first logging in.

#### URL Rewrite rules

**Apache**
```
Options +FollowSymLinks
RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [L]
```

**Nginx**
```
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```
## Updating BookStack

To update BookStack you can run the following command in the root directory of the application:
```
git pull origin release && composer install && php artisan migrate
```
This command will update the repository that was created in the installation, install the PHP dependencies using `composer` then run the database migrations.

## Social Authentication

BookStack currently supports login via both Google and GitHub. Once enabled options for these services will show up in the login, registration and user profile pages. By default these services are disabled. To enable them you will have to create an application on the external services to obtain the require application id's and secrets. Here are instructions to do this for the current supported services:

### Google

1. Open the [Google Developers Console](https://console.developers.google.com/).
2. Create a new project (May have to wait a short while for it to be created).
3. Select 'Enable and manage APIs'.
4. Enable the 'Google+ API'.
5. In 'Credentials' choose the 'OAuth consent screen' tab and enter a product name ('BookStack' or your custom set name).
6. Back in the 'Credentials' tab click 'New credentials' > 'OAuth client ID'.
7. Choose an application type of 'Web application' and enter the following urls under 'Authorized redirect URIs', changing `https://example.com` to your own domain where BookStack is hosted:
    - `https://example.com/login/service/google/callback`
    - `https://example.com/register/service/google/callback`
8. Click 'Create' and your app_id and secret will be displayed. Replace the false value on both the `GOOGLE_APP_ID` & `GOOGLE_APP_SECRET` variables in the '.env' file in the BookStack root directory with your own app_id and secret.
9. Set the 'APP_URL' environment variable to be the same domain as you entered in step 7. So, in this example, it will be `https://example.com`.
10. All done! Users should now be able to link to their social accounts in their account profile pages and also register/login using their Google accounts.

### Github

1. While logged in, open up your [GitHub developer applications](https://github.com/settings/developers).
2. Click 'Register new application'.
3. Enter an application name ('BookStack' or your custom set name), A link to your app instance under 'Homepage URL' and an 'Authorization callback URL' of the url that your BookStack instance is hosted on then click 'Register application'.
4. A 'Client ID' and a 'Client Secret' value will be shown. Add these two values to the to the `GITHUB_APP_ID` and `GITHUB_APP_SECRET` variables, replacing the default false value, in the '.env' file found in the BookStack root folder.
5. Set the 'APP_URL' environment variable to be the same domain as you entered in step 3.
6. All done! Users should now be able to link to their social accounts in their account profile pages and also register/login using their Github account.

## LDAP Authentication

BookStack can be configured to allow LDAP based user login. While LDAP login is enabled you cannot log in with the standard user/password login and new user registration is disabled. BookStack will only use the LDAP server for getting user details and for authentication. Data on the LDAP server is not currently editable through BookStack.

When a LDAP user logs into BookStack for the first time their BookStack profile will be created and they will be given the default role set under the 'Default user role after registration' option in the application settings.    

To set up LDAP-based authentication add or modify the following variables in your `.env` file:

```
# General auth
AUTH_METHOD=ldap

# The LDAP host, Adding a port is optional
LDAP_SERVER=ldap://example.com:389

# The base DN from where users will be searched within.
LDAP_BASE_DN=ou=People,dc=example,dc=com

# The full DN and password of the user used to search the server
# Can both be left as false to bind anonymously
LDAP_DN=false
LDAP_PASS=false

# A filter to use when searching for users
# The user-provided user-name used to replace any occurrences of '${user}'
LDAP_USER_FILTER=(&(uid=${user}))

# Set the LDAP version to use when connecting to the server.
LDAP_VERSION=false
```

You will also need to have the php-ldap extension installed on your system. It's recommended to change your `APP_DEBUG` variable to `true` while setting up LDAP to make any errors visible. Remember to change this back after LDAP is functioning.

A user in BookStack will be linked to a LDAP user via a 'uid'. If a LDAP user uid changes it can be updated in BookStack by an admin by changing the 'External Authentication ID' field on the user's profile.

You may find that you cannot log in with your initial Admin account after changing the `AUTH_METHOD` to `ldap`. To get around this set the `AUTH_METHOD` to `standard`, login with your admin account then change it back to `ldap`. You get then edit your profile and add your LDAP uid under the 'External Authentication ID' field. You will then be able to login in with that ID.

## Development & Testing

All development on BookStack is currently done on the master branch. When it's time for a release the master branch is merged into release with built & minified CSS & JS then tagged at it's version. Here are the current development requirements:

* [Node.js](https://nodejs.org/en/) **Development Only**
* [Gulp](http://gulpjs.com/) **Development Only**

SASS is used to help the CSS development and the JavaScript is run through browserify/babel to allow for writing ES6 code. Both of these are done using gulp.

BookStack has many integration tests that use Laravel's built-in testing capabilities which makes use of PHPUnit. To use you will need PHPUnit installed and accessible via command line. There is a `mysql_testing` database defined within the app config which is what is used by PHPUnit. This database is set with the following database name, user name and password defined as `bookstack-test`. You will have to create that database and credentials before testing.

The testing database will also need migrating and seeding beforehand. This can be done with the following commands:

```
php artisan migrate --database=mysql_testing
php artisan db:seed --class=DummyContentSeeder --database=mysql_testing
```

Once done you can run `phpunit` (or `./vendor/bin/phpunit` if `phpunit` is not found) in the application root directory to run all tests.

## License

BookStack is provided under the MIT License.

## Attribution

These are the great projects used to help build BookStack:

* [Laravel](http://laravel.com/)
* [AngularJS](https://angularjs.org/)
* [jQuery](https://jquery.com/)
* [TinyMCE](https://www.tinymce.com/)
* [highlight.js](https://highlightjs.org/)
* [jQuery Sortable](https://johnny.github.io/jquery-sortable/)
* [Material Design Iconic Font](http://zavoloklom.github.io/material-design-iconic-font/icons.html)
* [Dropzone.js](http://www.dropzonejs.com/)
* [ZeroClipboard](http://zeroclipboard.org/)
