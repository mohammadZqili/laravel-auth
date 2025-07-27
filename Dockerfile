FROM php:8.3-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
ARG APP_DIR=.
COPY ${APP_DIR} .

# Fix git ownership issue
RUN git config --global --add safe.directory /var/www/html || true

# Install dependencies without dev packages for CI
RUN composer install --no-dev --optimize-autoloader --no-scripts || \
    composer install --optimize-autoloader --no-scripts

# Generate autoload files
RUN composer dump-autoload --optimize

# Set permissions
RUN chmod -R 755 storage bootstrap/cache || true

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
