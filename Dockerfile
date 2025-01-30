FROM php:8.3-cli

# Installer les dépendances système nécessaires
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libsqlite3-dev \
    libsodium-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    curl \
    && docker-php-ext-install \
    xml \
    zip \
    intl \
    mbstring \
    pdo_mysql \
    mysqli \
    pdo_sqlite \
    sodium \
    gd \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html
