FROM php:8.1-apache

ENV APACHE_DOCUMENT_ROOT /app/public
WORKDIR /app

# Install additional dependacnies and configure apache
RUN apt-get update -y \
    && apt-get install -y git zip unzip libpng-dev libldap2-dev libzip-dev wait-for-it \
    && docker-php-ext-configure ldap --with-libdir="lib/$(gcc -dumpmachine)" \
    && docker-php-ext-install pdo_mysql gd ldap zip \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && a2enmod rewrite \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && mv composer.phar /usr/bin/composer \
    && php -r "unlink('composer-setup.php');"

# Use the default production configuration and update it as required
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && sed -i 's/memory_limit = 128M/memory_limit = 512M/g' "$PHP_INI_DIR/php.ini"
