# BookStack

A platform to create documentation/wiki content. General information about BookStack can be found at https://www.bookstackapp.com/


## Requirements

BookStack has similar requirements to Laravel. On top of those are some front-end build tools which are only required when developing.

* PHP >= 5.5.9
* OpenSSL PHP Extension
* PDO PHP Extension
* MBstring PHP Extension
* Tokenizer PHP Extension
* MySQL >= 5.6
* Git (Not strictly required but helps manage updates)
* [Composer](https://getcomposer.org/)
* [Node.js](https://nodejs.org/en/) **Development Only**
* [Gulp](http://gulpjs.com/) **Development Only**


## Installation

Ensure the requirements are met before installing.

This project currently uses the `release` branch of this repository as a stable channel for providing updates.

The installation is currently somewhat complicated and will be made simpler in future releases. Some PHP/Laravel experience will currently benefit.

1. Clone the release branch of this repository into a folder.

```
git clone https://github.com/ssddanbrown/BookStack.git --branch release --single-branch
```

2. `cd` into the application folder and run `composer install`.
3. Copy the `.env.example` file to `.env` and fill with your own database and mail details.
4. Ensure the `storage` & `bootstrap/cache` folders are writable by the web server.
5. In the application root, Run `php artisan key:generate` to generate a unique application key.
6. If not using apache or if `.htaccess` files are disabled you will have to create some URL rewrite rules as shown below.
7. Run `php migrate` to update the database.
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

## Testing

BookStack has many integration tests that use Laravel's built-in testing capabilities which makes use of PHPUnit. To use you will need PHPUnit installed and accessible via command line. There is a `mysql_testing` database defined within the app config which is what is used by PHPUnit. This database is set with the following database name, user name and password defined as `bookstack-test`. You will have to create that database and credentials before testing.

The testing database will also need migrating and seeding beforehand. This can be done with the following commands:

```
php artisan migrate --database=mysql_testing
php artisan db:seed --class=DummyContentSeeder --database=mysql_testing
```

Once done you can run `phpunit` in the application root directory to run all tests.

## License

BookStack is provided under the MIT License.

## Attribution

These are the great projects used to help build BookStack:

* [Laravel](http://laravel.com/)
* [VueJS](http://vuejs.org/)
* [jQuery](https://jquery.com/)
* [TinyMCE](https://www.tinymce.com/)
* [highlight.js](https://highlightjs.org/)
* [jQuery Sortable](https://johnny.github.io/jquery-sortable/)
* [Material Design Iconic Font](http://zavoloklom.github.io/material-design-iconic-font/icons.html)
* [Dropzone.js](http://www.dropzonejs.com/)
* [ZeroClipboard](http://zeroclipboard.org/)
