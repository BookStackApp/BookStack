# System Architecture

> BookStack architecture, design patterns, and code organization

---

## 📖 Chapter 1: Overview & Principles

### Architecture Philosophy

BookStack follows a **traditional MVC architecture** built on Laravel 12, emphasizing:

- **Convention over configuration**
- **Separation of concerns**
- **Domain-driven organization**
- **Repository pattern** for data access
- **Service layer** for business logic

### Design Principles

1. **Simplicity First**: Easy to understand and maintain
2. **Progressive Enhancement**: Advanced features don't complicate basic use
3. **Security by Default**: Permission system integrated at core
4. **Extensibility within Bounds**: Logical extension points without full plugin architecture
5. **Performance Aware**: Caching, eager loading, and optimization

---

## 📖 Chapter 2: Directory Structure

### Root Structure

```
BookStack/
├── app/                    # Application code (by domain)
├── bootstrap/              # Framework bootstrap
├── config/                 # Configuration files
├── database/               # Migrations, seeders, factories
├── lang/                   # Translations (50+ languages)
├── public/                 # Web root, compiled assets
├── resources/              # Views, raw assets, JS, SASS
├── routes/                 # Route definitions
├── storage/                # Logs, cache, uploads
├── tests/                  # PHPUnit tests
└── themes/                 # Custom theme directory
```

### App Directory Organization

```
app/
├── Access/                 # Authentication & authorization
│   ├── Controllers/       # Login, registration, MFA
│   ├── Ldap.php           # LDAP integration
│   ├── Saml2Service.php   # SAML authentication
│   └── SocialAuthService.php  # OAuth providers
├── Activity/              # Audit logging & webhooks
├── Api/                   # REST API
├── Entities/              # Core content models
│   ├── Models/           # Eloquent models
│   ├── Repos/            # Repository pattern
│   └── Controllers/      # Content controllers
├── Permissions/           # Permission system
├── Search/                # Search functionality
├── Settings/              # System settings
├── Theming/               # Theme system
├── Uploads/               # File upload handling
├── Users/                 # User management
└── Util/                  # Utilities
```

---

## 📖 Chapter 3: Core Patterns

### MVC Pattern

**Models** (`app/Entities/Models/`)
```php
namespace BookStack\Entities\Models;

class Page extends Entity
{
    protected $fillable = ['name', 'html', 'markdown'];
    
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
```

**Controllers** (`app/Entities/Controllers/`)
```php
namespace BookStack\Entities\Controllers;

class PageController extends Controller
{
    public function __construct(
        protected PageRepo $pageRepo
    ) {}
    
    public function show(string $bookSlug, string $pageSlug)
    {
        $page = $this->pageRepo->getBySlug($bookSlug, $pageSlug);
        return view('pages.show', ['page' => $page]);
    }
}
```

**Views** (`resources/views/`)
```blade
{{-- pages/show.blade.php --}}
@extends('layouts.simple')

@section('content')
    <div class="page-content">
        {!! $page->html !!}
    </div>
@endsection
```

### Repository Pattern

Repositories encapsulate data access logic:

```php
// app/Entities/Repos/PageRepo.php
class PageRepo
{
    public function getBySlug(string $bookSlug, string $pageSlug): Page
    {
        return Page::visible()
            ->whereHas('book', function($query) use ($bookSlug) {
                $query->where('slug', '=', $bookSlug);
            })
            ->where('slug', '=', $pageSlug)
            ->firstOrFail();
    }
}
```

**Benefits:**
- Centralized query logic
- Reusable across controllers
- Easier testing
- Permission integration

### Service Layer

Services handle complex business logic:

```php
// app/Access/LoginService.php
class LoginService
{
    public function __construct(
        protected EmailConfirmationService $emailConfirmation,
        protected UserRepo $userRepo,
        protected ThemeEvents $themeEvents
    ) {}
    
    public function login(User $user, string $method): void
    {
        auth()->login($user);
        Activity::add(ActivityType::AUTH_LOGIN, $user);
        $this->themeEvents->dispatch(ThemeEvents::AUTH_LOGIN, $method, $user);
    }
}
```

### Entity Hierarchy

BookStack uses a hierarchical content model:

```
Bookshelf (collection of books)
    ↓
Book (top-level container)
    ↓
Chapter (organizational unit within book)
    ↓
Page (actual content)
```

**Database Implementation (v24+):**
```sql
-- Unified entities table
entities (
    id, type, name, slug,
    book_id, chapter_id,
    created_at, updated_at
)

-- Page-specific data
entity_page_data (
    page_id, html, text, markdown,
    editor, draft, template
)

-- Book/Chapter descriptions
entity_container_data (
    entity_id, description
)
```

### Permission System

Multi-layered permission checking:

1. **Joint Permissions Table** (cached evaluation)
2. **Role Permissions** (assigned to roles)
3. **Entity Permissions** (override per-item)
4. **Ownership** (creator has special rights)

```php
// Check permission
if (userCan('page-view', $page)) {
    // User has access
}

// Query with permissions
$pages = Page::visible()->get();  // Only returns viewable pages

// Apply permissions to query
$query = DB::table('pages');
$permissionApplicator->restrictPageQuery($query);
```

**Permission Types:**
- `{entity}-view` - View content
- `{entity}-create` - Create new
- `{entity}-update` - Edit existing
- `{entity}-delete` - Delete content
- `restrictions-manage` - Manage permissions
- `settings-manage` - System settings
- `users-manage` - User administration

---

## 📖 Chapter 4: Frontend Architecture

### Component System

JavaScript components use a custom lightweight framework:

```javascript
// resources/js/components/example.ts
import {Component} from './component';

export class ExampleComponent extends Component {
    setup() {
        this.container = this.$el;
        this.button = this.$refs.button;
        this.button.addEventListener('click', this.onClick.bind(this));
    }
    
    onClick() {
        console.log('Button clicked');
    }
}
```

**HTML Integration:**
```html
<div component="example">
    <button refs="example@button">Click Me</button>
</div>
```

**Component Options:**
```html
<div component="dropdown"
     option:dropdown:bubble-escapes="true"
     option:dropdown:direction="down">
```

### Asset Pipeline

**Development:**
```bash
npm run dev          # Watch both JS and CSS
npm run build:js     # Build JavaScript once
npm run build:css    # Build CSS once
```

**Production:**
```bash
npm run production   # Minified, optimized bundles
```

**Output:**
```
public/dist/
├── app.js          # Core application (196 KB)
├── code.js         # Code editor (655 KB)
├── wysiwyg.js      # WYSIWYG editor (309 KB)
├── markdown.js     # Markdown editor (182 KB)
├── styles.css      # All styles (minified)
└── exports/        # PDF/export libraries
```

### SASS Structure

```
resources/sass/
├── styles.scss         # Main entry point
├── _variables.scss     # Color, spacing, etc.
├── _mixins.scss        # Reusable patterns
├── _text.scss          # Typography
├── _colors.scss        # Color system
├── _layout.scss        # Page structure
├── _blocks.scss        # Content blocks
├── _forms.scss         # Form styling
└── _components.scss    # UI components
```

---

## 📖 Chapter 5: Database Schema

### Unified Entity Model (v24+)

**Primary Table:**
```sql
entities
├── id              # Unique identifier
├── type            # 'book', 'chapter', 'page', 'bookshelf'
├── name            # Display name
├── slug            # URL-friendly identifier
├── book_id         # Parent book (for chapters/pages)
├── chapter_id      # Parent chapter (for pages)
├── priority        # Sort order
├── created_at
├── updated_at
├── deleted_at      # Soft deletes
├── created_by      # User ID
├── updated_by      # User ID
└── owned_by        # Owner user ID
```

**Related Tables:**
```sql
entity_page_data
├── page_id         # FK to entities.id
├── html            # Rendered HTML
├── text            # Plain text (for search)
├── markdown        # Raw markdown (if used)
├── editor          # 'wysiwyg' or 'markdown'
├── draft           # Draft flag
├── template        # Template flag
└── revision_count  # Number of revisions

entity_container_data
├── entity_id       # FK to entities.id (book/chapter)
└── description     # Markdown description

joint_permissions
├── entity_id       # FK to entities.id
├── entity_type     # Class name
├── role_id         # FK to roles.id
├── action          # view, create, update, delete
└── has_permission  # Boolean flag (cached)
```

### Permission Tables

```sql
roles
├── id
├── display_name
├── description
├── external_auth_id  # For LDAP/SAML
├── mfa_enforced
└── system_name       # 'admin', 'editor', etc.

role_permissions
├── id
├── role_id
├── permission_id     # FK to permissions.id
└── (role-level permissions)

permissions
├── id
├── name              # 'page-view', 'book-create', etc.
└── display_name
```

### Activity Tracking

```sql
activities
├── id
├── type              # ActivityType enum
├── detail            # JSON metadata
├── entity_id         # Related entity
├── entity_type       # Entity class
├── user_id           # Actor
├── ip                # IP address
├── created_at
└── loggable_id       # Polymorphic relation
```

### Search

```sql
search_terms
├── id
├── term              # Indexed search term
├── entity_id         # FK to entities.id
├── entity_type       # 'page', 'chapter', 'book'
├── score             # Relevance weight
└── (full-text indexed)
```

---

## 📖 Chapter 6: Extension Points

### Theme System

Custom themes can override views and add assets:

```
themes/
└── custom-theme/
    ├── functions.php       # Theme logic
    ├── public/            # Static assets
    │   ├── theme.css
    │   └── theme.js
    └── views/             # Override Blade templates
        └── layouts/
            └── simple.blade.php
```

**Activation:**
```env
APP_THEME=custom-theme
```

### Webhooks

Trigger external systems on events:

```php
// Configure webhook
$webhook = new Webhook([
    'endpoint' => 'https://example.com/webhook',
    'events' => ['page_update', 'page_create'],
    'active' => true
]);
```

**Event Types:**
- Content: create, update, delete
- User: create, update, delete
- Auth: login, logout

### Logical Themes (Events)

Inject custom logic without modifying core:

```php
// themes/custom/functions.php
use BookStack\Theming\ThemeEvents;

app()->make(ThemeEvents::class)->listen(
    ThemeEvents::AUTH_LOGIN,
    function ($method, $user) {
        // Custom login logic
        Log::info("User {$user->name} logged in via {$method}");
    }
);
```

### Custom Command Integration

Register custom Artisan commands:

```php
// themes/custom/functions.php
use Illuminate\Support\Facades\Artisan;

Artisan::command('custom:sync', function () {
    $this->info('Running custom sync...');
    // Custom logic
})->describe('Custom data sync');
```

---

## 🎯 Quick Reference

### Key Classes

| Class | Purpose | Location |
|-------|---------|----------|
| `Entity` | Base model for content | `app/Entities/Models/Entity.php` |
| `PageRepo` | Page data access | `app/Entities/Repos/PageRepo.php` |
| `PermissionApplicator` | Permission filtering | `app/Permissions/PermissionApplicator.php` |
| `Activity` | Audit logging facade | `app/Facades/Activity.php` |
| `Component` | Frontend base class | `resources/js/components/component.ts` |

### Helper Functions

```php
// app/App/helpers.php
user()              // Current authenticated user
userCan($action)    // Check permission
setting($key)       // Get system setting
```

### Configuration Files

| File | Purpose |
|------|---------|
| `config/app.php` | Application settings |
| `config/database.php` | Database connections |
| `config/auth.php` | Authentication config |
| `config/permission.php` | Permission settings |
| `config/services.php` | External services (OAuth, etc.) |

---

[← Back to Development](./README.md) | [Build & Test →](./build-and-test.md)
