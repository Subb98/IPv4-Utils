FROM php:8.1.1-cli-alpine3.15

WORKDIR /app

COPY . /app

RUN curl https://getcomposer.org/composer.phar > /usr/local/bin/composer \
    && chmod +x /usr/local/bin/composer \
    && composer install
