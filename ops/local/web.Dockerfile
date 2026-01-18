ARG IMAGE_VERSION=local

FROM evoting/composer:${IMAGE_VERSION} AS composer_base
FROM evoting/frontend:${IMAGE_VERSION} AS frontend

FROM php:8.4-alpine

ARG PHP_EXTS="bcmath ctype fileinfo mbstring pdo pdo_mysql gd pcntl zip"
ARG PHP_PECL_EXTS="redis"

WORKDIR /opt/apps/evoting

RUN apk add --no-cache --virtual .build-deps ${PHPIZE_DEPS} \
        freetype-dev libjpeg-turbo-dev libpng-dev libzip-dev curl-dev oniguruma-dev libxml2-dev \
    && apk add --no-cache freetype libjpeg-turbo libpng libzip curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure zip \
    && docker-php-ext-install -j$(nproc) ${PHP_EXTS} \
    && pecl install ${PHP_PECL_EXTS} \
    && docker-php-ext-enable ${PHP_PECL_EXTS} \
    && apk del .build-deps

RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini && \
    sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 128M/g' /usr/local/etc/php/php.ini && \
    sed -i 's/post_max_size = 8M/post_max_size = 128M/g' /usr/local/etc/php/php.ini

USER www-data

COPY --from=composer_base --chown=www-data /opt/apps/evoting /opt/apps/evoting
COPY --from=frontend --chown=www-data /opt/apps/evoting/public /opt/apps/evoting/public

ENTRYPOINT ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
