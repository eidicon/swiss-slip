FROM php:7.1-fpm

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libxml2-dev \
        libpng-dev \
        libicu-dev \
        autoconf \ 
        pkg-config \ 
        libssl-dev

RUN docker-php-ext-install iconv mcrypt mbstring bcmath json ctype iconv posix intl \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-configure xml --with-libxml-dir==/usr/ \
    && docker-php-ext-install gd xml \
    && docker-php-ext-install zip \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && pecl install apcu \
    && docker-php-ext-enable --ini-name 01-apcu.ini apcu \
    && pecl install -f apcu_bc \
    && docker-php-ext-enable --ini-name 02-apc.ini apc \
    && docker-php-ext-enable opcache 

    
