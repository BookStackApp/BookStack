FROM linuxserver/bookstack:latest
COPY . /var/www/html
WORKDIR /var/www/html
RUN chown -R abc:abc /var/www/html