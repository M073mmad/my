FROM php:8.2-apache

RUN apt-get update && apt-get install -y unzip git

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock* ./

RUN composer install --no-dev --optimize-autoloader

COPY . .

RUN chown -R www-data:www-data /var/www/html

# لا حاجة لتعريف CMD لأن Apache يبدأ تلقائياً
