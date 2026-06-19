FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    ffmpeg \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev

RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg

RUN docker-php-ext-install \
        pdo_mysql \
        bcmath \
        exif \
        pcntl \
        zip \
        gd

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/backend

CMD ["php-fpm"]