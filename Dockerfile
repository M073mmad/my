FROM php:8.2-cli

RUN apt-get update && apt-get install -y unzip git

# نسخ composer من الصورة الرسمية
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# انسخ كل ملفات المشروع (بما فيها composer.json و composer.lock و htdocs)
COPY . /app

# نفذ composer install
RUN composer install --no-dev --optimize-autoloader

# شغل السيرفر على مجلد htdocs
CMD ["php", "-S", "0.0.0.0:80", "-t", "htdocs"]
