FROM ubuntu:24.04

# Install additional dependencies
RUN apt-get update && \
    apt-get install -y \
        git \
		wget \
        zip \
        unzip \
		php \
		php-bcmath php-curl php-mbstring php-gd php-xml php-zip php-mysql php-ldap \
       && \
    rm -rf /var/lib/apt/lists/*

# Take branch as an argument so we can choose which BookStack
# branch to test against
ARG BRANCH=development

# Download BookStack & install PHP deps
RUN mkdir -p /var/www && \
    git clone https://github.com/bookstackapp/bookstack.git --branch "$BRANCH" --single-branch /var/www/bookstack && \
    cd /var/www/bookstack && \	
   wget https://raw.githubusercontent.com/composer/getcomposer.org/f3108f64b4e1c1ce6eb462b159956461592b3e3e/web/installer -O - -q | php -- --quiet --filename=composer && \
    php composer install

# Set the BookStack dir as the default working dir
WORKDIR /var/www/bookstack

# Set the default action as running php
ENTRYPOINT ["/bin/php"]
