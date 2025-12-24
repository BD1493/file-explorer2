# Use official PHP with Apache
FROM php:8.2-apache

# Set working directory to public
WORKDIR /var/www/html/public

# Copy all project files into public
COPY . /var/www/html/public/

# Enable Apache rewrite module
RUN a2enmod rewrite

# Update Apache config to use public as DocumentRoot
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Create data and storage folders inside public
RUN mkdir -p /var/www/html/public/data /var/www/html/public/storage/users \
    && chown -R www-data:www-data /var/www/html/public/data /var/www/html/public/storage \
    && chmod -R 755 /var/www/html/public/data /var/www/html/public/storage

# Set PHP upload limits
RUN echo "upload_max_filesize=50M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size=50M" >> /usr/local/etc/php/conf.d/uploads.ini

# Expose port 80
EXPOSE 80

# Start Apache in foreground
CMD ["apache2-foreground"]
