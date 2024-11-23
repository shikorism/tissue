FROM node:22.6.0-bullseye as node

FROM php:8.2.26-apache

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN apt-get update \
    && apt-get install -y git libpq-dev unzip libicu-dev \
    && docker-php-ext-install pdo_pgsql intl \
    && pecl install xdebug \
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && a2enmod rewrite

COPY docker/development/bin /usr/local/bin/
COPY docker/development/php.d /usr/local/etc/php/php.d/

COPY --from=node /usr/local/bin/node /usr/local/bin/
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node /opt/yarn-* /opt/yarn

RUN ln -s ../lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s ../lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx \
    && ln -s ../lib/node_modules/corepack/dist/corepack.js /usr/local/bin/corepack
RUN corepack enable

ENV COREPACK_ENABLE_DOWNLOAD_PROMPT 0

ENTRYPOINT ["tissue-entrypoint.sh"]
CMD ["apache2-foreground"]

WORKDIR /var/www/html
