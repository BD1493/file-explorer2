# Use official PHP Apache image
FROM php:8.2-apache

# Enable mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html/public

# Copy project files
COPY . /var/www/html/public/

# Create necessary directories if they don't exist
RUN mkdir -p /var/www/html/public/data /var/www/html/public/storage/users

# Set permissions so Apache/PHP can write
RUN chown -R www-data:www-data /var/www/html/public/data /var/www/html/public/storage \
    && chmod -R 755 /var/www/html/public/data /var/www/html/public/storage

# Increase upload size
RUN echo "upload_max_filesize=50M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size=50M" >> /usr/local/etc/php/conf.d/uploads.ini

EXPOSE 80

CMD ["apache2-foreground"]
