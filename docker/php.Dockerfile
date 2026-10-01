FROM php:8.2-bookworm

RUN apt-get update -qq \
    && apt-get install -qq git libzip-dev unzip \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
    zip \
    > /dev/null

RUN pecl install xdebug > /dev/null \
    && docker-php-ext-enable xdebug > /dev/null

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

CMD ['bash']
