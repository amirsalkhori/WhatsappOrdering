FROM nginx

WORKDIR /etc/nginx/conf.d

COPY docker/nginx/nginx.conf .

RUN mv nginx.conf default.conf

WORKDIR /var/www/html

#COPY app .