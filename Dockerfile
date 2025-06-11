FROM php:8.2-cli

# تثبيت الأدوات المطلوبة
RUN apt-get update && apt-get install -y unzip git

# تثبيت Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# مجلد العمل داخل الحاوية
WORKDIR /app

# نسخ ملفات المشروع
COPY . .

# تثبيت التبعيات (vendor)
RUN composer install --no-dev --optimize-autoloader

# ملف التشغيل الأساسي (غير إذا لزم)
CMD ["php", "htdocs/index.php"]
