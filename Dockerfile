FROM php:8.2-apache

# Enable mod_rewrite
RUN a2enmod rewrite

# Set working directory to root
WORKDIR /var/www/html

# Copy all files to the container
COPY . /var/www/html/

# Configure Upload Limits
RUN echo "upload_max_filesize=50M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size=50M" >> /usr/local/etc/php/conf.d/uploads.ini

# PERMISSIONS:
# Publicly accessible data and storage, full 777 permissions
RUN chown -R www-data:www-data /var/www/html/public/data /var/www/html/public/storage \
    && chmod -R 777 /var/www/html/public/data /var/www/html/public/storage

# Expose port 80
EXPOSE 80
