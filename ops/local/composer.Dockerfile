# Base PHP Alpine image
FROM php:8.4-alpine AS composer_base

# Copy composer binary dari official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# PHP extensions
ARG PHP_EXTS="bcmath ctype fileinfo mbstring pdo pdo_mysql gd pcntl zip"
ARG PHP_PECL_EXTS="redis"

# Install system deps & PHP extensions
RUN apk add --no-cache --virtual .build-deps ${PHPIZE_DEPS} \
        freetype-dev libjpeg-turbo-dev libpng-dev libzip-dev curl-dev oniguruma-dev libxml2-dev \
    && apk add --no-cache freetype libjpeg-turbo libpng libzip curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure zip \
    && docker-php-ext-install -j$(nproc) ${PHP_EXTS} \
    && pecl install ${PHP_PECL_EXTS} \
    && docker-php-ext-enable ${PHP_PECL_EXTS} \
    && apk del .build-deps

# Set working directory
WORKDIR /opt/apps/evoting

# Copy only composer files (cache layer)
COPY composer.json composer.lock ./

# Install dependencies (cached)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --no-scripts

# Copy full app source
COPY . .

# Optimize autoload
RUN composer dump-autoload --optimize
