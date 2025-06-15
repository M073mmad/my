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

# إصلاح تحذير ServerName
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# تغيير المنفذ من 80 إلى 8000 داخل Apache
RUN sed -i 's/80/8000/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

RUN chown -R www-data:www-data /var/www/html

# تأكد أن Apache يشتغل عند تشغيل الحاوية
EXPOSE 8000
