FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    sudo \
    curl \
    libpng-dev \
    libwebp-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    supervisor \
    mysql-client \
    mariadb-connector-c-dev \
    nano

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp
RUN docker-php-ext-install pdo pdo_mysql gd zip bcmath

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory contents
COPY . .

# Fix permissions and create necessary directories
RUN mkdir -p storage/logs bootstrap/cache \
    && touch storage/logs/laravel.log \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache \
    && chmod -R 777 storage/logs \
    && chmod 666 storage/logs/laravel.log

# Change current user to www-data
USER www-data

# Expose port 9000
EXPOSE 9000

# Start php-fpm server
CMD ["php-fpm"]