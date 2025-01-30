FROM php:8.3-cli

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libxml2-dev \
    && docker-php-ext-install xml

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html