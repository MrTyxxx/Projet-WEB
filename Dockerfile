FROM php:8.4-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite

# Ajout des outils nécessaires + Composer
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

WORKDIR /var/www/html