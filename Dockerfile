FROM php:7.1-apache

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN apt-get update \
    && apt-get install -y git libpq-dev unzip \
    && docker-php-ext-install pdo_pgsql \
    && pecl install xdebug \
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && a2enmod rewrite

COPY dist/bin /usr/local/bin/
COPY dist/php.d /usr/local/etc/php/php.d/

ENTRYPOINT ["tissue-entrypoint.sh"]
CMD ["apache2-foreground"]

WORKDIR /var/www/html
