#sert à construire un custom image pour l'app
FROM php:7.4-cli

WORKDIR /app
     #first . (source dir from host machine) is this app ààabove ,the second . (destination image) is the working directory ..i will copy all the files here in the docker container
COPY . .
    #once i copy all the files i have to run composer install
RUN apt-get update && \
        apt-get install -y --no-install-recommends wget \
        libfreetype6-dev \
        libpng-dev \
        libjpeg-dev \
        libcurl4-gnutls-dev \
        libyaml-dev \
        libicu-dev \
        libzip-dev \
        unzip \
        git > /dev/null && \
        apt-get install -y libcurl4-openssl-dev pkg-config libssl-dev > /dev/null
RUN docker-php-ext-install intl gettext gd bcmath zip sockets
RUN pecl install mongodb
#pecl repository for php extensions

RUN echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/mongodb.ini

#install composer
RUN echo "Installing Composer" && rm -rf vendor composer.lock && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer clearcache && \
    composer install

# Refresh laravel config
RUN php artisan config:cache

#to run dockerfile we have a command which is docker build .
# to build an image: docker build --rm -t project-docker-image:latest .
#docker exec -it admin_admin_1 bash to execute a command inside a container
#docker cp 239350a5966a6b465c690103ee5c3e960470632e119a4d4bc87f883f14bc5f1c:/app/vendor . -> copy m container lel host machine
