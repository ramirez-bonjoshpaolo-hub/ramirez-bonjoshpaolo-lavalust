FROM php:8.3-apache

RUN docker-php-ext-install pdo_mysql \
    && a2enmod rewrite headers \
    && sed -ri '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf \
    && printf 'ServerName localhost\n' > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername

WORKDIR /var/www/html

COPY . /var/www/html

RUN chmod +x /var/www/html/render-start.sh \
    && chown -R www-data:www-data /var/www/html/runtime \
    && chmod -R 775 /var/www/html/runtime

ENV PORT=10000

EXPOSE 10000

CMD ["/var/www/html/render-start.sh"]
