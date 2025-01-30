FROM php:8.3-apache

ARG BUILD_TYPE="production"

# Validate BUILD_TYPE
RUN if [ "$BUILD_TYPE" != "development" ] && [ "$BUILD_TYPE" != "production" ]; then \
        echo "Invalid BUILD_TYPE: $BUILD_TYPE. Must be 'development' or 'production'."; \
        exit 1; \
    fi

# copy sources
COPY src /var/www/html

# Set up database
RUN mkdir -p /var/databases/kino-app && \
    chown -R www-data:www-data /var/databases/kino-app && \
    chmod -R u+w /var/databases/kino-app

# Set up posters dir
RUN mkdir -p /var/www/html/posters && \
    chown -R www-data:www-data /var/www/html/posters && \
    chmod -R u+w /var/www/html/posters  

RUN mv "$PHP_INI_DIR/php.ini-$BUILD_TYPE" "$PHP_INI_DIR/php.ini"


