# Utiliser PHP 8.2 avec Apache
FROM php:8.2-apache

# Installer les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev unzip git curl \
    && docker-php-ext-install pdo pdo_mysql zip opcache

# Activer mod_rewrite pour Apache (utile pour Symfony)
RUN a2enmod rewrite

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Installer Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash \
        && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Définir le répertoire de travail
WORKDIR /var/www/html

# Installer les dépendances Symfony
#RUN git config --global --add safe.directory /var/www/html
#RUN export COMPOSER_ALLOW_SUPERUSER=1
#RUN composer install --no-dev --optimize-autoloader --no-scripts
#RUN composer dump-autoload

# Configurer Symfony en dev
ENV APP_ENV=dev
#RUN php bin/console cache:clear

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
# Appliquer le document root et règles dans le fichier Apache
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
    RUN echo '<Directory /var/www/html/public>\n\
        Options -Indexes +FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
        <IfModule mod_rewrite.c>\n\
            RewriteEngine On\n\
            RewriteCond %{REQUEST_FILENAME} !-f\n\
            RewriteCond %{REQUEST_FILENAME} !-d\n\
            RewriteCond %{HTTP:Authorization} .+\n\
            RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]\n\
            RewriteRule ^ index.php [QSA,L]\n\
        </IfModule>\n\
    </Directory>' >> /etc/apache2/sites-available/000-default.conf

# Exposer le port 80
EXPOSE 80

CMD ["apache2-foreground"]
