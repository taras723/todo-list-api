# Use PHP 8.1 with FPM
FROM php:8.1-fpm

# Install dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo_mysql zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . .

# Install Composer dependencies
RUN composer install --optimize-autoloader --no-dev

# Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expose port and start PHP-FPM
EXPOSE 9000
CMD ["php-fpm"]