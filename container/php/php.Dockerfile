FROM php:7.1.33-fpm-buster

RUN apt-get update && \
    apt-get install -y zip apt-utils re2c g++ zlib1g zlib1g-dbg zlib1g-dev zlibc openssl libssl-dev

RUN apt-get install -y cron make nano

RUN apt-get install -y --no-install-recommends libfreetype6-dev libjpeg-dev libpng-dev libwebp-dev libzip-dev  \
    # gd
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    # gmp
    && apt-get install -y --no-install-recommends libgmp-dev \
    && docker-php-ext-install gmp
    # pdo_mysql
RUN docker-php-ext-install pdo_mysql \
    # opcache
    && docker-php-ext-enable opcache \
    # zip
    && docker-php-ext-install zip \
    # bcmath
    && docker-php-ext-install bcmath \
    # mysql
    && docker-php-ext-install mysqli \
    # exif
    && docker-php-ext-install exif
    # imagick
#RUN apt-get install -y libmagickwand-dev --no-install-recommends

#RUN pecl install imagick && docker-php-ext-enable imagick
    # xdebug
# RUN pecl install xdebug
# RUN docker-php-ext-enable xdebug

#RUN pecl install redis
#RUN docker-php-ext-enable redis

# install openswoole
#RUN apt-get install libcurl4-openssl-dev
#RUN pecl install -D 'enable-sockets="no" enable-openssl="yes" enable-http2="yes" enable-mysqlnd="yes" enable-hook-curl="yes" enable-cares="yes" with-postgres="no"' openswoole
#RUN docker-php-ext-enable openswoole

###############################
# Supervisor                  #
###############################
RUN apt-get install supervisor -y
# Copy supervisord config for horizon
COPY php/supervisor.conf /etc/supervisor/conf.d/

RUN curl --silent --show-error https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Copy cronjob file to the cron.d directory
COPY php/cronjob /etc/cron.d/cronjob

# Give execution rights on the cron job
RUN chmod 0644 /etc/cron.d/cronjob

# Apply cron job
RUN crontab /etc/cron.d/cronjob

# Create the log file to be able to run tail
RUN touch /var/log/cron.log

#RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
#    apt-get install -y nodejs

    # clean up
RUN apt-get autoclean -y \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /tmp/pear/

# RUN laravel octane
#CMD php artisan octane:start --watch --server="swoole" --host="0.0.0.0"

#HEALTHCHECK --start-period=5s --interval=2s --timeout=5s --retries=8 CMD php artisan octane:status --server="swoole"|| exit 1

# Run the command on container startup
CMD php-fpm && service cron start && crontab /etc/cron.d/cronjob && tail -f /var/log/cron.log
