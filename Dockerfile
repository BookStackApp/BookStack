FROM php:5-apache
MAINTAINER Dan Brown
COPY . /var/www/html
COPY docker-entrypoint.sh /var/www/html
RUN apt-get update && \
    apt-get install git sed zlib1g-dev mysql-client -y && \
    curl -O https://getcomposer.org/installer && \
    php installer --install-dir=/usr/bin --filename=composer && \
    rm installer && chmod +x /var/www/html/docker-entrypoint.sh && \
    docker-php-ext-install pdo_mysql mbstring zip && cd /var/www/html && \
    composer install
ENV DBNAME=bookstack
WORKDIR /var/www/html
EXPOSE 80
CMD /var/www/html/docker-entrypoint.sh
