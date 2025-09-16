FROM nginx:alpine

# Copiar la configuración de Nginx
COPY nginx/default.conf /etc/nginx/conf.d/default.conf
COPY nginx/nginx.conf /etc/nginx/nginx.conf
COPY public /var/www/public


# Exponer el puerto 80
EXPOSE 80
