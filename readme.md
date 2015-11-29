# BookStack

A platform to create documentation/wiki content. General information about BookStack can be found at https://www.bookstackapp.com/

**BookStack is currently in rapid development so use now is heavily cautioned as future updates my break existing installations.**

## Requirements

BookStack has the similar requirements to Laravel. On top of those are some front-end build tools which the requirement of will be removed once out of beta release.

* PHP >= 5.5.9
* OpenSSL PHP Extension
* PDO PHP Extension
* Mbstring PHP Extension
* Tokenizer PHP Extension
* MySQL >= 5.6
* [Composer](https://getcomposer.org/)
* [Node.js](https://nodejs.org/en/) **To be removed in future**
* [Bower](http://bower.io/) **To be removed in future**
* [Gulp](http://gulpjs.com/) **To be removed in future**


## Installation

Ensure the requirements are met before installing.

The installation is currently somewhat complicated. Some PHP/Laravel experience will benefit. This will be streamlined in the future.

1. Clone the repository into a folder.
2. `cd` into folder and run `composer install` followed by `npm install` and `bower install`.
3. Run `gulp --production` to compile the JavaScript and css files.
4. Copy the `.env.example` file to `.env` and fill with your own database and mail details.
5. Ensure the `storage` & `bootstrap/cache` folders are writable by the web server.
5. In the application root, Run `php artisan key:generate` to generate a unique application key.
6. If not using apache or `.htaccess` files are disable you will have to create some  URL rewrite rules as shown below.
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
