FROM php:8.2-apache
RUN a2enmod rewrite
WORKDIR /var/www/html
COPY . /var/www/html/
# Critical: Ensure web server can write to data/storage
RUN chown -R www-data:www-data /var/www/html/public/data /var/www/html/public/storage
RUN chmod -R 777 /var/www/html/public/data /var/www/html/public/storage
# PHP Config to allow larger uploads
RUN echo "upload_max_filesize=50M" > /usr/local/etc/php/conf.d/uploads.ini
RUN echo "post_max_size=50M" >> /usr/local/etc/php/conf.d/uploads.ini
EXPOSE 80
