# BookStack

[![GitHub release](https://img.shields.io/github/release/BookStackApp/BookStack.svg)](https://github.com/BookStackApp/BookStack/releases/latest)
[![license](https://img.shields.io/badge/License-MIT-yellow.svg)](https://github.com/BookStackApp/BookStack/blob/development/LICENSE)
[![Crowdin](https://badges.crowdin.net/bookstack/localized.svg)](https://crowdin.com/project/bookstack)
[![Build Status](https://github.com/BookStackApp/BookStack/workflows/test-php/badge.svg)](https://github.com/BookStackApp/BookStack/actions)
[![Lint Status](https://github.com/BookStackApp/BookStack/workflows/lint-php/badge.svg)](https://github.com/BookStackApp/BookStack/actions)
[![Maintainability](https://api.codeclimate.com/v1/badges/5551731994dd22fa1f4f/maintainability)](https://codeclimate.com/github/BookStackApp/BookStack/maintainability)

[![Repo Stats](https://img.shields.io/static/v1?label=GitHub+project&message=stats&color=f27e3f)](https://gh-stats.bookstackapp.com/)
[![Discord](https://img.shields.io/static/v1?label=Discord&message=chat&color=738adb&logo=discord)](https://discord.gg/ztkBqR2)
[![Twitter](https://img.shields.io/static/v1?label=Twitter&message=@bookstack_app&color=1d9bf0&logo=twitter)](https://twitter.com/bookstack_app)
[![YouTube](https://img.shields.io/static/v1?label=YouTube&message=bookstackapp&color=ff0000&logo=youtube)](https://www.youtube.com/bookstackapp)

A platform for storing and organising information and documentation. Details for BookStack can be found on the official website at https://www.bookstackapp.com/.

* [Installation Instructions](https://www.bookstackapp.com/docs/admin/installation)
* [Documentation](https://www.bookstackapp.com/docs)
* [Demo Instance](https://demo.bookstackapp.com)
    * [Admin Login](https://demo.bookstackapp.com/login?email=admin@example.com&password=password)
* [Screenshots](https://www.bookstackapp.com/#screenshots) 
* [BookStack Blog](https://www.bookstackapp.com/blog)
* [Issue List](https://github.com/BookStackApp/BookStack/issues)
* [Discord Chat](https://discord.gg/ztkBqR2)

## 📚 Project Definition

BookStack is an opinionated wiki system that provides a pleasant and simple out-of-the-box experience. New users to an instance should find the experience intuitive and only basic word-processing skills should be required to get involved in creating content on BookStack. The platform should provide advanced power features to those that desire it but they should not interfere with the core simple user experience.

BookStack is not designed as an extensible platform to be used for purposes that differ to the statement above.

In regard to development philosophy, BookStack has a relaxed, open & positive approach. At the end of the day this is free software developed and maintained by people donating their own free time.

## 🌟 Project Sponsors

Shown below are our bronze, silver and gold project sponsors.
Big thanks to these companies for supporting the project.
Note: Listed services are not tested, vetted nor supported by the official BookStack project in any manner.
[View all sponsors](https://github.com/sponsors/ssddanbrown).

#### Silver Sponsors

<table><tbody><tr>
<td><a href="https://www.diagrams.net/" target="_blank">
    <img width="400" src="https://media.githubusercontent.com/media/BookStackApp/website/main/static/images/sponsors/diagramsnet.png" alt="Diagrams.net">
</a></td>
<td><a href="https://cloudabove.com/hosting" target="_blank">
    <img height="100" src="https://raw.githubusercontent.com/BookStackApp/website/main/static/images/sponsors/cloudabove.svg" alt="Cloudabove">
</a></td>
</tr></tbody></table>

#### Bronze Sponsor

<table><tbody><tr>
<td><a href="https://www.stellarhosted.com/bookstack/" target="_blank">
    <img width="280" src="https://media.githubusercontent.com/media/BookStackApp/website/main/static/images/sponsors/stellarhosted.png" alt="Stellar Hosted">
</a></td>
</tr></tbody></table>

## 🛣️ Road Map

Below is a high-level road map view for BookStack to provide a sense of direction of where the project is going. This can change at any point and does not reflect many features and improvements that will also be included as part of the journey along this road map. For more granular detail of what will be included in upcoming releases you can review the project milestones as defined in the "Release Process" section below.

- **Platform REST API** - *(Most actions implemented, maturing)*
    - *A REST API covering, at minimum, control of core content models (Books, Chapters, Pages) for automation and platform extension.*
- **Editor Alignment & Review** - *(Done)*
    - *Review the page editors with the goal of achieving increased interoperability & feature parity while also considering collaborative editing potential.*
- **Permission System Review** - *(In Progress)*
    - *Improvement in how permissions are applied and a review of the efficiency of the permission & roles system.*
- **Installation & Deployment Process Revamp**
    - *Creation of a streamlined & secure process for users to deploy & update BookStack with reduced development requirements (No git or composer requirement).*

## 🚀 Release Versioning & Process

BookStack releases are each assigned a date-based version number in the format `v<year>.<month>[.<optional_patch_number>]`. For example:

- `v20.12` - New feature released launched during December 2020. 
- `v21.06.2` - Second patch release upon the June 2021 feature release.

Patch releases are generally fairly minor, primarily intended for fixes and therefore are fairly unlikely to cause breakages upon update.
Feature releases are generally larger, bringing new features in addition to fixes and enhancements. These releases have a greater chance of introducing breaking changes upon update, so it's worth checking for any notes in the [update guide](https://www.bookstackapp.com/docs/admin/updates/).

Each BookStack release will have a [milestone](https://github.com/BookStackApp/BookStack/milestones) created with issues & pull requests assigned to it to define what will be in that release. Milestones are built up then worked through until complete at which point, after some testing and documentation updates, the release will be deployed.

Feature releases, and some patch releases, will be accompanied by a post on the [BookStack blog](https://www.bookstackapp.com/blog/) which will provide additional detail on features, changes & updates otherwise the [GitHub release page](https://github.com/BookStackApp/BookStack/releases) will show a list of changes. You can sign up to be alerted to new BookStack blog posts (once per week maximum) [at this link](https://updates.bookstackapp.com/signup/bookstack-news-and-updates).

## 🛠️ Development & Testing

All development on BookStack is currently done on the `development` branch. When it's time for a release the `development` branch is merged into release with built & minified CSS & JS then tagged at its version. Here are the current development requirements:

* [Node.js](https://nodejs.org/en/) v14.0+

This project uses SASS for CSS development and this is built, along with the JavaScript, using a range of npm scripts. The below npm commands can be used to install the dependencies & run the build tasks:

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

Once done you can run `composer test` in the application root directory to run all tests.

### 📜 Code Standards

PHP code standards are managed by [using PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer).
Static analysis is in place using [PHPStan](https://phpstan.org/) & [Larastan](https://github.com/nunomaduro/larastan).
The below commands can be used to utilise these tools:

```bash
# Run code linting using PHP_CodeSniffer
composer lint

# As above, but show rule names in output
composer lint -- -s

# Auto-fix formatting & lint issues via PHP_CodeSniffer phpcbf
composer format

# Run static analysis via larastan/phpstan
composer check-static
```

If submitting a PR, formatting as per our project standards would help for clarity but don't worry too much about using/understanding these tools as we can always address issues at a later stage when they're picked up by our automated tools.

### 🐋 Development using Docker

This repository ships with a Docker Compose configuration intended for development purposes. It'll build a PHP image with all needed extensions installed and start up a MySQL server and a Node image watching the UI assets.

To get started, make sure you meet the following requirements:

- Docker and Docker Compose are installed
- Your user is part of the `docker` group

If all the conditions are met, you can proceed with the following steps:

1. **Copy `.env.example` to `.env`**, change `APP_KEY` to a random 32 char string and set `APP_ENV` to `local`.
2. Make sure **port 8080 is unused** *or else* change `DEV_PORT` to a free port on your host.
3. **Run `chgrp -R docker storage`**. The development container will chown the `storage` directory to the `www-data` user inside the container so BookStack can write to it. You need to change the group to your host's `docker` group here to not lose access to the `storage` directory.
4. **Run `docker-compose up`** and wait until the image is built and all database migrations have been done.
5. You can now login with `admin@admin.com` and `password` as password on `localhost:8080` (or another port if specified).

If needed, You'll be able to run any artisan commands via docker-compose like so:

```bash
docker-compose run app php artisan list
```

The docker-compose setup runs an instance of [MailHog](https://github.com/mailhog/MailHog) and sets environment variables to redirect any BookStack-sent emails to MailHog. You can view this mail via the MailHog web interface on `localhost:8025`. You can change the port MailHog is accessible on by setting a `DEV_MAIL_PORT` environment variable.

#### Running tests

After starting the general development Docker, migrate & seed the testing database:

 ```bash
# This only needs to be done once
docker-compose run app php artisan migrate --database=mysql_testing
docker-compose run app php artisan db:seed --class=DummyContentSeeder --database=mysql_testing
```

Once the database has been migrated & seeded, you can run the tests like so:

 ```bash
docker-compose run app php vendor/bin/phpunit
```

#### Debugging

The docker-compose setup ships with Xdebug, which you can listen to on port 9090.
NB : For some editors like Visual Studio Code, you might need to map your workspace folder to the /app folder within the docker container for this to work.

## 🌎 Translations

Translations for text within BookStack is managed through the [BookStack project on Crowdin](https://crowdin.com/project/bookstack). Some strings have colon-prefixed variables such as `:userName`. Leave these values as they are as they will be replaced at run-time. Crowdin is the preferred way to provide translations, otherwise the raw translations files can be found within the `resources/lang` path.

If you'd like a new language to be added to Crowdin, for you to be able to provide translations for, please [open a new issue here](https://github.com/BookStackApp/BookStack/issues/new?template=language_request.yml).

Please note, translations in BookStack are provided to the "Crowdin Global Translation Memory" which helps BookStack and other projects with finding translations. If you are not happy with contributing to this then providing translations to BookStack, even manually via GitHub, is not advised.

## 🎁 Contributing, Issues & Pull Requests

Feel free to create issues to request new features or to report bugs & problems. Just please follow the template given when creating the issue.

Pull requests are welcome. Unless a small tweak or language update, It may be best to open the pull request early or create an issue for your intended change to discuss how it will fit into the project and plan out the merge. Just because a feature request exists, or is tagged, does not mean that feature would be accepted into the core project.

Pull requests should be created from the `development` branch since they will be merged back into `development` once done. Please do not build from or request a merge into the `release` branch as this is only for publishing releases. If you are looking to alter CSS or JavaScript content please edit the source files found in `resources/`. Any CSS or JS files within `public` are built from these source files and therefore should not be edited directly.

The project's code of conduct [can be found here](https://github.com/BookStackApp/BookStack/blob/development/.github/CODE_OF_CONDUCT.md).

## 🔒 Security

Security information for administering a BookStack instance can be found on the [documentation site here](https://www.bookstackapp.com/docs/admin/security/).

If you'd like to be notified of new potential security concerns you can [sign-up to the BookStack security mailing list](https://updates.bookstackapp.com/signup/bookstack-security-updates).

If you would like to report a security concern, details of doing so can [can be found here](https://github.com/BookStackApp/BookStack/blob/development/.github/SECURITY.md).

## ♿ Accessibility

We want BookStack to remain accessible to as many people as possible. We aim for at least WCAG 2.1 Level A standards where possible although we do not strictly test this upon each release. If you come across any accessibility issues please feel free to open an issue.

## 🖥️ Website, Docs & Blog

The website which contains the project docs & Blog can be found in the [BookStackApp/website](https://github.com/BookStackApp/website) repo.

## ⚖️ License

The BookStack source is provided under the MIT License. 

The libraries used by, and included with, BookStack are provided under their own licenses and copyright.
The licenses for many of our core dependencies can be found in the attribution list below but this is not an exhaustive list of all projects used within BookStack. 

## 👪 Attribution

The great people that have worked to build and improve BookStack can [be seen here](https://github.com/BookStackApp/BookStack/graphs/contributors).

The wonderful people that have provided translations, either through GitHub or via Crowdin [can be seen here](https://github.com/BookStackApp/BookStack/blob/development/.github/translators.txt).

Below are the great open-source projects used to help build BookStack. 
Note: This is not an exhaustive list of all libraries and projects that would be used in an active BookStack instance.

* [Laravel](http://laravel.com/) - _[MIT](https://github.com/laravel/framework/blob/v8.82.0/LICENSE.md)_
* [TinyMCE](https://www.tinymce.com/) - _[MIT](https://github.com/tinymce/tinymce/blob/develop/LICENSE.TXT)_
* [CodeMirror](https://codemirror.net) - _[MIT](https://github.com/codemirror/CodeMirror/blob/master/LICENSE)_
* [Sortable](https://github.com/SortableJS/Sortable) - _[MIT](https://github.com/SortableJS/Sortable/blob/master/LICENSE)_
* [Google Material Icons](https://github.com/google/material-design-icons) - _[Apache-2.0](https://github.com/google/material-design-icons/blob/master/LICENSE)_
* [Dropzone.js](http://www.dropzonejs.com/) - _[MIT](https://github.com/dropzone/dropzone/blob/main/LICENSE)_
* [clipboard.js](https://clipboardjs.com/) - _[MIT](https://github.com/zenorocha/clipboard.js/blob/master/LICENSE)_
* [markdown-it](https://github.com/markdown-it/markdown-it) and [markdown-it-task-lists](https://github.com/revin/markdown-it-task-lists) - _[MIT](https://github.com/markdown-it/markdown-it/blob/master/LICENSE) and [ISC](https://github.com/revin/markdown-it-task-lists/blob/master/LICENSE)_
* [Dompdf](https://github.com/dompdf/dompdf) - _[LGPL v2.1](https://github.com/dompdf/dompdf/blob/master/LICENSE.LGPL)_
* [BarryVD/Dompdf](https://github.com/barryvdh/laravel-dompdf) - _[MIT](https://github.com/barryvdh/laravel-dompdf/blob/master/LICENSE)_
* [BarryVD/Snappy (WKHTML2PDF)](https://github.com/barryvdh/laravel-snappy) - _[MIT](https://github.com/barryvdh/laravel-snappy/blob/master/LICENSE)_
* [WKHTMLtoPDF](http://wkhtmltopdf.org/index.html) - _[LGPL v3.0](https://github.com/wkhtmltopdf/wkhtmltopdf/blob/master/LICENSE)_
* [diagrams.net](https://github.com/jgraph/drawio) - _[Embedded Version Terms](https://www.diagrams.net/trust/) / [Source Project - Apache-2.0](https://github.com/jgraph/drawio/blob/dev/LICENSE)_
* [OneLogin's SAML PHP Toolkit](https://github.com/onelogin/php-saml) - _[MIT](https://github.com/onelogin/php-saml/blob/master/LICENSE)_
* [League/CommonMark](https://commonmark.thephpleague.com/) - _[BSD-3-Clause](https://github.com/thephpleague/commonmark/blob/2.2/LICENSE)_
* [League/Flysystem](https://flysystem.thephpleague.com) - _[MIT](https://github.com/thephpleague/flysystem/blob/3.x/LICENSE)_
* [pragmarx/google2fa](https://github.com/antonioribeiro/google2fa) - _[MIT](https://github.com/antonioribeiro/google2fa/blob/8.x/LICENSE.md)_
* [Bacon/BaconQrCode](https://github.com/Bacon/BaconQrCode) - _[BSD-2-Clause](https://github.com/Bacon/BaconQrCode/blob/master/LICENSE)_
* [phpseclib](https://github.com/phpseclib/phpseclib) - _[MIT](https://github.com/phpseclib/phpseclib/blob/master/LICENSE)_
* [Clockwork](https://github.com/itsgoingd/clockwork) - _[MIT](https://github.com/itsgoingd/clockwork/blob/master/LICENSE)_
* [PHPStan](https://phpstan.org/) & [Larastan](https://github.com/nunomaduro/larastan) - _[MIT](https://github.com/phpstan/phpstan/blob/master/LICENSE) and [MIT](https://github.com/nunomaduro/larastan/blob/master/LICENSE.md)_
* [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) - _[BSD 3-Clause](https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt)_
