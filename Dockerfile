FROM php:8-fpm-alpine

# install nginx manually
RUN apk --no-cache add shadow

RUN apk update && \
    apk add --no-cache \
        $PHPIZE_DEPS \
        nginx \
        git \
        openssl-dev \
        zlib-dev \
        libzip-dev \
        curl \
        gnupg \
        bash

RUN docker-php-ext-configure sockets
RUN docker-php-ext-install zip
RUN docker-php-ext-install sockets
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN docker-php-ext-install bcmath

#install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN mkdir /var/cache/nginx

# implement changes required to run NGINX as an unprivileged user
RUN  mkdir -p /etc/nginx && \
     mkdir -p /var/www/html && \
     mkdir -p /etc/nginx/sites-available/ && \
     mkdir -p /etc/nginx/sites-enabled/

COPY ./api /var/www/html
COPY ./docker-resources/nginx/nginx-site.conf /etc/nginx/conf.d/default.conf
COPY ./docker-resources/nginx/nginx.conf /etc/nginx/nginx.conf


RUN mkdir -p /var/www/html/var

RUN usermod -u 1001 nginx \
    && chown -R 1001:0 /var/cache/nginx \
    && chown -R 1001:0 /var/www/html/var \
    && chmod -R g+w /var/cache/nginx \
    && chmod -R g+w /var/www/html/var

# forward request and error logs to docker log collector
RUN ln -sf /dev/stdout /var/log/nginx/access.log && ln -sf /dev/stderr /var/log/nginx/error.log

WORKDIR /var/www/html

STOPSIGNAL SIGTERM
USER 1001

EXPOSE 8080

CMD /bin/bash -c "nginx && php-fpm"