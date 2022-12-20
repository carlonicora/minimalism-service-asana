FROM php:8.2-cli-alpine

RUN apk update && apk add --no-cache curl g++ make autoconf bash \
  && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer