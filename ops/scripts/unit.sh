#!/bin/sh

PHP_VERSION=$(php -r 'echo PHP_VERSION;')
printf "\nRunning unit tests in PHP ${PHP_VERSION}\n"
./vendor/bin/phpspec run