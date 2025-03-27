FROM php:8.3-bullseye

RUN apt-get update -qq \
    && apt-get install -qq git libzip-dev unzip \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
    zip \
    > /dev/null

RUN pecl install xdebug > /dev/null \
    && docker-php-ext-enable xdebug > /dev/null

CMD ["bash"]
