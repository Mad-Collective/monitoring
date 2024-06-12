FROM php:8.1-fpm-alpine
RUN docker-php-ext-install sockets

WORKDIR /app/monitoring
VOLUME ["/app/monitoring"]

RUN apk add --no-cache \
        curl
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer
