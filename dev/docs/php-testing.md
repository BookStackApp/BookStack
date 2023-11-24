# BookStack PHP Testing

BookStack has many test cases defined within the `tests/` directory of the app. These are built upon [PHPUnit](https://phpunit.de/) along with Laravel's own test framework additions, and a bunch of custom helper classes.

## Setup

The application tests are mostly functional, rather than unit tests, meaning they simulate user actions and system components and therefore these require use of the database. To avoid potential conflicts within your development environment, the tests use a separate database. This is defined via a specific `mysql_testing` database connection in our configuration, and expects to use the following database access details:

- Host: `127.0.0.1`
- Username: `bookstack-test`
- Password: `bookstack-test`
- Database: `bookstack-test`

You will need to create a database, with access for these credentials, to allow the system to connect when running tests. Alternatively, if those don't suit, you can define a `TEST_DATABASE_URL` option in your `.env` file, or environment, with connection details like so:

```bash
TEST_DATABASE_URL="mysql://username:password@host-name:port/database-name"
```

The testing database will need migrating and seeding with test data beforehand. This can be done by running `composer refresh-test-database`.

## Running Tests

You can run all tests via composer with `composer test` in the application root directory.
Alternatively, you can run PHPUnit directly with `php vendor/bin/phpunit`.

Some editors, like PHPStorm, have in-built support for running tests on a per file, directory or class basis.
Otherwise, you can run PHPUnit with specified tests and/or filter to limit the tests ran:

```bash
# Run all test in the "./tests/HomepageTest.php" file
php vendor/bin/phpunit ./tests/HomepageTest.php

# Run all test in the "./tests/User" directory
php vendor/bin/phpunit ./tests/User

# Filter to a particular test method name
php vendor/bin/phpunit --filter test_default_homepage_visible

# Filter to a particular test class name
php vendor/bin/phpunit --filter HomepageTest
```

If the codebase needs to be tested with deprecations, this can be done via uncommenting the relevant line within the `TestCase@setUp` function.  This is not expected for most PRs to the project, but instead used for maintenance tasks like dependency & PHP upgrades.

## Writing Tests

To understand how tests are written & used, it's advised you read through existing test cases similar to what you need to write. Tests are written in a rather scrappy manner, compared to the core app codebase, which is fine and expected since there's often hoops to jump through for various functionality. Scrappy tests are better than no tests.

Test classes have to be within the `tests/` folder, and be named ending in `Test`. These should always extend the `Tests\TestCase` class.
Test methods should be written in snake_case, start with `test_`, and be public methods.

Here are some general rules & patterns we follow in the tests:

- All external remote system resources, like HTTP calls and LDAP connections, are mocked.
- We prefer to hard-code expected text & URLs to better detect potential changes in the system rather than use dynamic references. This provides higher sensitivity to changes, and has never been much of a maintenance issue.
- Only test with an admin user if needed, otherwise keep to less privileged users to ensure permission systems are active and exercised within tests.
- If testing for the lack of something (e.g. `$this->assertDontSee('TextAfterChange')`) then this should be accompanied by some form of positive confirmation (e.g. `$this->assertSee('TextBeforeChange')`).

### Test Helpers

Our default `TestCase` is bloated with helpers to assist in testing scenarios. Some of these shown below, but you should jump through and explore these in your IDE/editor to explore their full capabilities and options:

```php
// Run the test as a logged-in-user at a certain privilege level
$this->asAdmin();
$this->asEditor();
$this->asViewer();

// Provides a bunch of entity (shelf/book/chapter/page) content and actions 
$this->entities;

// Provides various user & role abilities
$this->users;

// Provides many helpful actions relate to system & content permissions
$this->permissions;

// Provides a range of methods for dealing with files & uploads in tests
$this->files;

// Parse HTML of a response to assert HTML-based conditions
// Uses https://github.com/ssddanbrown/asserthtml library.
$this->withHtml($resp);
// Example:
$this->withHtml($this->get('/'))->assertElementContains('p[id="top"]', 'Hello!');
```