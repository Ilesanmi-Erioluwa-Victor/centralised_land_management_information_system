FROM php:8.2-apache

RUN apt-get update && apt-get install -y libpq-dev zip unzip git \
    && docker-php-ext-install pdo pdo_pgsql \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . /var/www/html/

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && chown -R www-data:www-data /var/www/html/public/uploads

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

EXPOSE 80
