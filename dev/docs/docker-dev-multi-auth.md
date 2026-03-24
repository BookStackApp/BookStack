# Docker Dev Setup For Multi-Auth Testing

This repository already ships with a general Docker development setup. This document describes the branch-local stack added for testing mixed authentication modes such as `standard + oidc`.

By default this stack now includes a fake OIDC provider, so you can test the mixed login flow without real Entra, client secrets or metadata setup.

## Files

- `docker-compose.dev.yml`: Dedicated development stack.
- `.env.docker-dev.example`: Example environment for local + OIDC testing.
- `dev/docker/entrypoint.app.sh`: Bootstraps composer, app key and migrations on container startup.

## Quick Start

1. Copy the example env file:

   ```bash
   cp .env.docker-dev.example .env.docker-dev
   ```

2. Start the stack:

   ```bash
   docker compose --env-file .env.docker-dev -f docker-compose.dev.yml up --build
   ```

3. Open BookStack at `http://localhost:8080`.

4. Open MailHog at `http://localhost:8025`.

5. Use the normal BookStack login form for local accounts, or click the fake OIDC button for the external flow.

## Default Behavior

- The app boots with `AUTH_METHODS=standard,oidc`.
- `AUTH_PRIMARY_METHOD=oidc` makes OIDC the preferred external method.
- Local accounts still remain available via the standard login form.
- The fake OIDC provider is exposed on `http://localhost:9091`.
- The default fake OIDC user is `fake.user@example.com`.
- If `APP_KEY` is left as `base64:changeme`, the app container will generate one automatically on first boot.

## Fake OIDC Provider

The fake provider auto-approves the authorization request and immediately redirects back to BookStack with a valid code flow result.

You can adjust the fake identity in `.env.docker-dev` with these optional values:

- `FAKE_OIDC_EMAIL`
- `FAKE_OIDC_NAME`
- `FAKE_OIDC_SUB`
- `FAKE_OIDC_USERNAME`
- `FAKE_OIDC_GROUPS`

## Switching To Real Entra Later

If you want to test against Entra instead of the fake provider, replace the `OIDC_*` values in `.env.docker-dev` and set the auth/token/userinfo/logout endpoints to your real tenant.

## Useful Commands

Run migrations or artisan commands:

```bash
docker compose --env-file .env.docker-dev -f docker-compose.dev.yml run --rm app php artisan list
```

Run PHPUnit:

```bash
docker compose --env-file .env.docker-dev -f docker-compose.dev.yml run --rm app php artisan test
```

Run only the multi-auth tests:

```bash
docker compose --env-file .env.docker-dev -f docker-compose.dev.yml run --rm app php artisan test tests/Auth/MultiAuthTest.php
```

Stop and remove containers:

```bash
docker compose --env-file .env.docker-dev -f docker-compose.dev.yml down
```

Reset the database volume:

```bash
docker compose --env-file .env.docker-dev -f docker-compose.dev.yml down -v
```
