FROM php:8.2-apache

# تثبيت الأدوات المطلوبة
RUN apt-get update && apt-get install -y unzip git

# نسخ Composer من صورته الرسمية
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# تعيين مجلد العمل
WORKDIR /var/www/html

# نسخ كل ملفات المشروع (بما فيها composer.json و htdocs و secure)
COPY htdocs/ /var/www/html/
COPY secure/ /var/www/html/secure/
COPY composer.json composer.lock* ./
RUN composer install --no-dev --optimize-autoloader

# تفعيل mod_rewrite
RUN a2enmod rewrite

# إعداد Apache للسماح بإعادة الكتابة (mod_rewrite)
RUN echo '<Directory "/var/www/html">\n\
    AllowOverride All\n\
</Directory>' > /etc/apache2/conf-available/custom.conf \
    && a2enconf custom

# تعديل الصلاحيات
RUN chown -R www-data:www-data /var/www/html
