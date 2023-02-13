FROM php:7.1-apache
ENV LANG C.UTF-8

RUN apt-get update \
  && apt-get install -y git unzip zlib1g-dev libpq-dev unzip git wget libpng-dev libmcrypt-dev libfreetype6-dev libjpeg62-turbo-dev ffmpeg imagemagick gnupg \
  && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
  && docker-php-ext-install zip pdo_mysql mysqli mbstring gd iconv mcrypt -j$(nproc) \
  && docker-php-ext-enable mysqli

RUN echo 'CipherString = DEFAULT@SECLEVEL=1' >> /etc/ssl/openssl.cnf

RUN pecl install xdebug-2.5.5 \
  && docker-php-ext-enable xdebug
RUN a2enmod rewrite
RUN a2enmod ssl
RUN a2ensite default-ssl

ENV COMPOSER_ALLOW_SUPERUSER 1
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

ENV COMPOSER_HOME ~/.composer
RUN composer global config repositories.packagist composer https://packagist.jp
RUN composer global require hirak/prestissimo

RUN ["apt-get", "install", "-y", "vim"]

RUN curl -sL https://deb.nodesource.com/setup_11.x | bash -
RUN apt-get install -y nodejs
RUN npm install npm@latest -g

WORKDIR /var/www/html
VOLUME /var/www/html
ADD . /var/www/html

RUN docker-php-ext-install sockets

RUN composer install
RUN npm install
