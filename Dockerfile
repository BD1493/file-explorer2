FROM php:8.2-apache

# Enable mod_rewrite
RUN a2enmod rewrite

# Copy all files
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/public

# Set permissions for storage and data
RUN mkdir -p public/data public/storage/users \
    && chown -R www-data:www-data public/data public/storage \
    && chmod -R 755 public/data public/storage

# Increase PHP upload size
RUN echo "upload_max_filesize=50M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size=50M" >> /usr/local/etc/php/conf.d/uploads.ini

EXPOSE 80

CMD ["apache2-foreground"]
