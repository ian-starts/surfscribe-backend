#!/bin/bash

echo "$(date) [deployed.sh] Running deploy script"

if [ -f artisan ]; then
    php artisan migrate --force
    php artisan cache:clear
    php artisan route:cache
    php artisan config:cache
fi