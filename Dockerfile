# Etapa 1: Construcción
FROM php:8.3-fpm-alpine AS builder

# Instalar dependencias y extensiones
RUN apk add \
    git \
    unzip \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    libxml2-dev \
    bash \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql bcmath gd zip

# Copiar Composer desde la imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar el directorio de trabajo
WORKDIR /var/www/html
COPY . .

# Instalar dependencias de Laravel y optimizar
RUN composer update || true  # No queremos que falle si hay alertas de seguridad
RUN composer install --no-dev --optimize-autoloader
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache

# Permisos para almacenamiento y caché
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Etapa 2: Producción
FROM php:8.3-fpm-alpine

# Instalar Nginx y Supervisor
RUN apk add  nginx supervisor

RUN apk add libpng-dev libjpeg-turbo-dev libzip-dev libwebp-dev libxml2-dev bash && \
    docker-php-ext-install bcmath pdo pdo_mysql gd zip

# Copiar código de Laravel desde la imagen de construcción
COPY --from=builder /var/www/html /var/www/html
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader
# Copiar configuraciones
COPY .docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY .docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Ajustar permisos
RUN chown -R www-data:www-data /var/www/html
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
RUN chown -R www-data:www-data /var/log/nginx /var/lib/nginx /run && \
    chmod -R 775 /var/log/nginx /var/lib/nginx


EXPOSE 80

# Iniciar Supervisor
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

