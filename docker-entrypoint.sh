#!/bin/bash
if [ -f ./.env.example ]; then
  echo "Waiting for database (20s)..."
  sleep 20
  echo 'Creating config file...'
  mv ./.env.example ./.env
  sed -i s/DB_HOST=localhost/DB_HOST=$MYSQL_PORT_3306_TCP_ADDR/ .env
  sed -i s/DB_USERNAME=database_username/DB_USERNAME=root/ .env
  sed -i s/DB_DATABASE=database_database/DB_DATABASE=$DBNAME/ .env
  sed -i s/DB_PASSWORD=database_user_password/DB_PASSWORD=$MYSQL_ENV_MYSQL_ROOT_PASSWORD/ .env
  echo 'Install Dependencies...'
  php artisan key:generate
  echo 'Configuring web and database servers...'
  rm /etc/apache2/sites-available/000-default.conf && mv /var/www/html/000-default.conf /etc/apache2/sites-available && cd /etc/apache2/sites-enabled && ls -s /etc/apache2/sites-available/000-default.conf
  mysql -h $MYSQL_PORT_3306_TCP_ADDR -u root -p$MYSQL_ENV_MYSQL_ROOT_PASSWORD -e "CREATE DATABASE $DBNAME;"
  cd /var/www/html && php artisan migrate --force
  a2enmod rewrite
  chown -R www-data:www-data /var/www/html
fi

apache2-foreground
