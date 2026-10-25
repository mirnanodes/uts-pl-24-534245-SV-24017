#!/bin/sh
# Container entrypoint: prepare Laravel, then hand over to FrankenPHP.
set -e

if [ -z "${APP_KEY}" ]; then
    echo "[entrypoint] ERROR: APP_KEY is empty. Fill APP_KEY in the root .env file (see .env.example)." >&2
    exit 1
fi

# Retry migrations a few times in case the database is still warming up.
attempt=1
until php artisan migrate --force --no-interaction; do
    if [ "${attempt}" -ge 10 ]; then
        echo "[entrypoint] ERROR: database is not reachable after ${attempt} attempts." >&2
        exit 1
    fi
    echo "[entrypoint] Database not ready yet (attempt ${attempt}), retrying in 3s..."
    attempt=$((attempt + 1))
    sleep 3
done

# Seeders are idempotent: they only insert data when it does not exist yet.
php artisan db:seed --force --no-interaction

# Cache config, routes and views for production performance.
php artisan optimize

echo "[entrypoint] Laravel is ready, starting: $*"
exec "$@"
