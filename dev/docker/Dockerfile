FROM php:8.1-apache

ENV APACHE_DOCUMENT_ROOT /app/public
WORKDIR /app

RUN <<EOR
# Install additional dependencies
apt-get update
apt-get install -y \
	git \
	zip \
	unzip \
	libpng-dev \
	libldap2-dev \
	libzip-dev \
	wait-for-it
rm -rf /var/lib/apt/lists/*

# Configure apache
docker-php-ext-configure ldap --with-libdir="lib/$(gcc -dumpmachine)"
docker-php-ext-install pdo_mysql gd ldap zip
pecl install xdebug
docker-php-ext-enable xdebug
a2enmod rewrite
sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Install composer
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Use the default production configuration and update it as required
mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
sed -i 's/memory_limit = 128M/memory_limit = 512M/g' "$PHP_INI_DIR/php.ini"
EOR
