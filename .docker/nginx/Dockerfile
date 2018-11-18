FROM nginx:alpine

ADD default.conf /etc/nginx/conf.d/default.conf
ADD nginx.conf /etc/nginx/

# ARG PHP_UPSTREAM_CONTAINER=php-fpm
ARG PHP_UPSTREAM_CONTAINER=app
ARG PHP_UPSTREAM_PORT=9000
ARG PUID=1000

# Set upstream conf and remove the default conf
RUN sed -i "s/^nginx:x:100:/nginx:x:${PUID}:/" /etc/passwd \
    && mkdir -p /etc/nginx/sites-available \
    && echo "upstream php-upstream { server ${PHP_UPSTREAM_CONTAINER}:${PHP_UPSTREAM_PORT}; }" > /etc/nginx/conf.d/upstream.conf


CMD ["nginx"]

EXPOSE 80 443
