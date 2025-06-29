# Imagen base de PHP con Apache
FROM php:8.2-apache

# Copia el código del proyecto al directorio raíz del servidor
COPY . /var/www/html/

# Da permisos adecuados
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Habilita mod_rewrite para usar .htaccess (opcional, si haces URLs amigables)
RUN a2enmod rewrite

# Configura apache para que use .htaccess si existe
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf
