FROM php:8.4-fpm

WORKDIR /app

RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    locales \
    jpegoptim \
    optipng \
    pngquant \
    gifsicle \
    vim \
    unzip \
    git \
    curl \
    wget \
    libzip-dev \
    libssl-dev \
    libssh2-1-dev \
    ca-certificates

RUN docker-php-ext-install -j$(nproc) \
    bcmath \
    mbstring \
    pdo \
    pdo_mysql \
    zip

RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) gd

RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN mkdir -p \
        bootstrap/cache \
        storage/app/private \
        storage/app/public \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        storage/ssh && \
    composer install --no-interaction --no-dev --prefer-dist --no-scripts && \
    composer dump-autoload --no-interaction --optimize

COPY docker/entrypoint.sh /usr/local/bin/docker-entrypoint

EXPOSE 8000

ENTRYPOINT ["sh", "/usr/local/bin/docker-entrypoint"]
CMD php artisan serve --host=0.0.0.0 --port=8000
