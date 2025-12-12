# # Laravel + PHP-FPM Stage
# FROM php:8.2-fpm as laravel

# # Install Dependencies
# RUN apt-get update && apt-get install -y \
#     git curl zip unzip libpng-dev libonig-dev libxml2-dev \
#     && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# # Install Composer
# COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# # Copy Application
# WORKDIR /var/www
# COPY . .

# # Install dependencies
# RUN composer install --no-dev --optimize-autoloader

# # Optimize Laravel
# # RUN php artisan key:generate --force
# RUN php artisan storage:link || true
# RUN php artisan config:cache
# # RUN php artisan route:cache
# RUN php artisan view:cache

# # ----------------------------------------------------
# # Final Image with nginx + php-fpm
# # ----------------------------------------------------
# FROM nginx:stable

# # Copy Laravel app from previous stage
# COPY --from=laravel /var/www /var/www

# # Copy nginx configuration
# COPY ./docker/nginx.conf /etc/nginx/conf.d/default.conf

# # Set working directory
# WORKDIR /var/www

# # Expose port
# EXPOSE 80

# CMD ["nginx", "-g", "daemon off;"]

FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    libzip-dev zip unzip \
    && docker-php-ext-install pdo pdo_mysql zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Workdir
WORKDIR /var/www/html

# Copy Laravel
COPY . /var/www/html

# Set izin
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Symlink storage
RUN php artisan storage:link || true

# Copy Nginx config
COPY ./deploy/nginx.conf /etc/nginx/nginx.conf

# Copy Supervisor config
COPY ./deploy/supervisor.conf /etc/supervisor/conf.d/supervisor.conf

# Expose port
EXPOSE 80

# Command start: supervisord menjalankan php-fpm dan nginx
CMD ["/usr/bin/supervisord"]
