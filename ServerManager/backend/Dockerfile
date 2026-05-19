FROM php:8.2-fpm

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

RUN composer install --no-interaction --no-dev --prefer-dist

RUN php artisan key:generate || true
RUN php artisan config:cache || true

EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=8000
