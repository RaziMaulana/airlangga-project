FROM php:8.4-fpm-alpine

# Install library pendukung sistem untuk PostgreSQL, GD, dan ZIP
RUN apk add --no-cache \
    postgresql-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql pgsql gd zip

# Ambil binary Composer resmi
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# Tambahkan --no-scripts agar tidak error saat database/env belum ada
RUN composer install --no-dev --no-scripts --optimize-autoloader

# Atur permission direktori cache dan storage
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]