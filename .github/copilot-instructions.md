# BookStack Development Guide

## Architecture Overview

BookStack is a Laravel 12-based documentation platform with a traditional MVC structure. The codebase uses:
- **Backend**: PHP 8.2+ with Laravel 12, namespace `BookStack\`
- **Frontend**: TypeScript/JavaScript with component-based architecture, SASS for styles
- **Database**: MySQL with Eloquent ORM

### Key Directory Structure

- `app/` - Core application organized by domain (Access, Activity, Entities, Permissions, Users, etc.)
  - `Models/` subdirectories contain Eloquent models
  - `Repos/` subdirectories contain repository pattern implementations
  - `Controllers/` subdirectories contain HTTP and API controllers
  - Service classes (e.g., `LoginService`, `LdapService`) handle business logic
- `resources/js/` - TypeScript/JavaScript frontend code using component system
- `resources/sass/` - SASS stylesheets
- `resources/views/` - Blade templates
- `routes/` - `web.php` (authenticated UI routes) and `api.php` (REST API routes)
- `tests/` - PHPUnit tests mirroring `app/` structure

### Core Patterns

**Entities Hierarchy**: The platform uses a hierarchical content structure:
- `Bookshelf` → `Book` → `Chapter` → `Page`
- Models in `app/Entities/Models/` extend `Entity` or specialized base classes (`BookChild`)
- Use `scopeVisible()` on queries to enforce permission filtering

**Repository Pattern**: Business logic lives in repository classes (e.g., `BookRepo`, `PageRepo`) in `*Repos/` directories. These handle CRUD operations, not controllers directly.

**Permission System**: Complex permission handling via:
- `PermissionApplicator` - Apply permission filters to queries
- `userCan($permission, $ownable)` helper function in `app/App/helpers.php`
- Check permissions using `Permission` class constants, not string literals
- Joint permissions table caches permission evaluation for performance

**Activity Tracking**: Use `Activity::add(ActivityType::*, $entity)` facade for audit logging, not direct database calls.

**Frontend Components**: 
- Component-based system in `resources/js/components/`
- Register components via HTML attributes: `component="component-name"`
- Reference elements with `refs="component-name@refName"` 
- Component options via `option:component-name:option-key="value"`
- Components extend `Component` base class from `component.ts`

## Development Workflows

### Build Commands

```bash
# PHP dependencies
composer install

# JavaScript/CSS development (watch mode)
npm run dev              # Watches both JS and CSS
npm run build:js:watch   # JS only
npm run build:css:watch  # CSS only

# Production builds
npm run production       # Minified JS and CSS

# Linting and testing
composer lint            # PHP CodeSniffer
composer format          # Auto-fix PHP formatting
composer check-static    # PHPStan static analysis
composer test            # PHPUnit tests
npm run lint             # ESLint
npm run test             # Jest tests
```

### Testing

- PHPUnit configuration in `phpunit.xml` with extensive test environment variables
- Tests use `DatabaseTransactions` trait for automatic rollback
- Test helpers: `EntityProvider`, `UserRoleProvider`, `PermissionsProvider` available via `$this->entities`, `$this->users`, `$this->permissions`
- Factory-based test data creation via `database/factories/`

### Database Migrations

```bash
php artisan migrate                    # Run migrations
php artisan migrate:refresh            # Reset and re-run
php artisan db:seed --class=DummyContentSeeder  # Seed test content
composer refresh-test-database         # Refresh test DB with seeding
```

## Conventions

**Naming**:
- Controllers: `*Controller` for web, `*ApiController` for API endpoints
- Services: `*Service` suffix (e.g., `LoginService`, `EmailConfirmationService`)
- Repositories: `*Repo` suffix
- Use explicit imports, avoid aliases except for established facades

**Routing**:
- Web routes require `auth` middleware (see `routes/web.php`)
- API routes follow RESTful conventions (list, create, read, update, delete)
- Controllers are namespaced by domain, imported via `as` aliases at route file top

**Eloquent Relationships**:
- Always define inverse relationships
- Use lazy-loading protection (check `Model::preventLazyLoading()` in `AppServiceProvider`)
- Leverage query scopes for common filters (e.g., `scopeVisible()` for permissions)

**Frontend**:
- Use TypeScript for new code where possible
- Avoid jQuery - use vanilla DOM APIs or existing framework utilities
- Translations via `window.$trans.get('key')` or `trans('key')` helper in Blade
- HTTP requests via `window.$http` service, not raw fetch/axios

## External Integrations

- **Authentication**: Supports LDAP, SAML2, OAuth2 (via Socialite), and standard email/password
  - Auth services in `app/Access/` (e.g., `LdapService`, `Saml2Service`, `SocialAuthService`)
- **Storage**: Configurable via Laravel filesystems (local, S3) for images/attachments
- **Exports**: PDF generation via wkhtmltopdf (knplabs/snappy) or dompdf
- **Editor**: TinyMCE and custom Markdown editor with CodeMirror integration

## Common Gotchas

- Don't bypass the permission system - always use `scopeVisible()` or `userCan()` checks
- Database transactions for multi-step operations use `DatabaseTransaction` helper class
- Use `Activity::add()` for audit events, not manual logging
- Frontend component initialization is automatic via `window.$components.init()` - don't manually instantiate
- Helpers in `app/App/helpers.php` are autoloaded - use `user()`, `userCan()`, `setting()`, etc.
