FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    unzip \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy Laravel code into the container
COPY . /var/www/html

# Copy Nginx configuration
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

# Permissions for Laravel
RUN chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose HTTP port
EXPOSE 80

# Start Nginx + PHP-FPM
CMD service nginx start && php-fpm
