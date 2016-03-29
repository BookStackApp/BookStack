# BookStack

A platform for storing and organising information and documentation. General information and documentation for BookStack can be found at https://www.bookstackapp.com/.

* [Installation Instructions](https://www.bookstackapp.com/docs/admin/installation)
* [Documentation](https://www.bookstackapp.com/docs)
* [Demo Instance](https://demo.bookstackapp.com) *(Login username: `admin@example.com`. Password: `password`)*
* [BookStack Blog](https://www.bookstackapp.com/blog)

## Development & Testing

All development on BookStack is currently done on the master branch. When it's time for a release the master branch is merged into release with built & minified CSS & JS then tagged at it's version. Here are the current development requirements:

* [Node.js](https://nodejs.org/en/)
* [Gulp](http://gulpjs.com/)

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
* [TinyColorPicker](http://www.dematte.at/tinyColorPicker/index.html)
* [Marked](https://github.com/chjj/marked)
