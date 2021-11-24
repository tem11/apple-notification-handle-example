FROM php:8-fpm

#install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get update \
    && apt-get install -y dnsutils libonig-dev libbz2-dev libpq-dev libzip-dev curl libxml2-dev zip unzip librabbitmq-dev git
RUN docker-php-ext-install pdo_mysql \
    mysqli \
    opcache \
    bcmath \
    sockets \
    zip \
    bz2

# Installation of pecl breaks horribly, need to compile from master
#RUN pecl install amqp
#RUN docker-php-ext-enable amqp


RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

COPY symfony.ini /usr/local/etc/php/conf.d
COPY symfony.ini /usr/local/etc/php/cli/conf.d
COPY xdebug.ini  /usr/local/etc/php/conf.d
COPY symfony.pool.conf /usr/local/etc/php/php-fpm.d/

#install php-cs-fixer
RUN curl -L https://cs.symfony.com/download/php-cs-fixer-v2.phar -o php-cs-fixer
RUN chmod a+x php-cs-fixer
RUN mv php-cs-fixer /usr/local/bin/php-cs-fixer

WORKDIR /var/www/html
