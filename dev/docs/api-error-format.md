# API Error Response Format

## Standard Error Response Structure

All API errors follow a consistent JSON structure:

```json
{
  "error": {
    "message": "Human-readable error description",
    "code": "MACHINE_READABLE_CODE",
    "status": 400
  }
}
```

### Fields

| Field | Type | Description |
|-------|------|-------------|
| `message` | string | Human-readable error message suitable for display |
| `code` | string | Machine-readable error code for programmatic handling |
| `status` | integer | HTTP status code (matches response status) |

### Validation Errors

When validation fails, an additional `validation` field contains field-specific errors:

```json
{
  "error": {
    "message": "The given data was invalid.",
    "code": "VALIDATION_ERROR",
    "status": 422,
    "validation": {
      "email": ["The email field is required."],
      "password": ["The password must be at least 8 characters."]
    }
  }
}
```

## Error Codes

| Code | HTTP Status | Description |
|------|-------------|-------------|
| `BAD_REQUEST` | 400 | Malformed request or invalid parameters |
| `UNAUTHORIZED` | 401 | Authentication required or failed |
| `FORBIDDEN` | 403 | Authenticated but lacks permission |
| `NOT_FOUND` | 404 | Resource does not exist |
| `METHOD_NOT_ALLOWED` | 405 | HTTP method not supported for this endpoint |
| `CONFLICT` | 409 | Request conflicts with existing state |
| `VALIDATION_ERROR` | 422 | Input validation failed |
| `TOO_MANY_REQUESTS` | 429 | Rate limit exceeded |
| `INTERNAL_ERROR` | 500 | Unexpected server error |
| `BAD_GATEWAY` | 502 | Upstream service error |
| `SERVICE_UNAVAILABLE` | 503 | Service temporarily unavailable |

## Examples

### 401 Unauthorized

```json
{
  "error": {
    "message": "Unauthenticated",
    "code": "UNAUTHORIZED",
    "status": 401
  }
}
```

### 404 Not Found

```json
{
  "error": {
    "message": "Page not found",
    "code": "NOT_FOUND",
    "status": 404
  }
}
```

### 500 Internal Error

```json
{
  "error": {
    "message": "An unexpected error occurred",
    "code": "INTERNAL_ERROR",
    "status": 500
  }
}
```

## Implementation

See the following files for implementation details:

- `app/Exceptions/Handler.php` - Central API error rendering
- `app/Http/Controller.php` - `jsonError()` helper method
- `app/Exceptions/StoppedAuthenticationException.php` - Auth-related errors

## Best Practices

1. **Always check the `code` field** for programmatic error handling
2. **Display the `message` field** to users as-is
3. **Never expose internal error details** in production
4. **Use `validation` field** to show field-level errors in forms
