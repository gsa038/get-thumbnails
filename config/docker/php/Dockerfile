FROM php:7.4.10-fpm
RUN apt-get update && apt-get upgrade -y \
    && apt-get install -y \
    g++ \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libmagickwand-dev \
    libpng-dev \
    libzip-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && pecl install xdebug && docker-php-ext-enable xdebug \
    && pecl install imagick && docker-php-ext-enable imagick \
    && docker-php-source delete \
    && apt-get remove -y g++ wget \
    && apt-get autoremove --purge -y && apt-get autoclean -y && apt-get clean -y \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /tmp/* /var/tmp/*





