FROM php:8-apache

# Install system dependencies
RUN apt update && apt install -y \
    git zip unzip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

COPY . .

RUN composer install

USER $user
