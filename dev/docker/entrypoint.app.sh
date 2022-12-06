#!/bin/bash

set -e

env

if [[ -n "$1" ]]; then
    exec "$@"
else
    composer install
    wait-for-it db:3306 -t 45
    php artisan migrate --database=mysql
    chown www-data:www-data -R bootstrap/cache public/uploads storage 
    chmod -R 755 bootstrap/cache public/uploads storage
    exec apache2-foreground
fi
