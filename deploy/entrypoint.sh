#!/bin/sh
set -e

# Runs once per container start (Render restarts the container on every
# deploy, so this effectively runs on every deploy — no separate release
# step needed for this single-service setup).
php artisan config:cache
php artisan route:cache
php artisan view:cache

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
	php artisan migrate --force
fi

php artisan storage:link || true

exec supervisord -c /etc/supervisord.conf