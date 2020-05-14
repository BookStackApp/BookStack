FROM php:7.3-apache

ENV APACHE_DOCUMENT_ROOT /app/public
WORKDIR /app

RUN apt-get update -y \
    && apt-get install -y git zip unzip libtidy-dev libpng-dev libldap2-dev libxml++2.6-dev wait-for-it libzip-dev \
    && docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu \
    && docker-php-ext-install pdo pdo_mysql tidy dom xml mbstring gd ldap zip \
    && a2enmod rewrite \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && mv composer.phar /usr/bin/composer \
    && php -r "unlink('composer-setup.php');"
