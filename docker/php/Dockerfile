FROM php:8.3-apache

RUN docker-php-ext-install pdo_mysql \
    && a2enmod rewrite \
    && echo "date.timezone = America/Sao_Paulo" > "$PHP_INI_DIR/conf.d/fuso.ini"

# O site sai de public/: o resto do projeto (src, views, .env) não fica acessível pelo navegador
ENV APACHE_DOCUMENT_ROOT=/var/www/painel/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf

WORKDIR /var/www/painel
