#!/bin/bash

set -e

env

if [[ -n "$1" ]]; then
    exec "$@"
else
    wait-for-it db:3306 -t 45
    php artisan migrate --database=mysql
    chown -R www-data:www-data storage
    exec apache2-foreground
fi