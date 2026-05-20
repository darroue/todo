# Stage 1: Generate Wayfinder TypeScript types (needs PHP + routes)
FROM php:8.4-cli-alpine AS wayfinder
RUN apk add --no-cache oniguruma-dev libzip-dev && \
    docker-php-ext-install mbstring zip
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-scripts --prefer-dist
COPY . .
RUN mkdir -p bootstrap/cache storage/logs && \
    APP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA= \
    DB_CONNECTION=sqlite DB_DATABASE=/tmp/build.sqlite \
    php artisan wayfinder:generate --with-form

# Stage 2: Build frontend assets
FROM node:22-alpine AS assets
ENV DOCKER_BUILD=true
WORKDIR /build
COPY package*.json ./
RUN npm install
COPY . .
COPY --from=wayfinder /app/resources/js/actions ./resources/js/actions
COPY --from=wayfinder /app/resources/js/routes ./resources/js/routes
RUN npm run build

# Stage 3: PHP 8.4 production image
FROM php:8.4-fpm-alpine

# System dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    libpng-dev \
    libzip-dev \
    libpq-dev \
    oniguruma-dev

# PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql mbstring zip gd bcmath pcntl opcache

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Nginx config
RUN printf 'server {\n\
    listen 8000;\n\
    root /var/www/html/public;\n\
    index index.php;\n\
    \n\
    location / {\n\
        try_files $uri $uri/ /index.php?$query_string;\n\
    }\n\
    \n\
    location ~ \\.php$ {\n\
        fastcgi_pass 127.0.0.1:9000;\n\
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;\n\
        include fastcgi_params;\n\
        fastcgi_hide_header X-Powered-By;\n\
    }\n\
    \n\
    location ~ /\\.(?!well-known).* {\n\
        deny all;\n\
    }\n\
}\n' > /etc/nginx/http.d/default.conf

# Supervisord config
RUN printf '[supervisord]\n\
nodaemon=true\n\
logfile=/dev/null\n\
logfile_maxbytes=0\n\
pidfile=/tmp/supervisord.pid\n\
\n\
[program:php-fpm]\n\
command=php-fpm -F\n\
autostart=true\n\
autorestart=true\n\
stdout_logfile=/dev/stdout\n\
stdout_logfile_maxbytes=0\n\
stderr_logfile=/dev/stderr\n\
stderr_logfile_maxbytes=0\n\
\n\
[program:nginx]\n\
command=nginx -g "daemon off;"\n\
autostart=true\n\
autorestart=true\n\
stdout_logfile=/dev/stdout\n\
stdout_logfile_maxbytes=0\n\
stderr_logfile=/dev/stderr\n\
stderr_logfile_maxbytes=0\n' > /etc/supervisord.conf

# PHP opcache tuning
RUN printf 'opcache.enable=1\n\
opcache.memory_consumption=128\n\
opcache.max_accelerated_files=10000\n\
opcache.validate_timestamps=0\n' > /usr/local/etc/php/conf.d/opcache.ini

WORKDIR /var/www/html

# Composer dependencies (own layer for caching)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --prefer-dist

# Application code
COPY . .

# Built frontend assets
COPY --from=assets /build/public/build ./public/build

# Permissions and directories
RUN mkdir -p storage/logs storage/framework/sessions storage/framework/views storage/framework/cache/data \
             storage/app/public bootstrap/cache && \
    composer dump-autoload --optimize && \
    chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD ["sh", "-c", "php artisan migrate --force && supervisord -c /etc/supervisord.conf"]
