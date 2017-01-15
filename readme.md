# BookStack

[![GitHub release](https://img.shields.io/github/release/ssddanbrown/BookStack.svg?maxAge=2592000)](https://github.com/ssddanbrown/BookStack/releases/latest)
[![license](https://img.shields.io/github/license/ssddanbrown/BookStack.svg?maxAge=2592000)](https://github.com/ssddanbrown/BookStack/blob/master/LICENSE)
[![Build Status](https://travis-ci.org/BookStackApp/BookStack.svg)](https://travis-ci.org/BookStackApp/BookStack)

A platform for storing and organising information and documentation. General information and documentation for BookStack can be found at https://www.bookstackapp.com/.

* [Installation Instructions](https://www.bookstackapp.com/docs/admin/installation)
* [Documentation](https://www.bookstackapp.com/docs)
* [Demo Instance](https://demo.bookstackapp.com)
  * *Username: `admin@example.com`*
  * *Password: `password`*
* [BookStack Blog](https://www.bookstackapp.com/blog)

## Development & Testing

All development on BookStack is currently done on the master branch. When it's time for a release the master branch is merged into release with built & minified CSS & JS then tagged at it's version. Here are the current development requirements:

* [Node.js](https://nodejs.org/en/) v6.9+

SASS is used to help the CSS development and the JavaScript is run through browserify/babel to allow for writing ES6 code. Both of these are done using gulp. To run the build task you can use the following commands:

``` bash
# Build and minify for production
npm run-script build

# Build for dev (With sourcemaps) and watch for changes
npm run-script dev
```

BookStack has many integration tests that use Laravel's built-in testing capabilities which makes use of PHPUnit. To use you will need PHPUnit installed and accessible via command line. There is a `mysql_testing` database defined within the app config which is what is used by PHPUnit. This database is set with the following database name, user name and password defined as `bookstack-test`. You will have to create that database and credentials before testing.

The testing database will also need migrating and seeding beforehand. This can be done with the following commands:

``` bash
php artisan migrate --database=mysql_testing
php artisan db:seed --class=DummyContentSeeder --database=mysql_testing
```

Once done you can run `phpunit` in the application root directory to run all tests.

## Translations

As part of BookStack v0.14 support for translations has been built in. All text strings can be found in the `resources/lang` folder where each language option has its own folder. To add a new language you should copy the `en` folder to an new folder (eg. `fr` for french) then go through and translate all text strings in those files, leaving the keys and file-names intact. If a language string is missing then the `en` translation will be used. To show the language option in the user preferences language drop-down you will need to add your language to the options found at the bottom of the `resources/lang/en/settings.php` file. A system-wide language can also be set in the `.env` file like so: `APP_LANG=en`.
 
 Some strings have colon-prefixed variables in such as `:userName`. Leave these values as they are as they will be replaced at run-time.

## Website, Docs & Blog 

The website project docs & Blog can be found in the [BookStackApp/website](https://github.com/BookStackApp/website) repo.

## License

The BookStack source is provided under the MIT License.

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
* [Moment.js](http://momentjs.com/)
* [BarryVD](https://github.com/barryvdh)
    * [Debugbar](https://github.com/barryvdh/laravel-debugbar)
    * [Dompdf](https://github.com/barryvdh/laravel-dompdf)
    * [Snappy (WKHTML2PDF)](https://github.com/barryvdh/laravel-snappy)
    * [Laravel IDE helper](https://github.com/barryvdh/laravel-ide-helper)
* [WKHTMLtoPDF](http://wkhtmltopdf.org/index.html)

Additionally, Thank you [BrowserStack](https://www.browserstack.com/) for supporting us and making cross-browser testing easy.
