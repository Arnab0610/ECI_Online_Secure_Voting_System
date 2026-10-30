FROM php:8.1-apache

# Install extensions needed for MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy project files to Apache public directory
COPY . /var/www/html/

# Expose port 80
EXPOSE 80