# BookStack

[![GitHub release](https://img.shields.io/github/release/BookStackApp/BookStack.svg)](https://github.com/BookStackApp/BookStack/releases/latest)
[![license](https://img.shields.io/github/license/BookStackApp/BookStack.svg)](https://github.com/BookStackApp/BookStack/blob/master/LICENSE)
[![Build Status](https://travis-ci.org/BookStackApp/BookStack.svg)](https://travis-ci.org/BookStackApp/BookStack)

A platform for storing and organising information and documentation. General information and documentation for BookStack can be found at https://www.bookstackapp.com/.

* [Installation Instructions](https://www.bookstackapp.com/docs/admin/installation)
* [Documentation](https://www.bookstackapp.com/docs)
* [Demo Instance](https://demo.bookstackapp.com)
  * *Username: `admin@example.com`*
  * *Password: `password`*
* [BookStack Blog](https://www.bookstackapp.com/blog)

## Project Definition

BookStack is an opinionated wiki system that provides a pleasant and simple out of the box experience. New users to an instance should find the experience intuitive and only basic word-processing skills should be required to get involved in creating content on BookStack. The platform should provide advanced power features to those that desire it but they should not interfere with the core simple user experience.

BookStack is not designed as an extensible platform to be used for purposes that differ to the statement above. 

## Development & Testing

All development on BookStack is currently done on the master branch. When it's time for a release the master branch is merged into release with built & minified CSS & JS then tagged at it's version. Here are the current development requirements:

* [Node.js](https://nodejs.org/en/) v6.9+

SASS is used to help the CSS development and the JavaScript is run through browserify/babel to allow for writing ES6 code. Both of these are done using gulp. To run the build task you can use the following commands:

``` bash
# Build assets for development
npm run-script build

# Build and minify assets for production
npm run-script production

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

You will also need to add the language to the `locales` array in the `config/app.php` file.

There is a script available which compares translation content to `en` files to see what items are missing or redundant. This can be ran like so from your BookStack install folder:

```bash
# Syntax
php resources/lang/check.php <lang>

# Examples
php resources/lang/check.php fr
php resources/lang/check.php pt_BR
```
 
Some strings have colon-prefixed variables in such as `:userName`. Leave these values as they are as they will be replaced at run-time.
 
## Contributing

Feel free to create issues to request new features or to report bugs and problems. Just please follow the template given when creating the issue.

### Standards

PHP code within BookStack is generally to [PSR-2](http://www.php-fig.org/psr/psr-2/) standards. From the BookStack root folder you can run `./vendor/bin/phpcs` to check code is formatted correctly and `./vendor/bin/phpcbf` to auto-fix non-PSR-2 code.

### Pull Requests

Pull requests are very welcome. If the scope of your pull request is large it may be best to open the pull request early or create an issue for it to discuss how it will fit in to the project and plan out the merge.

Pull requests should be created from the `master` branch and should be merged back into `master` once done. Please do not build from or request a merge into the `release` branch as this is only for publishing releases.

If you are looking to alter CSS or JavaScript content please edit the source files found in `resources/assets`. Any CSS or JS files within `public` are built from these source files and therefore should not be edited directly. 

## Website, Docs & Blog 

The website project docs & Blog can be found in the [BookStackApp/website](https://github.com/BookStackApp/website) repo.

## License

The BookStack source is provided under the MIT License.

## Attribution

These are the great open-source projects used to help build BookStack:

* [Laravel](http://laravel.com/)
* [jQuery](https://jquery.com/)
* [TinyMCE](https://www.tinymce.com/)
* [CodeMirror](https://codemirror.net)
* [Vue.js](http://vuejs.org/)
* [Axios](https://github.com/mzabriskie/axios)
* [jQuery Sortable](https://johnny.github.io/jquery-sortable/)
* [Material Design Iconic Font](http://zavoloklom.github.io/material-design-iconic-font/icons.html)
* [Dropzone.js](http://www.dropzonejs.com/)
* [clipboard.js](https://clipboardjs.com/)
* [TinyColorPicker](http://www.dematte.at/tinyColorPicker/index.html)
* [markdown-it](https://github.com/markdown-it/markdown-it) and [markdown-it-task-lists](https://github.com/revin/markdown-it-task-lists)
* [Moment.js](http://momentjs.com/)
* [BarryVD](https://github.com/barryvdh)
    * [Debugbar](https://github.com/barryvdh/laravel-debugbar)
    * [Dompdf](https://github.com/barryvdh/laravel-dompdf)
    * [Snappy (WKHTML2PDF)](https://github.com/barryvdh/laravel-snappy)
    * [Laravel IDE helper](https://github.com/barryvdh/laravel-ide-helper)
* [WKHTMLtoPDF](http://wkhtmltopdf.org/index.html)
* [Draw.io](https://github.com/jgraph/drawio)
