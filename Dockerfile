FROM php:8.2-apache

# تحديث النظام وتثبيت أدوات
RUN apt-get update && apt-get install -y unzip git

# نسخ Composer من صورة composer الرسمية
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# تعيين مجلد العمل داخل الحاوية
WORKDIR /var/www/html

# نسخ ملفات composer (للتبعيات)
COPY composer.json composer.lock* ./

# تثبيت التبعيات باستخدام Composer
RUN composer install --no-dev --optimize-autoloader

# نسخ ملفات المشروع (مجلد htdocs فقط) إلى مجلد الويب
COPY htdocs/ /var/www/html/
COPY secure/ /var/www/html/

# تعديل صلاحيات الملفات ليتمكن Apache من الوصول إليها
RUN chown -R www-data:www-data /var/www/html

# لا حاجة لتعريف CMD لأن Apache يبدأ تلقائياً
