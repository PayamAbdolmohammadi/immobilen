#!/usr/bin/env bash

set -e

if [ -z "$1" ]; then
  echo "Usage: ./scripts/deploy-local.sh \"commit message\""
  exit 1
fi

npm run build

docker compose exec app php artisan test

git status
git add .
git commit -m "$1"

echo "Ready to push:"
echo "git push origin master"
