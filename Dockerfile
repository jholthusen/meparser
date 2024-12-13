FROM composer AS composer

# Copying the source directory and install the dependencies with composer
COPY ./ /app

# Run composer install to install the dependencies
RUN composer install \
  --optimize-autoloader \
  --no-interaction \
  --no-progress

# Continue stage build with the desired image and copy the source including the dependencies downloaded by composer
FROM serversideup/php:8.3-fpm-nginx-alpine
COPY --chown=www-data:www-data --from=composer /app /var/www/html/public