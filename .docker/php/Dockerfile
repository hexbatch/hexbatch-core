FROM php:8.5.7-fpm-trixie  AS my_hci_php_base_8_7_fpm

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/


RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions  \
bcmath bz2 gd gettext exif gmp intl ioncube_loader mysqli pcntl pdo_mysql pdo_pgsql pgsql soap sockets tidy zip

FROM my_hci_php_base_8_7_fpm AS my_hci_php_extensions_imagick
RUN install-php-extensions imagick

FROM my_hci_php_extensions_imagick AS my_hci_php_redis
RUN install-php-extensions redis-6.3.0


FROM my_hci_php_redis AS my_hci_php_base_xdebug

RUN curl -fL --output /tmp/pie.phar https://github.com/php/pie/releases/latest/download/pie.phar \
  &&  mv /tmp/pie.phar /usr/local/bin/pie \
  &&  chmod +x /usr/local/bin/pie

RUN pie install xdebug/xdebug  && docker-php-ext-enable xdebug


FROM my_hci_php_base_xdebug AS my_hci_php_config



RUN echo "memory_limit=512M" > $PHP_INI_DIR/conf.d/memory-limit.ini
RUN echo "upload_max_filesize=513M" > $PHP_INI_DIR/conf.d/upload-max-filesize.ini
RUN echo "post_max_size=1G" > $PHP_INI_DIR/conf.d/post-max-size.ini
RUN echo "error_reporting=E_ALL & ~E_DEPRECATED & ~E_STRICT" > $PHP_INI_DIR/conf.d/error-reporting.ini
RUN echo "\
         xdebug.mode=debug,develop \n\
         xdebug.client_host=host.docker.internal \n\
         xdebug.idekey=PHPSTORM" >> $PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini


FROM my_hci_php_config AS my_hci_php_composer

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

FROM my_hci_php_composer AS my_hci_install_tools

RUN  apt-get -y update \
     && apt-get -y autoremove \
     && apt-get clean \
     && apt-get install -y \
     nano vim iputils-ping unzip fd-find zip git curl default-mysql-client gpg wget ffmpeg

# add era for convenience

RUN mkdir -p /etc/apt/keyrings \
    && wget -qO- https://raw.githubusercontent.com/eza-community/eza/main/deb.asc | gpg --dearmor -o /etc/apt/keyrings/gierens.gpg \
    && echo "deb [signed-by=/etc/apt/keyrings/gierens.gpg] https://deb.gierens.de stable main" |  tee /etc/apt/sources.list.d/gierens.list \
    && chmod 644 /etc/apt/keyrings/gierens.gpg /etc/apt/sources.list.d/gierens.list \
    && apt -y update \
    && apt install -y eza


FROM my_hci_install_tools AS my_hci_add_aliases

# change change the level for lll
RUN echo "\nalias ls='eza'\n\
alias ll='eza -aglF --long --header --smart-group --git'\n\
alias lll='eza -aglF --long --header --smart-group --git --tree --level=2'\n" >> /root/.bashrc




