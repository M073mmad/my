FROM php:8.2-apache

RUN apt-get update && apt-get install -y unzip git

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY htdocs/ /var/www/html/
COPY secure/ /var/www/html/secure/
COPY apache-config/custom.conf /etc/apache2/conf-available/custom.conf

COPY composer.json composer.lock* ./

RUN composer install --no-dev --optimize-autoloader

RUN a2enmod rewrite

RUN a2enconf custom

RUN chown -R www-data:www-data /var/www/html
