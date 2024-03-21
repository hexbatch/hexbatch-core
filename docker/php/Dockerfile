FROM phpstorm/php-apache:8.2-xdebug3.2 as my_php_base_8_2_xdebug_3_2

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions gd mysqli pgsql zip intl bcmath pdo_mysql pdo_pgsql bz2 gettext gmp exif redis rdkafka soap sockets tidy ioncube_loader

RUN  apt-get -y update \
     && apt-get -y autoremove \
     && apt-get clean \
     && apt-get install -y \
     unzip nano vim iputils-ping


FROM my_php_base_8_2_xdebug_3_2 as my_gk_php_apache

RUN a2enmod rewrite
RUN a2enmod headers


RUN echo "memory_limit=512M" > $PHP_INI_DIR/conf.d/memory-limit.ini
RUN echo "upload_max_filesize=513M" > $PHP_INI_DIR/conf.d/upload-max-filesize.ini
RUN echo "post_max_size=1G" > $PHP_INI_DIR/conf.d/post-max-size.ini
RUN echo "error_reporting=E_ALL & ~E_DEPRECATED & ~E_STRICT" > $PHP_INI_DIR/conf.d/error-reporting.ini

RUN sed -ri -e "s!/var/www/html!/var/www/html/public!g" /etc/apache2/sites-available/*.conf
RUN sed -ri -e "s!/var/www/html!/var/www/html/public!g" /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf



