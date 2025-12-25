FROM php:8.2-apache
RUN a2enmod rewrite
WORKDIR /var/www/html
COPY . /var/www/html/
RUN echo "upload_max_filesize=50M\npost_max_size=50M" > /usr/local/etc/php/conf.d/uploads.ini
RUN chown -R www-data:www-data /var/www/html/public/data /var/www/html/public/storage && chmod -R 777 /var/www/html/public/data /var/www/html/public/storage
EXPOSE 80
