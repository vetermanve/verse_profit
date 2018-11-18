#
#--------------------------------------------------------------------------
# Image Setup
# $REF: $
#--------------------------------------------------------------------------
#
# Arguments:
#     PUID                 Индетификатор пользователя. Default "1000"
#     PHP_INSTALL_XDEBUG   Включить расшимрения xdebug в конфиг php (false|true). Default "false"
#     PHP_XDEBUG_PORT      Порт для xdebug. Default "9000"
#

FROM php:7.2-fpm-alpine

#
#--------------------------------------------------------------------------
# Core Software's Installation
#--------------------------------------------------------------------------
#

RUN apk --no-cache --update add       \
                    shadow            \
                    libxml2-dev       \
                    g++               \
                    make              \
                    curl-dev          \
                    libzip-dev        \
                    icu-dev           \
                    libpng-dev        \
                    postgresql-dev    \
                    autoconf          \
                    libjpeg-turbo-dev \
                    rabbitmq-c-dev    \
                    libmemcached-dev  \
                    freetype-dev      \
                    imagemagick-dev   \
        && rm -rf /tmp/*              \
        && rm -rf /var/cache/apk/*
# END Core Software's Installation  -------------

#
#--------------------------------------------------------------------------
# PHP Installation
#--------------------------------------------------------------------------
#
RUN echo "Installing extensions"        && \
    pecl update-channels                && \
  docker-php-ext-configure zip             \
        --with-libzip                   && \
  docker-php-ext-configure intl

RUN docker-php-ext-install -j6 \
    zip           \
    intl          \
    bcmath        \
    opcache       \
    pcntl         \
    pgsql         \
    pdo_pgsql     \
    sockets


RUN pecl install -o -f amqp             && \
        docker-php-ext-enable amqp      && \
    pecl install -o -f memcached        && \
        docker-php-ext-enable memcached && \
    pecl install -o -f imagick          && \
        docker-php-ext-enable imagick   && \
    pecl install -o -f redis            && \
        docker-php-ext-enable redis     && \
    pecl install -o -f xdebug           && \
#    pecl install -o -f swoole    && docker-php-ext-enable swoole    && \
#
#   Cleaning up
    rm -rf /tmp/* /var/tmp/*
# END PHP Installation --------------------------


#
#--------------------------------------------------------------------------
# PHP Configuration AND COMPOSER
#--------------------------------------------------------------------------
#

ADD ./50-custom.ini         /usr/local/etc/php/conf.d/

# Installing Composer
RUN printf "\nInstalling Composer\n\n"; \
    EXPECTED_SIGNATURE=$(wget -q -O - https://composer.github.io/installer.sig); \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"; \
    ACTUAL_SIGNATURE=$(php -r "echo hash_file('SHA384', 'composer-setup.php');"); \
    if [ "$EXPECTED_SIGNATURE" != "$ACTUAL_SIGNATURE" ]; \
    then \
      >&2 echo 'ERROR: Invalid installer signature'; \
      exit 1; \
    else \
      php composer-setup.php --install-dir=/usr/local/bin --filename=composer; \
    fi; \
    rm composer-setup.php

RUN composer global require "hirak/prestissimo" && \
    rm -rf /root/.composer/cache

#
#--------------------------------------------------------------------------
# User www-data Configuration
#--------------------------------------------------------------------------
#
RUN cp -r /root/.composer /home/www-data/ \
    && chown -R www-data:www-data /home/www-data \
    && usermod -d /home/www-data www-data

ARG PUID=1000
ENV PUID ${PUID}
RUN if [ -n "${PUID}" ] && [ "${PUID%:*}" != 0 ]; then \
  usermod -u ${PUID} www-data >/dev/null 2>&1 \
;fi

# END PHP Configuration --------------------------


#####################################
# pgsql client
#####################################
#RUN wget -q https://www.postgresql.org/media/keys/ACCC4CF8.asc -O - | apt-key add - && \
#    sh -c 'echo "deb http://apt.postgresql.org/pub/repos/apt/ `lsb_release -cs`-pgdg main" >> /etc/apt/sources.list.d/pgdg.list' && \
#    apt-get -qy update && apt-get -qy install --no-install-recommends \
#        postgresql-client \
#    && apt-get autoremove -qy && apt-get clean -qy && rm -rf /tmp/* /var/lib/apt/lists/*
# END pgsql client --------------------------

#
#--------------------------------------------------------------------------
# SSH and GIT Installation
#--------------------------------------------------------------------------
#
#ARG INSTALL_GIT=true
#
#RUN if [ ${INSTALL_GIT} = true ]; then \
#    apt-get -qy update && apt-get -qy install --no-install-recommends libssl-dev tcl-dev gettext git && \
#    apt-get autoremove -qy && apt-get clean -qy && rm -rf /tmp/* /var/tmp/* /var/lib/apt/lists/* && \
#    git --version \
#;fi


ARG PHP_INSTALL_XDEBUG=false

RUN if [ ${PHP_INSTALL_XDEBUG} = true ]; then \
    docker-php-ext-enable xdebug \
;fi


ARG PHP_XDEBUG_PORT=false

RUN if [ ${PHP_XDEBUG_PORT} != false ]; then \
    echo "xdebug.remote_port=${PHP_XDEBUG_PORT}" >> /usr/local/etc/php/conf.d/50-custom.ini \
;fi

#
#--------------------------------------------------------------------------
# Clear code
#--------------------------------------------------------------------------
#

ENV PATH /var/www:$PATH

WORKDIR /var/www

CMD ["php-fpm", "--allow-to-run-as-root", "--nodaemonize"]
