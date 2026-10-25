# =============================================================================
# SummitGear API — Laravel 12 served by FrankenPHP (PHP 8.3)
# =============================================================================
FROM dunglas/frankenphp:1.13-php8.3-bookworm

# Non-secret defaults only. Secrets (APP_KEY, DB_PASSWORD, ...) are injected
# at runtime through docker-compose / GitHub Secrets, never baked in here.
ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_INTERACTION=1

# PHP extensions required by Laravel + MySQL.
RUN install-php-extensions \
    pdo_mysql \
    bcmath \
    intl \
    opcache \
    pcntl \
    zip

# Composer binary from the official image (pinned version).
COPY --from=composer:2.10 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# 1) Dependencies first: this layer is reused as long as
#    composer.json / composer.lock do not change.
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-progress

# 2) Application source code.
COPY . .

# 3) Optimized autoloader + package discovery, writable dirs, non-root user.
RUN composer dump-autoload --optimize --no-dev \
    && mkdir -p storage/framework/cache/data storage/framework/sessions \
       storage/framework/views storage/logs bootstrap/cache \
       /data/caddy /config/caddy \
    && useradd --create-home --shell /usr/sbin/nologin appuser \
    && chown -R appuser:appuser storage bootstrap/cache /data/caddy /config/caddy \
    && chmod +x docker/entrypoint.sh

USER appuser

EXPOSE 8000

ENTRYPOINT ["/app/docker/entrypoint.sh"]
CMD ["frankenphp", "run", "--config", "/app/docker/Caddyfile"]
