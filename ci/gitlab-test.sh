#!/usr/bin/env bash

cp .env.example .env
php composer.phar install
php artisan key:generate
php artisan migrate --seed
touch storage/app/installed
vendor/bin/phpunit
