#!/bin/bash

set -e

if [[ "$1" == "composer" ]]; then
    exec "$@"
else
    wait-for-it db:3306 -t 45
    php artisan migrate --database=mysql_docker_dev
    chown -R www-data:www-data storage
    exec apache2-foreground
fi