# BookStack API Documentation

## Overview

The BookStack API provides programmatic access to BookStack content including pages, chapters, books, shelves, users, and more.

## Authentication

All API requests require authentication via:
- **API Token**: Pass in the `Authorization` header: `Authorization: Bearer YOUR_API_TOKEN`
- **Session Cookie**: For browser-based API exploration (GET requests only)

## Base URL

```
GET http://your-bookstack-url/api/
```

## Standard Error Response

All errors follow a consistent format:

```json
{
  "error": {
    "message": "Human-readable error description",
    "code": "MACHINE_READABLE_CODE",
    "status": 400
  }
}
```

## Permission Requirements

### Endpoint Permission Matrix

| Endpoint | Method | Permission Required | Middleware |
|----------|--------|---------------------|------------|
| **Pages** | | | |
| `/api/pages` | GET | (none - uses entity visibility) | - |
| `/api/pages` | POST | `page-create` | `can:page-create` |
| `/api/pages/{id}` | GET | (none - uses entity visibility) | - |
| `/api/pages/{id}` | PUT | (entity-specific) | controller |
| `/api/pages/{id}` | DELETE | (entity-specific) | controller |
| `/api/pages/{id}/export/*` | GET | `content-export` | `can:content-export` |
| **Chapters** | | | |
| `/api/chapters` | GET | (none - uses entity visibility) | - |
| `/api/chapters` | POST | `chapter-create` | `can:chapter-create` |
| `/api/chapters/{id}` | GET | (none - uses entity visibility) | - |
| `/api/chapters/{id}` | PUT | (entity-specific) | controller |
| `/api/chapters/{id}` | DELETE | (entity-specific) | controller |
| `/api/chapters/{id}/export/*` | GET | `content-export` | `can:content-export` |
| **Books** | | | |
| `/api/books` | GET | (none - uses entity visibility) | - |
| `/api/books` | POST | `book-create` | `can:book-create` |
| `/api/books/{id}` | GET | (none - uses entity visibility) | - |
| `/api/books/{id}` | PUT | (entity-specific) | controller |
| `/api/books/{id}` | DELETE | (entity-specific) | controller |
| `/api/books/{id}/export/*` | GET | `content-export` | `can:content-export` |
| **Shelves** | | | |
| `/api/shelves` | GET | (none - uses entity visibility) | - |
| `/api/shelves` | POST | `bookshelf-create` | `can:bookshelf-create` |
| `/api/shelves/{id}` | GET | (none - uses entity visibility) | - |
| `/api/shelves/{id}` | PUT | (entity-specific) | controller |
| `/api/shelves/{id}` | DELETE | (entity-specific) | controller |
| **Attachments** | | | |
| `/api/attachments` | GET | (none) | - |
| `/api/attachments` | POST | `attachment-create` | `can:attachment-create` |
| `/api/attachments/{id}` | GET | (none) | - |
| `/api/attachments/{id}` | PUT | (entity-specific) | controller |
| `/api/attachments/{id}` | DELETE | (entity-specific) | controller |
| **Comments** | | | |
| `/api/comments` | GET | (none) | - |
| `/api/comments` | POST | `comment-create` | `can:comment-create` |
| `/api/comments/{id}` | GET | (none) | - |
| `/api/comments/{id}` | PUT | (entity-specific) | controller |
| `/api/comments/{id}` | DELETE | (entity-specific) | controller |
| **Images** | | | |
| `/api/image-gallery` | GET | (none) | - |
| `/api/image-gallery` | POST | `image-create` | `can:image-create` |
| `/api/image-gallery/{id}` | GET | (none) | - |
| `/api/image-gallery/{id}` | PUT | (entity-specific) | controller |
| `/api/image-gallery/{id}` | DELETE | (entity-specific) | controller |
| **Admin Endpoints** | | | |
| `/api/users` | * | `users-manage` | `can:users-manage` |
| `/api/roles` | * | `user-roles-manage` | `can:user-roles-manage` |
| `/api/audit-log` | GET | `settings-manage` | `can:settings-manage` |
| `/api/system` | GET | `settings-manage` | `can:settings-manage` |
| `/api/content-permissions/*` | * | `restrictions-manage` | `can:restrictions-manage` |
| `/api/recycle-bin` | * | `settings-manage` | `can:settings-manage` |
| `/api/imports` | * | `content-import` | `can:content-import` |
| **Public Endpoints** | | | |
| `/api/search` | GET | (none) | - |
| `/api/docs.json` | GET | (none) | - |

## Permission Types

### Entity-Specific Permissions (Checked in Controllers)

For read, update, and delete operations on specific entities, permissions are checked against the specific entity:
- **View**: User must have view permission on the entity
- **Update**: User must have update permission on the entity
- **Delete**: User must have delete permission on the entity

These cannot be checked at the route level because the entity must first be loaded from the database.

### Generic Create Permissions (Checked at Route Level)

Create operations use generic permissions that can be checked at the route level:
- `page-create`
- `chapter-create`
- `book-create`
- `bookshelf-create`
- `attachment-create`
- `comment-create`
- `image-create`

### Admin Permissions

Admin endpoints require specific system permissions:
- `users-manage` - Manage users
- `user-roles-manage` - Manage roles
- `settings-manage` - System settings
- `restrictions-manage` - Content restrictions
- `content-export` - Export content
- `content-import` - Import content

## Error Codes

| Code | HTTP Status | Description |
|------|-------------|-------------|
| `UNAUTHORIZED` | 401 | Authentication required |
| `FORBIDDEN` | 403 | Authenticated but lacks permission |
| `NOT_FOUND` | 404 | Resource not found |
| `VALIDATION_ERROR` | 422 | Input validation failed |
| `INTERNAL_ERROR` | 500 | Server error |

## Listing Endpoints

List endpoints return paginated results:

```json
{
  "data": [...],
  "total": 100,
  "per_page": 20,
  "current_page": 1,
  "last_page": 5
}
```

Query parameters:
- `limit` - Number of items per page (default: 20, max: 100)
- `offset` - Number of items to skip (for pagination)
- `sort` - Field to sort by (e.g., `name`, `-created_at`)
- `filter` - Filter expression (e.g., `filter=name:contains:Test`)
