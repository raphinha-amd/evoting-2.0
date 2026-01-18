ARG IMAGE_VERSION=production

FROM evoting/composer:${IMAGE_VERSION} AS composer_base
FROM evoting/frontend:${IMAGE_VERSION} AS frontend

# We need a stage which contains FPM to actually run and process requests to our PHP application.
FROM php:8.4-fpm-alpine

# We need to declare that we want to use the args in this build step
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

# Update the php.ini file to increase the upload and post size
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini && \
    sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 600M/g' /usr/local/etc/php/php.ini && \
    sed -i 's/post_max_size = 8M/post_max_size = 600M/g' /usr/local/etc/php/php.ini

# As FPM uses the www-data user when running our application,
# we need to make sure that we also use that user when starting up,
# so our user "owns" the application when running
USER  www-data

# We have to copy in our code base from our initial build which we installed in the previous stage
COPY --from=composer_base --chown=www-data /opt/apps/evoting /opt/apps/evoting
COPY --from=frontend --chown=www-data /opt/apps/evoting/public /opt/apps/evoting/public

# We want to cache the event, routes, and views so we don't try to write them when we are in Kubernetes.
# Docker builds should be as immutable as possible, and this removes a lot of the writing of the live application.
RUN php artisan event:cache && \
    php artisan route:cache && \
    php artisan view:cache
