FROM php:8.2-apache

# تثبيت الأدوات المطلوبة
RUN apt-get update && apt-get install -y unzip git

# نسخ Composer من الصورة الرسمية
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# تعيين مجلد العمل
WORKDIR /app

# نسخ composer.json و composer.lock فقط أولاً
COPY composer.json composer.lock* ./

# تثبيت مكتبات PHP
RUN composer install --no-dev --optimize-autoloader

# نسخ باقي ملفات المشروع (بما فيها htdocs)
COPY . .

# تشغيل التطبيق
CMD ["php", "htdocs/index.php"]
