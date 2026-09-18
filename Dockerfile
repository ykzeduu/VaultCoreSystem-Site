FROM php:8.2-apache

# Instala as extensões de MySQL que o PDO precisa (não vêm por padrão nessa imagem)
RUN docker-php-ext-install pdo_mysql mysqli

# Garante que os certificados raiz (CA) estão atualizados, necessários pra conectar via SSL no TiDB
RUN apt-get update && apt-get install -y ca-certificates && update-ca-certificates && rm -rf /var/lib/apt/lists/*

COPY . /var/www/html/

# Garante que o Apache (rodando como www-data) consiga gravar as fotos
# enviadas pelo formulário de cadastro de equipamentos/produtos.
# IMPORTANTE: como o disco do Render é temporário (não persistente no plano
# gratuito), essas imagens somem a cada novo deploy — veja o LEIA-ME.md.
RUN mkdir -p /var/www/html/assets/uploads/produtos \
    && chown -R www-data:www-data /var/www/html/assets/uploads \
    && chmod -R 775 /var/www/html/assets/uploads

EXPOSE 80
