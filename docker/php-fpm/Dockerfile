FROM php:7.2.24-fpm

MAINTAINER Salamatov Dmitry <dimasalam@yandex.ru>

WORKDIR /var/www
ARG APP_DIR=/var/www

# Install php-extentions and apk packages
RUN set -xe \
    && apt-get update \
    && apt-get install -y \
        nano \
	    zlib1g-dev \
        libmcrypt-dev \
        libxml2-dev \
        libicu-dev \
        libmariadb-dev \
        libc-client-dev \
        libkrb5-dev \
        libpng-dev \
        libcurl4-gnutls-dev \
        git \
        apt-utils \
        ssh \
        libssl-dev \
        locales \
        unzip \
        zlib1g-dev \
    --no-install-recommends \
    && docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-install \
        intl \
        pdo_mysql \
        pcntl \
        curl \
        mbstring \
        dom \
        simplexml \
        opcache \
        zip \
        soap \
        xml \
        bcmath \
        imap \
        gd \
        iconv \
        json \
        sockets \
        mysqli \
    && pecl install -o -f \
	    mcrypt-1.0.1 \
	&& pecl channel-update pecl.php.net \
    && rm -rf /tmp/pear \
    && rm -r /var/lib/apt/lists/* \
    && sed -i -e 's/# en_US.UTF-8 UTF-8/en_US.UTF-8 UTF-8/' /etc/locale.gen  \
    && sed -i -e 's/# ru_RU.UTF-8 UTF-8/ru_RU.UTF-8 UTF-8/' /etc/locale.gen  \
    && locale-gen \
    && ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

ENV LANG ru_RU.UTF-8
ENV LANGUAGE ru_RU:ru
ENV LC_ALL ru_RU.UTF-8

# Add php configs
COPY docker/php-fpm/config/php.ini /usr/local/etc/php/
COPY docker/php-fpm/config/php-fpm.conf /usr/local/etc/php-fpm.d/

# Install composer
## Allow Composer to be run as root
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_PROCESS_TIMEOUT 1800
## Install composer to /usr/bin
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
### Для ускорения работы composer
###RUN composer global require hirak/prestissimo

RUN  apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    && mkdir -p ${APP_DIR}/dev/public
