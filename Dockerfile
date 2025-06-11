FROM php:8.2-apache

# تثبيت الأدوات المطلوبة
RUN apt-get update && apt-get install -y unzip git

# نسخ Composer من صورته الرسمية
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# تعيين مجلد العمل
WORKDIR /var/www/html

# نسخ كل ملفات المشروع (بما فيها htdocs و secure و composer.json و vendor إن وُجد)
COPY . .

# تثبيت التبعيات
RUN composer install --no-dev --optimize-autoloader

# تفعيل mod_rewrite
RUN a2enmod rewrite

# إعدادات Apache
RUN echo '<Directory "/var/www/html">\n\
    AllowOverride All\n\
</Directory>' > /etc/apache2/conf-available/custom.conf \
    && a2enconf custom

# صلاحيات
RUN chown -R www-data:www-data /var/www/html
