ARG PHP_TAG=8.0-alpine



FROM webdevops/php-nginx:$PHP_TAG as kbot-vendor

WORKDIR /app

COPY composer.* .

RUN composer install \
    --no-autoloader \
    --no-interaction \
    --no-scripts



FROM webdevops/php-nginx:$PHP_TAG

WORKDIR /app

COPY ./app/ /app/app
COPY ./bootstrap/ /app/bootstrap
COPY ./config/ /app/config
COPY ./database/ /app/database
COPY ./routes/ /app/routes
COPY ./resources/ /app/resources
COPY ./storage/ /app/storage
COPY ./public/ /app/public
# COPY ./.env /app/
COPY ./artisan /app/
# COPY ./server.php /app/
COPY ./composer.json /app/composer.json
COPY ./composer.lock /app/composer.lock


# Copy vendors
COPY --from=kbot-vendor /app/vendor /app/vendor

RUN composer dump-autoload

RUN php artisan storage:link

# Copy nginx config
COPY .deploy/conf/nginx/default.nginx /opt/docker/etc/nginx/vhost.conf

RUN ln -s /shared/.env /app/.env

RUN echo 'alias a="php artisan"' >> ~/.bashrc
