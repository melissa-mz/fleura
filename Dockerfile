FROM php:8.2-apache

# Installation des dépendances et extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mysqli pdo_pgsql pgsql \
    && apt-get clean

# Activer le module rewrite d'Apache
RUN a2enmod rewrite

# Copier les fichiers du projet
COPY . /var/www/html/

# Définir les permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Créer le dossier d'upload
RUN mkdir -p /var/www/html/assets/images/products \
    && chmod -R 777 /var/www/html/assets/images/products

EXPOSE 80

CMD ["apache2-foreground"]