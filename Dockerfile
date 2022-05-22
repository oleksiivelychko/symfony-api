FROM php:8.1-cli

ENV PHP_IDE_CONFIG="serverName=dockerHost"
ENV LOG_DIR="/app/var/log"

ADD https://raw.githubusercontent.com/oleksiivelychko/laravel-dashboard/main/.docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN echo 'deb [trusted=yes] https://repo.symfony.com/apt/ /' | tee /etc/apt/sources.list.d/symfony-cli.list

RUN apt-get update && apt-get install -y \
    libonig-dev \
    libmcrypt-dev \
    libpq-dev \
    git \
    symfony-cli

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring

RUN pecl install xdebug-3.1.4
RUN docker-php-ext-enable xdebug

WORKDIR /app
COPY . /app

RUN composer install

EXPOSE 8000
CMD symfony server:start --port=8000