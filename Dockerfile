FROM php:8.2-apache
RUN a2enmod rewrite
WORKDIR /var/www/html
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html/public/data /var/www/html/public/storage
RUN chmod -R 777 /var/www/html/public/data /var/www/html/public/storage
RUN echo "upload_max_filesize=64M\npost_max_size=64M" > /usr/local/etc/php/conf.d/uploads.ini
EXPOSE 80
