#!/bin/bash

set -e

env

if [[ -n "$1" ]]; then
    exec "$@"
else
    composer install
    wait-for-it db:3306 -t 45
    mkdir -p storage public/uploads bootstrap/cache
    if [[ ! -f .env ]]; then
        if [[ -f .env.docker-dev ]]; then
            cp .env.docker-dev .env
        elif [[ -f .env.example ]]; then
            cp .env.example .env
        fi
    fi
    current_app_key=$(grep -E '^APP_KEY=' .env 2>/dev/null | cut -d '=' -f2- || true)
    if [[ -z "$current_app_key" || "$current_app_key" == "SomeRandomString" || "$current_app_key" == "base64:changeme" ]]; then
        php artisan key:generate --force
    fi
    php artisan migrate --database=mysql --force
    chown -R www-data storage public/uploads bootstrap/cache
    exec apache2-foreground
fi
