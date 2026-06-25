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
        exif

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --no-autoloader --prefer-dist

COPY package.json package-lock.json* ./
RUN npm ci

COPY . .

RUN composer dump-autoload --optimize --no-dev \
    && npm run build \
    && php artisan storage:link || true \
    && php artisan filament:assets \
    && rm -rf node_modules

EXPOSE 8000

CMD php artisan config:clear; \
    php artisan view:clear; \
    php artisan cache:clear; \
    php artisan migrate --force --no-interaction || echo 'migrate failed, starting server anyway'; \
    exec php artisan serve --host 0.0.0.0 --port "${PORT:-8000}"
