# PHP-FPM image for the Hotel Inventory & Search API/Web app.
FROM php:8.3-fpm-alpine

# System dependencies for the PHP extensions we need.
RUN apk add --no-cache \
        bash \
        git \
        icu-dev \
        libzip-dev \
        oniguruma-dev \
        $PHPIZE_DEPS

# Core PHP extensions (MySQL, intl, mbstring, bcmath, zip).
RUN docker-php-ext-install pdo_mysql intl mbstring bcmath zip

# Redis extension (used as cache/session store; enables search cache tags).
RUN pecl install redis && docker-php-ext-enable redis

# Composer (copied from the official Composer image).
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install PHP dependencies first to leverage Docker layer caching.
COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-scripts --prefer-dist

# Copy the rest of the application.
COPY . .

RUN composer dump-autoload --optimize \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
