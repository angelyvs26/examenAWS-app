FROM php:8.3-apache
RUN docker-php-ext-install mysqli
COPY index.php /var/www/html/
EXPOSE 80
