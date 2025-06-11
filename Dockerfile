FROM php:8.2-apache

# تفعيل mod_rewrite لو كنت تحتاجه
RUN a2enmod rewrite

# نسخ ملفات المشروع إلى السيرفر داخل الحاوية
COPY . /var/www/html/

# تغيير مجلد العمل الافتراضي
WORKDIR /var/www/html/

# تثبيت Composer داخل الحاوية (اختياري لأنك سترفع vendor بنفسك)
# لكن لا ضرر من إضافته
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# التأكد من تحميل مجلد vendor داخل الصورة (اختياري)
RUN ls -la vendor
