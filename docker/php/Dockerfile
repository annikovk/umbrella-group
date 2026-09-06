# PHP 8.3 + Apache с mod_php — как на проде (Plesk /opt/plesk/php/8.3).
FROM php:8.3-apache

# Расширения под WP 6.5 и плагины сайта. imagick намеренно нет: сборка тянет
# пол-гигабайта зависимостей, а WP прекрасно обходится GD.
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        libpng-dev libjpeg62-turbo-dev libwebp-dev libfreetype6-dev \
        libzip-dev libicu-dev libonig-dev \
        mariadb-client less; \
    docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype; \
    docker-php-ext-install -j"$(nproc)" gd zip intl exif mysqli opcache bcmath; \
    rm -rf /var/lib/apt/lists/*

# wp-cli: им скрипт создаёт локального админа и им же удобно лазить в базу.
RUN curl -fsSL -o /usr/local/bin/wp \
        https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar; \
    chmod +x /usr/local/bin/wp

# В образе APACHE_RUN_USER жёстко прописан www-data — переписываем на форму с
# дефолтом, иначе compose не сможет подставить uid хозяина машины.
RUN sed -ri 's/^export APACHE_RUN_(USER|GROUP)=.*$/export APACHE_RUN_\1=${APACHE_RUN_\1:-www-data}/' \
        /etc/apache2/envvars

COPY php/php.ini /usr/local/etc/php/conf.d/zz-local.ini
COPY apache/taxlab.conf /etc/apache2/sites-available/taxlab.conf

RUN set -eux; \
    a2enmod rewrite headers expires; \
    a2dissite 000-default; \
    a2ensite taxlab

WORKDIR /var/www/vhosts/taxlab.ru/httpdocs
