FROM php:8.2-apache

# Copy website files
COPY . /var/www/html/

# Ensure storage and data directories exist
RUN mkdir -p /var/www/html/storage /var/www/html/data

# Give write permissions
RUN chmod -R 777 /var/www/html/storage /var/www/html/data

EXPOSE 80
CMD ["apache2-foreground"]
