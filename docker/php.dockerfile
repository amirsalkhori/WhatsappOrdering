FROM php:8.0-fpm-alpine as linux_dependencies

# Install modules
RUN apk upgrade --update && apk --no-cache add \
        $PHPIZE_DEPS \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        icu-dev \
        libpq \
        curl-dev \
        oniguruma-dev \
        unzip \
        postgresql-dev \
        rabbitmq-c \
        rabbitmq-c-dev

FROM linux_dependencies AS php_installation

# Extract PHP source
# Create directory for amqp extension
# Download AMQP master branch files to extension directory
# Install amqp extension using built-in docker binary
RUN docker-php-source extract \
    && mkdir /usr/src/php/ext/amqp \
    && curl -L https://github.com/php-amqp/php-amqp/archive/master.tar.gz | tar -xzC /usr/src/php/ext/amqp --strip-components=1

RUN docker-php-ext-install \
		bcmath \
    intl \
    opcache \
    mbstring \
    amqp

FROM php_installation AS php_extentions_installation
RUN apk add --no-cache mysql-client msmtp perl wget procps shadow libzip libpng libjpeg-turbo libwebp freetype icu
#        amqp-1.10.2 \
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        -j$(nproc) gd \
        pdo \
        pdo_pgsql \
        mysqli \
        pdo_mysql \
    && pecl install \
        redis-5.3.2 \
        igbinary-3.2.1 \
    && docker-php-ext-enable \
        redis \
        amqp \
        igbinary

RUN docker-php-source delete

RUN wget https://getcomposer.org/composer-stable.phar -O /usr/local/bin/composer && chmod +x /usr/local/bin/composer

CMD ["php-fpm"]