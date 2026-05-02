FROM php:8.2-cli

# 1. Install System Dependencies (Needed for Composer to unzip packages)
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# 2. Install Composer (Copying the binary from the official image)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# 3. Optimized Layer Caching
# Copy only composer files first so 'composer install' is cached unless dependencies change
COPY composer.json composer.lock* ./

# 4. Install Dependencies
# --no-dev: Skip testing tools for production
# --optimize-autoloader: Makes the class map faster
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# 5. Copy the rest of the application
COPY . .

EXPOSE 8080

# Keep your current CMD for now, but in production, you'd eventually use RoadRunner or FrankenPHP
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]