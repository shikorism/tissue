FROM php:8.2.26-cli-bullseye as php

RUN apt-get update \
    && apt-get install -y git libpq-dev unzip libicu-dev \
    && docker-php-ext-install pdo_pgsql intl \
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY . /app

RUN composer install -n --no-dev --prefer-dist --optimize-autoloader

FROM node:22.6.0-bullseye

WORKDIR /app
COPY --from=php /app /app

RUN corepack enable
RUN yarn install \
    && yarn run prod \
    && yarn run doc
