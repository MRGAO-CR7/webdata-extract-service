# use the php image for consistency - no added overhead if not using any php
FROM php:7.0.30-apache

# enable Apache's mod_rewrite
RUN a2enmod rewrite

RUN apt-get update && \
    apt-get install -y \
    libxml2-dev

# install redis extension
RUN pecl install -o -f redis \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis

# install required PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql bcmath soap

WORKDIR /var/www/html
COPY . .

# add apache alias for /customers/v1
RUN echo 'Alias /webdata-extract-service/v1 /var/www/html/app/webroot' > /etc/apache2/conf-enabled/webdata-extract-service.conf

# update `www-data`'s uid to match host users - fixes VirtualBox + Ubuntu issues
ARG APACHE_USER_UID=1000
ENV APACHE_USER_UID=${APACHE_USER_UID}
RUN usermod -u $APACHE_USER_UID www-data
