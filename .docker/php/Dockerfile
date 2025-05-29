FROM php:8.3-apache  AS my_hci_php_base_8_3_apache

RUN pecl install xdebug-3.4.2 && docker-php-ext-enable xdebug


ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions bcmath bz2 gd gettext exif gmp intl ioncube_loader mysqli pcntl pdo_mysql pdo_pgsql pgsql redis soap sockets tidy zip



FROM my_hci_php_base_8_3_apache AS my_hbc_php_apache

RUN a2enmod rewrite
RUN a2enmod headers


RUN echo "memory_limit=512M" > $PHP_INI_DIR/conf.d/memory-limit.ini
RUN echo "upload_max_filesize=513M" > $PHP_INI_DIR/conf.d/upload-max-filesize.ini
RUN echo "post_max_size=1G" > $PHP_INI_DIR/conf.d/post-max-size.ini
RUN echo "error_reporting=E_ALL & ~E_DEPRECATED & ~E_STRICT" > $PHP_INI_DIR/conf.d/error-reporting.ini
RUN echo "\
         xdebug.mode=debug,develop \n\
         xdebug.client_host=host.docker.internal \n\
         xdebug.idekey=PHPSTORM" >> $PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini


RUN sed -ri -e "s!/var/www/html!/var/www/html/public!g" /etc/apache2/sites-available/*.conf
RUN sed -ri -e "s!/var/www/html!/var/www/html/public!g" /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf


FROM my_hbc_php_apache AS my_hbc_php_apache_composer

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

FROM my_hbc_php_apache_composer AS my_hbc_install_tools

RUN  apt-get -y update \
     && apt-get -y autoremove \
     && apt-get clean \
     && apt-get install -y \
     nano vim iputils-ping unzip fd-find zip git curl default-mysql-client gpg wget




FROM my_hbc_install_tools AS my_hbc_add_eza

# add era for convenience

RUN mkdir -p /etc/apt/keyrings

RUN wget -qO- https://raw.githubusercontent.com/eza-community/eza/main/deb.asc | gpg --dearmor -o /etc/apt/keyrings/gierens.gpg

RUN echo "deb [signed-by=/etc/apt/keyrings/gierens.gpg] http://deb.gierens.de stable main" |  tee /etc/apt/sources.list.d/gierens.list

RUN chmod 644 /etc/apt/keyrings/gierens.gpg /etc/apt/sources.list.d/gierens.list

RUN  apt-get -y update \
    && apt-get install -y eza


# change change the level for lll
RUN echo "\nalias ls='eza'\n\
alias ll='eza -aglF --long --header --smart-group --git'\n\
alias lll='eza -aglF --long --header --smart-group --git --tree --level=2'\n" >> /root/.bashrc
