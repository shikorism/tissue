ARG TISSUE_FOUNDATION_IMAGE_NAME

FROM ${TISSUE_FOUNDATION_IMAGE_NAME} as foundation

FROM php:8.2.26-fpm-bullseye

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev libicu-dev \
    && docker-php-ext-install pdo_pgsql intl opcache \
    && rm -rf /var/lib/apt/lists/*

COPY --from=foundation --chown=www-data:www-data /app /app

COPY ./docker/production/bin/tissue-php-entrypoint /usr/local/bin/
COPY ./docker/production/config/php.ini "$PHP_INI_DIR/php.ini"

WORKDIR /app
ENTRYPOINT ["tissue-php-entrypoint"]
CMD ["php-fpm"]
