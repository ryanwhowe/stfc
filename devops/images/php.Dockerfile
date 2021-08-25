FROM php:7.4-fpm-buster

RUN apt-get update \
  && apt-get install -y --no-install-recommends \
    git \
    unzip \
    zlib1g-dev \
    libxml2-dev \
    libzip-dev \
    default-mysql-client \
    smbclient libsmbclient-dev \
    libmagickwand-dev \
  && docker-php-ext-install \
    zip \
    intl \
    mysqli \
    pdo pdo_mysql \
    opcache \
    sockets \
    pcntl

RUN yes | pecl install smbclient && docker-php-ext-enable smbclient && yes | pecl install imagick && docker-php-ext-enable imagick

RUN yes | pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini

RUN pecl install apcu && docker-php-ext-enable apcu \
    && echo "apc.enable_cli=1" >> /usr/local/etc/php/php.ini \
    && echo "apc.enable=1" >> /usr/local/etc/php/php.ini

RUN curl -sS https://get.symfony.com/cli/installer | bash && mv /root/.symfony/bin/symfony /usr/local/bin/symfony

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
ENV COMPOSER_MEMORY_LIMIT=-1
ENV COMPOSER_CACHE_DIR=/tmp

# Add useful scripts
COPY php/scripts/*.sh /tmp/scripts/

# Make all scripts executable
RUN chmod +x /tmp/scripts/*.sh

WORKDIR /var/www/symfony/
