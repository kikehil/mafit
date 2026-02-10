FROM php:8.3-fpm

# Instalar dependencias del sistema y extensiones PHP
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libicu-dev \
    libsodium-dev \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl zip sodium

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /var/www/mafit

COPY . /var/www/mafit

RUN chown -R www-data:www-data /var/www/mafit \
    && chmod -R 775 /var/www/mafit/storage \
    && chmod -R 775 /var/www/mafit/bootstrap/cache

# Exponer puerto 9000
EXPOSE 9000

CMD ["php-fpm"]
