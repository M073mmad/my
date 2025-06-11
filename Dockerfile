# نستخدم صورة PHP الرسمية مع Apache (أو CLI حسب مشروعك)
FROM php:8.1-apache

# تثبيت الأدوات المطلوبة (curl, unzip, git, ... إلخ)
RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# تثبيت Composer بشكل رسمي
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# نسخ ملفات المشروع إلى مجلد العمل داخل الحاوية
WORKDIR /var/www/html
COPY . /var/www/html

# تشغيل أمر composer install لتثبيت الـ dependencies في مجلد vendor
RUN composer install --no-dev --optimize-autoloader

# تعديل صلاحيات المجلدات (اختياري حسب حاجتك)
RUN chown -R www-data:www-data /var/www/html/vendor

# تعيين بورت السيرفر (اعتمادًا على Apache في الصورة)
EXPOSE 80

# الأمر الافتراضي لتشغيل Apache
CMD ["apache2-foreground"]
