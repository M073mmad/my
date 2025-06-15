FROM php:8.2-apache

RUN apt-get update && apt-get install -y unzip git

# تثبيت Composer من صورة مستقلة
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# نسخ ملفات المشروع
COPY htdocs/ /var/www/html/
COPY secure/ /var/www/html/secure/
COPY apache-config/custom.conf /etc/apache2/conf-available/custom.conf
COPY composer.json composer.lock* ./

# تثبيت تبعيات PHP بدون حزم التطوير وتحسين التحميل التلقائي
RUN composer install --no-dev --optimize-autoloader

# تفعيل mod_rewrite وتهيئة custom.conf
RUN a2enmod rewrite
RUN a2enconf custom

# إصلاح تحذير ServerName
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# **لا تغير البورت، اترك Apache يعمل على 80**
# (قم بحذف أو تعليق أي تعديل للبورت)

# ضبط صلاحيات الملفات لمستخدم الويب
RUN chown -R www-data:www-data /var/www/html

# إعلان بورت 80 (الافتراضي لـ HTTP)
EXPOSE 80
