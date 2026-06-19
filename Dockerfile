FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    ffmpeg \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        bcmath \
        exif \
        pcntl \
        zip \
        gd

RUN sed -i 's|listen = 127.0.0.1:9000|listen = 9000|' \
    /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www/backend

CMD ["php artisan migrate --force"]
CMD ["php artisan db:seed --force"]
CMD ["php artisan storage:link"]

CMD ["php-fpm"]