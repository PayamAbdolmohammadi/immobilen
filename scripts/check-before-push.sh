#!/usr/bin/env bash

set -e

echo "Running checks before push..."

npm run build

docker compose exec app php artisan test

git status

echo "Checks completed."
