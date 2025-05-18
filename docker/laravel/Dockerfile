FROM php:8.2-fpm-alpine

# Install dependencies
RUN apk add --no-cache \
    bash \
    git \
    curl \
    libzip-dev \
    unzip \
    zip \
    oniguruma-dev \
    icu-dev \
    gcc \
    g++ \
    make \
    autoconf

# PHP extensions
RUN docker-php-ext-install pdo pdo_mysql zip intl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy application code
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --no-progress --prefer-dist

# Fix permissions for Laravel
RUN chown -R 1000:1000 /var/www/storage /var/www/bootstrap/cache

# Expose PHP-FPM port
EXPOSE 9000
CMD ["php-fpm"]