# Imagen base de PHP con Apache
FROM php:8.2-apache

# 🔹 Instala extensiones necesarias para conectar MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# 🔹 Copia el código al contenedor
COPY . /var/www/html/

# 🔹 Da permisos adecuados
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# 🔹 Habilita mod_rewrite
RUN a2enmod rewrite

# 🔹 Permitir .htaccess
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# 🚨 Agrega esta línea al final
EXPOSE 80
