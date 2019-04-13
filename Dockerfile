FROM dutchandbold/laravel-docker:latest

USER www-data

ARG APP_VERSION=${APP_VERSION:-""}
ARG APP_ENV=${APP_ENV:-"production"}
ENV APP_VERSION=${APP_VERSION}
ENV APP_ENV=${APP_ENV}
ENV NGINX_LISTEN 443 default_server
ENV NGINX_SSL on
ENV PHP_UPLOAD_MAX_FILESIZE=50M
ENV PHP_POST_MAX_SIZE=50M
ENV TZ=Europe/Amsterdam
ENV NGINX_SSL_REDIRECT=1

COPY --chown=www-data . /web

COPY --chown=www-data ./.env.$APP_ENV /web/.env

COPY --chown=www-data ./server/deployed.sh /scripts/deployed.sh

COPY --chown=www-data ./server/certs/$APP_ENV /config/ssl

COPY --chown=www-data ./server/nginx-default.conf /config/

RUN rm -f /web/.env.*

USER root
