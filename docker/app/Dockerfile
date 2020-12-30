FROM yiisoftware/yii2-php:7.4-apache as build-stage
ARG VERSION=latest

WORKDIR /app
RUN rm -rf /app/*
RUN git clone https://github.com/proenix/library-tdd.git . && \
    cd /app && \
    git checkout ${VERSION} && \
    git pull && \
    chown www-data:www-data -R /app &&\
    chmod 755 /app/yii
RUN composer install

# Release build
FROM yiisoftware/yii2-php:7.4-apache as release
LABEL maintainer="proenix@proenix.pl"
COPY --from=build-stage /app /app