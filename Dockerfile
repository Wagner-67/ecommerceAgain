FROM php:8.4-apache

WORKDIR /var/www/html

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Apache DocumentRoot anpassen
RUN sed -ri -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf \
    && sed -ri -e "s!/var/www/!/var/www/html/public!g" /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# PHP Extensions + Tools installieren
RUN apt-get update && apt-get install -y \
    git zip unzip libicu-dev libpng-dev libzip-dev libxml2-dev libpq-dev libonig-dev \
    make autoconf gcc g++ \
 && docker-php-ext-install intl pdo_mysql zip gd opcache \
 && pecl install redis \
 && docker-php-ext-enable redis \
 && a2enmod rewrite \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

# WICHTIG: Apache neu starten damit Extensions geladen werden
RUN service apache2 restart

# Composer kopieren
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer