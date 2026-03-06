FROM php:8.4-apache
RUN a2enmod rewrite
COPY 000-default.conf /etc/apache2/sites-enabled/000-default.conf