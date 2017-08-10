FROM composer:latest

RUN apk add --no-cache build-base gnupg

VOLUME ["/app"]
WORKDIR /app

RUN adduser -D -u 1000 container
USER container

ENTRYPOINT ["bin/phpunit"]
CMD ["--help"]
