FROM php:7.1.7-apache

RUN apt-get update && \
    apt-get install -y --no-install-recommends git zip unzip && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /var/www/html/

RUN cd /var/www/html && \
    composer install --no-interaction 