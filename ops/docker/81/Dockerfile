FROM php:8.1-alpine

RUN apk add --no-cache --virtual .deps \
                git \
                make \
                bash

RUN docker-php-ext-install sockets

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer