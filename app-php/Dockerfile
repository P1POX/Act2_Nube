FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql

COPY --chown=www-data:www-data . /var/www/html/