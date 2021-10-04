FROM php:7-alpine

# Ext versions
ENV YAML_VER=2.0.0
ENV REDIS_VER=3.1.2
ENV MSGPACK_VER=2.0.2
ENV PHALCON_VER=3.1.2

# CN mirror
RUN sed -i 's/dl-cdn.alpinelinux.org/mirrors.ustc.edu.cn/g' /etc/apk/repositories

# Add depends for runtime
RUN apk add --update \
    yaml

# Add depends for building
RUN apk add --update --virtual builds \
    libc-dev \
    yaml-dev \
    autoconf \
    re2c \
    make \
    gcc \
    g++ \
    gc

# PECL update
RUN pecl channel-update pecl.php.net

# Ext pdo-mysql
RUN docker-php-ext-install pdo pdo_mysql

# Ext yaml
RUN printf "\n" | pecl install yaml-$YAML_VER && docker-php-ext-enable yaml

# Ext redis
RUN pecl install redis-$REDIS_VER && docker-php-ext-enable redis

# Ext msgpack
RUN pecl install msgpack-$MSGPACK_VER && docker-php-ext-enable msgpack

# Ext phalcon
RUN curl -sS -o /tmp/phalcon.tar.gz https://codeload.github.com/phalcon/cphalcon/tar.gz/v$PHALCON_VER \
    && cd /tmp/ \
    && tar zxvf phalcon.tar.gz \
    && cd cphalcon-$PHALCON_VER/build \
    && sh install \
    && docker-php-ext-enable phalcon

# Cleanup
RUN apk del builds && rm -rf /var/cache/apk/* /tmp/*
RUN unset YAML_VER && unset REDIS_VER && unset MSGPACK_VER && unset PHALCON_VER

VOLUME ["/app"]

WORKDIR "/app"

CMD ["php"]