#!/usr/bin/env bash

set -e

cd ~/immobilen

git fetch origin
git checkout master
git pull origin master

docker compose down
docker compose up -d --build

docker compose exec app php artisan migrate --force
docker compose exec app php artisan optimize:clear
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache

curl -I https://immobilen.payamdev.de
