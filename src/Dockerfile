FROM php:8.4-fpm

# PHP upload & memory limits
RUN echo "upload_max_filesize=50M" > /usr/local/etc/php/conf.d/uploads.ini \
 && echo "post_max_size=50M" >> /usr/local/etc/php/conf.d/uploads.ini \
 && echo "memory_limit=256M" >> /usr/local/etc/php/conf.d/uploads.ini

# Install system packages (ADD libicu-dev)
RUN apt-get update && apt-get install -y \
    nginx \
    procps \
    curl \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libmariadb-dev \
    libicu-dev \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        zip \
        intl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy source
COPY . .

# Install PHP deps
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy nginx config
COPY nginx/default.conf /etc/nginx/sites-available/default

EXPOSE 8080

CMD ["sh", "-c", "php-fpm & nginx -g 'daemon off;'"]
