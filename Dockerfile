FROM php:8.4-cli-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        curl \
        ca-certificates \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libicu-dev \
        libonig-dev \
        libexif-dev \
        zlib1g-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        intl \
        zip \
        gd \
        pdo_mysql \
        bcmath \
        mbstring \
        exif \
        opcache

RUN { \
        echo 'opcache.enable=1'; \
        echo 'opcache.enable_cli=1'; \
        echo 'opcache.memory_consumption=192'; \
        echo 'opcache.interned_strings_buffer=16'; \
        echo 'opcache.max_accelerated_files=20000'; \
        echo 'opcache.validate_timestamps=0'; \
        echo 'opcache.jit=tracing'; \
        echo 'opcache.jit_buffer_size=64M'; \
        echo 'realpath_cache_size=4096K'; \
        echo 'realpath_cache_ttl=600'; \
    } > /usr/local/etc/php/conf.d/zz-opcache.ini \
    && { \
        echo 'memory_limit=512M'; \
        echo 'max_execution_time=120'; \
    } > /usr/local/etc/php/conf.d/zz-app.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*

ENV PHP_CLI_SERVER_WORKERS=4

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --no-autoloader --prefer-dist

COPY package.json package-lock.json* ./
RUN npm install --no-audit --no-fund

COPY . .

RUN composer dump-autoload --optimize --no-dev \
    && npm run build \
    && php artisan storage:link || true \
    && php artisan filament:assets \
    && php artisan view:cache \
    && php artisan event:cache \
    && (php artisan icons:cache || true) \
    && (php artisan filament:cache-components || true) \
    && rm -rf node_modules

EXPOSE 8000

CMD php artisan config:cache; \
    php artisan route:cache; \
    php artisan migrate --force --no-interaction || echo 'migrate failed, starting server anyway'; \
    exec php artisan serve --host 0.0.0.0 --port "${PORT:-8000}"
