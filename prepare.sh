#!/bin/bash

# https://laravel.com/docs/5.5/deployment
composer install --optimize-autoloader
php artisan config:cache