# Use PHP 8.4-FPM official image
FROM php:8.4-fpm

# Set working directory
WORKDIR /var/www/html

# Install necessary system dependencies
RUN apt-get update
RUN apt-get install -y --no-install-recommends zip unzip git curl libsqlite3-dev
RUN docker-php-ext-install pdo pdo_mysql pdo_sqlite
RUN docker-php-ext-install pcntl
RUN docker-php-ext-configure pcntl --enable-pcntl
RUN apt-get install percona-toolkit --yes


RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html
RUN mkdir -p /var/www/html/database
RUN touch /var/www/html/database/database.sqlite
RUN chmod 777 /var/www/html/database/database.sqlite

CMD ["php-fpm"]
