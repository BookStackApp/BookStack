#!/bin/sh
# This script will install a new BookStack instance on a fresh Debian 10.2 server.
# This script is experimental and does not ensure any security.
# Original Script by BookStack
# Updated Script by https://github.com/th3r3isnospoon

# Fetch domain to use from first provided parameter,
# Otherwise request the user to input their domain
DOMAIN=$1
if [ -z $1 ]
then
echo ""
printf "Enter the domain you want to host BookStack and press [ENTER]\nExamples: my-site.com or docs.my-site.com\n"
read DOMAIN
fi

# Get the current machine IP address
CURRENT_IP=$(ip addr | grep 'state UP' -A2 | tail -n1 | awk '{print $2}' | cut -f1  -d'/')

# Install core system packages
export DEBIAN_FRONTEND=noninteractive
apt update
apt install -y git apache2 curl unzip sudo php7.3-fpm php7.3-curl php7.3-mbstring php7.3-ldap \
php7.3-tidy php7.3-xml php7.3-zip php7.3-gd php7.3-mysql mariadb-server-10.3 libapache2-mod-php7.3

# Set up database
DB_PASS="$(head /dev/urandom | tr -dc A-Za-z0-9 | head -c 13)"
mysql -u root --execute="CREATE DATABASE bookstack;"
mysql -u root --execute="CREATE USER 'bookstack'@'localhost' IDENTIFIED BY '$DB_PASS';"
mysql -u root --execute="GRANT ALL ON bookstack.* TO 'bookstack'@'localhost';FLUSH PRIVILEGES;"

# Download BookStack
cd /var/www
git clone https://github.com/BookStackApp/BookStack.git --branch release --single-branch bookstack
BOOKSTACK_DIR="/var/www/bookstack"
cd $BOOKSTACK_DIR

# Install composer
EXPECTED_SIGNATURE=$(wget https://composer.github.io/installer.sig -O - -q)
curl -s https://getcomposer.org/installer > composer-setup.php
ACTUAL_SIGNATURE=$(php -r "echo hash_file('SHA384', 'composer-setup.php');")

if [ "$EXPECTED_SIGNATURE" = "$ACTUAL_SIGNATURE" ]
then
    php composer-setup.php --quiet
    RESULT=$?
    rm composer-setup.php
else
    >&2 echo 'ERROR: Invalid composer installer signature'
    rm composer-setup.php
    exit 1
fi

# Install BookStack composer dependancies
php composer.phar install

# Copy and update BookStack environment variables
cp .env.example .env
sed -i.bak 's/DB_DATABASE=.*$/DB_DATABASE=bookstack/' .env
sed -i.bak 's/DB_USERNAME=.*$/DB_USERNAME=bookstack/' .env
sed -i.bak "s/DB_PASSWORD=.*\$/DB_PASSWORD=$DB_PASS/" .env
echo "APP_URL="
# Generate the application key
php artisan key:generate --no-interaction --force
# Migrate the databases
php artisan migrate --no-interaction --force

# Set file and folder permissions
chown www-data:www-data -R bootstrap/cache public/uploads storage && chmod -R 755 bootstrap/cache public/uploads storage

# Set up apache
sudo a2enmod rewrite
sudo a2enmod php7.3

cat >/etc/apache2/sites-available/bookstack.conf <<EOL
<VirtualHost *:80>
	ServerName ${DOMAIN}

	ServerAdmin webmaster@localhost
	DocumentRoot /var/www/bookstack/public/

    <Directory /var/www/bookstack/public/>
        Options Indexes FollowSymLinks
        AllowOverride None
        Require all granted
        <IfModule mod_rewrite.c>
            <IfModule mod_negotiation.c>
                Options -MultiViews -Indexes
            </IfModule>

            RewriteEngine On

            # Handle Authorization Header
            RewriteCond %{HTTP:Authorization} .
            RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

            # Redirect Trailing Slashes If Not A Folder...
            RewriteCond %{REQUEST_FILENAME} !-d
            RewriteCond %{REQUEST_URI} (.+)/$
            RewriteRule ^ %1 [L,R=301]

            # Handle Front Controller...
            RewriteCond %{REQUEST_FILENAME} !-d
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteRule ^ index.php [L]
        </IfModule>
    </Directory>

	ErrorLog ${APACHE_LOG_DIR}/error.log
	CustomLog ${APACHE_LOG_DIR}/access.log combined

</VirtualHost>
EOL

sudo a2dissite 000-default.conf
sudo a2ensite bookstack.conf

# Restart apache to load new config
systemctl reload apache2

echo ""
echo "Setup Finished, Your BookStack instance should now be installed."
echo "You can login with the email 'admin@admin.com' and password of 'password'"
echo "MySQL was installed without a root password, It is recommended that you set a root MySQL password."
echo ""
echo "You can access your BookStack instance at: http://$CURRENT_IP/ or http://$DOMAIN/"
echo ""
echo "If Your BookStack instance doesn't load, please run 'systemctl reload apache2' and retry."
echo ""
