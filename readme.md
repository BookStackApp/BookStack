# BookStack

[![GitHub release](https://img.shields.io/github/release/BookStackApp/BookStack.svg)](https://github.com/BookStackApp/BookStack/releases/latest)
[![license](https://img.shields.io/badge/License-MIT-yellow.svg)](https://github.com/BookStackApp/BookStack/blob/master/LICENSE)
[![Crowdin](https://badges.crowdin.net/bookstack/localized.svg)](https://crowdin.com/project/bookstack)
[![Build Status](https://github.com/BookStackApp/BookStack/workflows/phpunit/badge.svg)](https://github.com/BookStackApp/BookStack/actions)
[![Discord](https://img.shields.io/static/v1?label=Chat&message=Discord&color=738adb&logo=discord)](https://discord.gg/ztkBqR2)

A platform for storing and organising information and documentation. Details for BookStack can be found on the official website at https://www.bookstackapp.com/.

* [Installation Instructions](https://www.bookstackapp.com/docs/admin/installation)
* [Documentation](https://www.bookstackapp.com/docs)
* [Demo Instance](https://demo.bookstackapp.com)
    * [Admin Login](https://demo.bookstackapp.com/login?email=admin@example.com&password=password)
* [BookStack Blog](https://www.bookstackapp.com/blog)

## 📚 Project Definition

BookStack is an opinionated wiki system that provides a pleasant and simple out of the box experience. New users to an instance should find the experience intuitive and only basic word-processing skills should be required to get involved in creating content on BookStack. The platform should provide advanced power features to those that desire it but they should not interfere with the core simple user experience.

BookStack is not designed as an extensible platform to be used for purposes that differ to the statement above.

In regards to development philosophy, BookStack has a relaxed, open & positive approach. At the end of the day this is free software developed and maintained by people donating their own free time.

## 🛣️ Road Map

Below is a high-level road map view for BookStack to provide a sense of direction of where the project is going. This can change at any point and does not reflect many features and improvements that will also be included as part of the journey along this road map. For more granular detail of what will be included in upcoming releases you can review the project milestones as defined in the "Release Process" section below.

- **Platform REST API** *(Base Implemented, In review and roll-out)*
    - *A REST API covering, at minimum, control of core content models (Books, Chapters, Pages) for automation and platform extension.*
- **Editor Alignment & Review**
    - *Review the page editors with goal of achieving increased interoperability & feature parity while also considering collaborative editing potential.*
- **Permission System Review**
    - *Improvement in how permissions are applied and a review of the efficiency of the permission & roles system.*
- **Installation & Deployment Process Revamp**
    - *Creation of a streamlined & secure process for users to deploy & update BookStack with reduced development requirements (No git or composer requirement).*

## 🚀 Release Versioning & Process

BookStack releases are each assigned a version number, such as "v0.25.2", in the format `v<phase>.<feature>.<patch>`. A change only in the `patch` number indicates a fairly minor release that mainly contains fixes and therefore is very unlikely to cause breakages upon update. A change in the `feature` number indicates a release which will generally bring new features in addition to fixes and enhancements. These releases have a small chance of introducing breaking changes upon update so it's worth checking for any notes in the [update guide](https://www.bookstackapp.com/docs/admin/updates/). A change in the `phase` indicates a much large change in BookStack that will likely incur breakages requiring manual intervention.

Each BookStack release will have a [milestone](https://github.com/BookStackApp/BookStack/milestones) created with issues & pull requests assigned to it to define what will be in that release. Milestones are built up then worked through until complete at which point, after some testing and documentation updates, the release will be deployed. 

For feature releases, and some patch releases, the release will be accompanied by a post on the [BookStack blog](https://www.bookstackapp.com/blog/) which will provide additional detail on features, changes & updates otherwise the [GitHub release page](https://github.com/BookStackApp/BookStack/releases) will show a list of changes. You can sign up to be alerted to new BookStack blogs posts (once per week maximum) [at this link](http://eepurl.com/cmmq5j).

## 🛠️ Development & Testing

All development on BookStack is currently done on the master branch. When it's time for a release the master branch is merged into release with built & minified CSS & JS then tagged at its version. Here are the current development requirements:

* [Node.js](https://nodejs.org/en/) v10.0+

SASS is used to help the CSS development and the JavaScript is run through babel to allow for writing ES6 code. This is done using webpack. To run the build task you can use the following commands:

``` bash
# Install NPM Dependencies
npm install

# Build assets for development
npm run build

# Build and minify assets for production
npm run production

# Build for dev (With sourcemaps) and watch for changes
npm run dev
```

BookStack has many integration tests that use Laravel's built-in testing capabilities which makes use of PHPUnit. There is a `mysql_testing` database defined within the app config which is what is used by PHPUnit. This database is set with the database name, user name and password all defined as `bookstack-test`. You will have to create that database and that set of credentials before testing.

The testing database will also need migrating and seeding beforehand. This can be done with the following commands:

``` bash
php artisan migrate --database=mysql_testing
php artisan db:seed --class=DummyContentSeeder --database=mysql_testing
```

Once done you can run `php vendor/bin/phpunit` in the application root directory to run all tests.

### 📜 Code Standards

PHP code within BookStack is generally to [PSR-2](http://www.php-fig.org/psr/psr-2/) standards. From the BookStack root folder you can run `./vendor/bin/phpcs` to check code is formatted correctly and `./vendor/bin/phpcbf` to auto-fix non-PSR-2 code.

### 🐋 Development using Docker

This repository ships with a Docker Compose configuration intended for development purposes. It'll build a PHP image with all needed extensions installed and start up a MySQL server and a Node image watching the UI assets.

To get started, make sure you meet the following requirements:

- Docker and Docker Compose are installed
- Your user is part of the `docker` group

If all the conditions are met, you can proceed with the following steps:

1. Install PHP/Composer dependencies with **`docker-compose run app composer install`** (first time can take a while because the image has to be built).
2. **Copy `.env.example` to `.env`** and change `APP_KEY` to a random 32 char string.
3. Make sure **port 8080 is unused** *or else* change `DEV_PORT` to a free port on your host.
4. **Run `chgrp -R docker storage`**. The development container will chown the `storage` directory to the `www-data` user inside the container so BookStack can write to it. You need to change the group to your host's `docker` group here to not lose access to the `storage` directory.
5. **Run `docker-compose up`** and wait until all database migrations have been done.
6. You can now login with `admin@admin.com` and `password` as password on `localhost:8080` (or another port if specified).

If needed, You'll be able to run any artisan commands via docker-compose like so:

 ```shell script
docker-compose run app php artisan list 
```

The docker-compose setup runs an instance of [MailHog](https://github.com/mailhog/MailHog) and sets environment variables to redirect any BookStack-sent emails to MailHog. You can view this mail via the MailHog web interface on `localhost:8025`. You can change the port MailHog is accessible on by setting a `DEV_MAIL_PORT` environment variable.

## 🌎 Translations

Translations for text within BookStack is managed through the [BookStack project on Crowdin](https://crowdin.com/project/bookstack). Some strings have colon-prefixed variables in such as `:userName`. Leave these values as they are as they will be replaced at run-time. Crowdin is the preferred way to provide translations, otherwise the raw translations files can be found within the `resources/lang` path.

If you'd like a new language to be added to Crowdin, for you to be able to provide translations for, please [open a new issue here](https://github.com/BookStackApp/BookStack/issues/new?template=language_request.md).

Please note, translations in BookStack are provided to the "Crowdin Global Translation Memory" which helps BookStack and other projects with finding translations. If you are not happy with contributing to this then providing translations to BookStack, even manually via GitHub, is not advised.

## 🎁 Contributing, Issues & Pull Requests

Feel free to create issues to request new features or to report bugs & problems. Just please follow the template given when creating the issue.

Pull requests are welcome. Unless a small tweak or language update, It may be best to open the pull request early or create an issue for your intended change to discuss how it will fit in to the project and plan out the merge. Pull requests should be created from the `master` branch since they will be merged back into `master` once done. Please do not build from or request a merge into the `release` branch as this is only for publishing releases. If you are looking to alter CSS or JavaScript content please edit the source files found in `resources/assets`. Any CSS or JS files within `public` are built from these source files and therefore should not be edited directly.

The project's code of conduct [can be found here](https://github.com/BookStackApp/BookStack/blob/master/.github/CODE_OF_CONDUCT.md).

## 🔒 Security

Security information for administering a BookStack instance can be found on the [documentation site here](https://www.bookstackapp.com/docs/admin/security/).

If you'd like to be notified of new potential security concerns you can [sign-up to the BookStack security mailing list](http://eepurl.com/glIh8z).

If you would like to report a security concern in a more confidential manner than via a GitHub issue, You can directly email the lead maintainer [ssddanbrown](https://github.com/ssddanbrown). You will need to login to be able to see the email address on the [GitHub profile page](https://github.com/ssddanbrown). Alternatively you can send a DM via twitter to [@ssddanbrown](https://twitter.com/ssddanbrown).

## ♿ Accessibility

We want BookStack to remain accessible to as many people as possible. We aim for at least WCAG 2.1 Level A standards where possible although we do not strictly test this upon each release. If you come across any accessibility issues please feel free to open an issue.

## 🖥️ Website, Docs & Blog

The website which contains the project docs & Blog can be found in the [BookStackApp/website](https://github.com/BookStackApp/website) repo.

## ⚖️ License

The BookStack source is provided under the MIT License. The libraries used by, and included with, BookStack are provided under their own licenses.

## 👪 Attribution

The great people that have worked to build and improve BookStack can [be seen here](https://github.com/BookStackApp/BookStack/graphs/contributors).

The wonderful people that have provided translations, either through GitHub or via Crowdin [can be seen here](https://github.com/BookStackApp/BookStack/blob/master/.github/translators.txt).

These are the great open-source projects used to help build BookStack:

* [Laravel](http://laravel.com/)
* [TinyMCE](https://www.tinymce.com/)
* [CodeMirror](https://codemirror.net)
* [Vue.js](http://vuejs.org/)
* [Sortable](https://github.com/SortableJS/Sortable) & [Vue.Draggable](https://github.com/SortableJS/Vue.Draggable)
* [Google Material Icons](https://material.io/icons/)
* [Dropzone.js](http://www.dropzonejs.com/)
* [clipboard.js](https://clipboardjs.com/)
* [markdown-it](https://github.com/markdown-it/markdown-it) and [markdown-it-task-lists](https://github.com/revin/markdown-it-task-lists)
* [BarryVD](https://github.com/barryvdh)
    * [Debugbar](https://github.com/barryvdh/laravel-debugbar)
    * [Dompdf](https://github.com/barryvdh/laravel-dompdf)
    * [Snappy (WKHTML2PDF)](https://github.com/barryvdh/laravel-snappy)
    * [Laravel IDE helper](https://github.com/barryvdh/laravel-ide-helper)
* [WKHTMLtoPDF](http://wkhtmltopdf.org/index.html)
* [Draw.io](https://github.com/jgraph/drawio)
* [Laravel Stats](https://github.com/stefanzweifel/laravel-stats)
* [OneLogin's SAML PHP Toolkit](https://github.com/onelogin/php-saml)