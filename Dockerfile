FROM php:8.2-apache

# Instala as extensões de MySQL que o PDO precisa (não vêm por padrão nessa imagem)
RUN docker-php-ext-install pdo_mysql mysqli

# Garante que os certificados raiz (CA) estão atualizados, necessários pra conectar via SSL no TiDB
RUN apt-get update && apt-get install -y ca-certificates && update-ca-certificates && rm -rf /var/lib/apt/lists/*

COPY . /var/www/html/
EXPOSE 80
