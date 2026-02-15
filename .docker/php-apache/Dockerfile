FROM php:8.2.0-apache
COPY . /var/www/html
COPY ./.docker/php-apache/000-default.conf /etc/apache2/sites-available/000-default.conf

RUN apt-get update

RUN apt-get update && apt-get -y --no-install-recommends install \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    zlib1g-dev \
    libjpeg-dev \
# Configure the gd extension to include JPEG support BEFORE installing it
&& docker-php-ext-configure gd --with-jpeg \
# Install all required PHP extensions in a single command
&& docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    gd \
    zip \
    exif \
    mbstring \
    xml \
    dom \
# Enable Apache's rewrite module
&& a2enmod rewrite \
# Clean up to reduce image size
&& apt-get autoremove -y && apt-get clean \
&& rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

#RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash
#RUN apt install nodejs -y
RUN curl -sS https://getcomposer.org/installer | \
php -- --install-dir=/usr/local/bin --filename=composer
