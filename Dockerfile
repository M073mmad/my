FROM php:8.2-apache

RUN apt-get update && apt-get install -y unzip git

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# نسخ الملفات
COPY htdocs/ /var/www/html/
COPY secure/ /var/www/html/secure/
COPY apache-config/custom.conf /etc/apache2/conf-available/custom.conf
COPY composer.json composer.lock* ./

# تثبيت الاعتماديات
RUN composer install --no-dev --optimize-autoloader

# تفعيل Apache mod_rewrite
RUN a2enmod rewrite

# تفعيل إعدادات Apache المخصصة
RUN a2enconf custom

# إصلاح تحذير ServerName
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# إعطاء صلاحيات للمجلد
RUN chown -R www-data:www-data /var/www/html
