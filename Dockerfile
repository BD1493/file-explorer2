FROM php:8.2-apache
RUN a2enmod rewrite
WORKDIR /var/www/html
COPY . /var/www/html/
# Critical: Make data and storage writable by the web server
RUN chown -R www-data:www-data /var/www/html/public/data /var/www/html/public/storage
RUN chmod -R 777 /var/www/html/public/data /var/www/html/public/storage
EXPOSE 80
