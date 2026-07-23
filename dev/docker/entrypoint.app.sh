#!/bin/bash

set -e

env

if [[ -n "$1" ]]; then
    exec "$@"
else
    mkdir -p /app/storage/framework/{cache/data,sessions,testing,views}
    chown -R www-data /app/storage/framework || true
    chown -R www-data /app/bootstrap/cache || true
    chown -R www-data /app/public/uploads || true
    composer install
    wait-for-it db:3306 -t 45
    php artisan migrate --database=mysql --force
    exec apache2-foreground
fi
