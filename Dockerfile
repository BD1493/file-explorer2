FROM php:8.2-apache

# Enable mod_rewrite
RUN a2enmod rewrite

# Set Apache DocumentRoot to project root (since index.php is in main directory)
RUN sed -i 's|/var/www/html|/var/www/html|g' /etc/apache2/sites-available/000-default.conf

# Copy all project files
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Create data/storage directories inside public and set permissions
RUN mkdir -p public/data public/storage/users \
    && chown -R www-data:www-data public/data public/storage \
    && chmod -R 755 public/data public/storage

# Increase PHP upload limits
RUN echo "upload_max_filesize=50M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size=50M" >> /usr/local/etc/php/conf.d/uploads.ini

EXPOSE 80
CMD ["apache2-foreground"]
