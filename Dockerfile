FROM php:8.3-fpm

# Install necessary extensions for Laravel
RUN apt-get update && \
    apt-get install -y libzip-dev zip unzip && \
    docker-php-ext-install zip

# Install PostgreSQL PHP extension
RUN apt-get install -y libpq-dev && \
    docker-php-ext-install pdo_pgsql

# Download and install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set up Nginx as the web server
RUN apt-get update && apt-get install -y nginx
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf

# Set the working directory to /var/www/html
WORKDIR /var/www/html

# Copy the Laravel app files to the working directory
COPY . /var/www/html

COPY ./docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./docker/php/php.ini-production /usr/local/etc/php/php.ini

RUN composer update --ignore-platform-req=ext-sockets --no-plugins --no-scripts
# php ini dir -> /usr/local/etc/php

RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html/storage
RUN chmod -R 755 /var/www/html/public

# Expose port 80 for Nginx
EXPOSE 80
RUN nginx -t
# Start Nginx and PHP-FPM
CMD service nginx start && php-fpm
