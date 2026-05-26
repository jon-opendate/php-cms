FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY apache/cms.conf /etc/apache2/conf-enabled/cms.conf
COPY public/ /var/www/html/
COPY src/ /var/www/src/

RUN chown -R www-data:www-data /var/www/html /var/www/src
