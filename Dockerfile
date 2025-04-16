FROM php:8.2-apache

# 1. Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip libzip-dev && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Instalar extensiones PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# 3. Configurar Apache
RUN a2enmod rewrite

# 4. Configurar Composer (con mirror para España y cache)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer config --global process-timeout 600 && \
    composer config --global cache-dir /tmp/composer-cache && \
    composer config -g repos.packagist composer https://packagist.es

# 5. Establecer directorio de trabajo
WORKDIR /var/www/html

# 6. Copiar código fuente
COPY . .

# 7. Preparar directorios Laravel
RUN mkdir -p storage/framework/{sessions,views,cache} bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# 8. Instalar dependencias PHP (con reintento automático)
RUN composer install --no-scripts --no-autoloader --no-interaction || \
    (sleep 30 && composer install --no-scripts --no-autoloader --no-interaction)

# 9. Finalizar configuración
RUN composer dump-autoload --optimize

EXPOSE 80
