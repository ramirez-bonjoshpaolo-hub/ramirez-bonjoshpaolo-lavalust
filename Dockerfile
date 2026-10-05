FROM php:8.3-apache

# Install PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite headers

# Allow .htaccess overrides
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Copy app files
WORKDIR /var/www/html
COPY . /var/www/html/

# Fix permissions
RUN chmod +x render-start.sh \
    && mkdir -p runtime/cache runtime/logs runtime/session \
    && chown -R www-data:www-data runtime \
    && chmod -R 775 runtime

ENV PORT=10000
EXPOSE 10000
CMD ["/var/www/html/render-start.sh"]
