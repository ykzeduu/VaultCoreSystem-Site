FROM php:8.2-apache

# Instala as extensões de MySQL que o PDO precisa (não vêm por padrão nessa imagem)
RUN docker-php-ext-install pdo_mysql mysqli

COPY . /var/www/html/
EXPOSE 80
