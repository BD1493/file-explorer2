# Use official PHP image
FROM php:8.2-apache

# Copy website files to Apache root
COPY . /var/www/html/

# Give write permissions for storage and data
RUN chmod -R 777 /var/www/html/storage /var/www/html/data

# Expose port 80
EXPOSE 80

# Start Apache in foreground
CMD ["apache2-foreground"]
