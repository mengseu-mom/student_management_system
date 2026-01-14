# FROM php:8.3-cli

# # Install system dependencies
# RUN apt-get update && apt-get install -y \
#     git unzip libpq-dev \
#     && docker-php-ext-install pdo pdo_pgsql

# WORKDIR /var/www/html

# COPY . /var/www/html

# # Install composer
# COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# RUN composer install --no-interaction --prefer-dist --optimize-autoloader
# RUN chown -R www-data:www-data storage bootstrap/cache

# EXPOSE 8000

# # Use Laravel built-in server instead of FrankenPHP
# CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev zip curl \
    && docker-php-ext-install pdo pdo_pgsql zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

RUN chown -R www-data:www-data /var/www

EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=8000
