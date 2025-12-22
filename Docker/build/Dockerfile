# Using a qualified path allows, for example, running with Podman out of the box
FROM docker.io/php:8.4-apache-trixie

ARG BUILD_DATE
ARG GIT_REF=master
ARG GIT_REPOSITORY=slub/dfg-viewer
ARG GIT_SERVER_URL=https://github.com
ARG T3_VERSION=13

LABEL \
    maintainer="https://slub-dresden.de" \
    org.label-schema.vendor="Saxon State and University Library Dresden" \
    org.label-schema.name="DFG-Viewer" \
    org.label-schema.vcs-ref=$GIT_REF \
    org.label-schema.vcs-url="${GIT_SERVER_URL}/${GIT_REPOSITORY}/" \
    org.label-schema.build-date=$BUILD_DATE \
    org.opencontainers.image.vendor="Saxon State and University Library Dresden" \
    org.opencontainers.image.title="DFG-Viewer" \
    org.opencontainers.image.description="The DFG Viewer is a free web service for browsing digitized books from remote library repositories in a rich and dynamic environment." \
    org.opencontainers.image.source="${GIT_SERVER_URL}/${GIT_REPOSITORY}/" \
    org.opencontainers.image.documentation="${GIT_SERVER_URL}/${GIT_REPOSITORY}/blob/${GIT_REF}/README.md" \
    org.opencontainers.image.revision=$GIT_REF \
    org.opencontainers.image.created=$BUILD_DATE

# We use this pre-installation of Martin Helmich https://github.com/martin-helmich/docker-typo3 to set up all the necessary system components.
# TYPO3 is installed manually by Martin Helmich, but we prefer an Composer-based setup.
RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y --no-install-recommends \
        wget \
# Configure PHP
        libxml2-dev libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng-dev \
        libpq-dev \
        libzip-dev \
        zlib1g-dev \
# Install required 3rd party tools
        graphicsmagick && \
# Configure extensions
    docker-php-ext-configure gd --with-libdir=/usr/include/ --with-jpeg --with-freetype && \
    docker-php-ext-install -j$(nproc) mysqli soap gd zip opcache intl pgsql pdo_pgsql exif && \
    echo 'always_populate_raw_post_data = -1\nmax_execution_time = 240\nmax_input_vars = 1500\nupload_max_filesize = 32M\npost_max_size = 32M' > /usr/local/etc/php/conf.d/typo3.ini && \
# Configure Apache as needed
    a2enmod rewrite && \
    apt-get clean && \
    apt-get -y purge \
        libxml2-dev libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng-dev \
        libzip-dev \
        zlib1g-dev && \
    rm -rf /var/lib/apt/lists/* /usr/src/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer \
  | php -- --install-dir=/usr/bin --filename=composer

# Install TYPO3
RUN cd /var/www/html && \
    composer create-project typo3/cms-base-distribution dfg-viewer "^${T3_VERSION}" && \
   # cp ./dfg-viewer/public/typo3/sysext/install/Resources/Private/FolderStructureTemplateFiles/root-htaccess ./dfg-viewer/public/.htaccess && \
    touch ./dfg-viewer/public/FIRST_INSTALL && \
    mkdir -p ./dfg-viewer/public/fileadmin && \
    chown -R www-data:www-data ./dfg-viewer

WORKDIR /var/www/html/dfg-viewer

# Configure Apache
COPY apache2-dfg-viewer.conf /etc/apache2/sites-available/dfg-viewer.conf
RUN a2ensite dfg-viewer.conf && \
# Disable default behaviour
    a2dissite 000-default.conf

COPY extensions /var/www/html/dfg-viewer/packages

# Sites configuration
COPY --chown=www-data site/dfgviewer/config.yaml ./config/sites/dfgviewer/config.yaml

# Install scripts
COPY startup.sh /usr/bin/
COPY wait-for-it.sh /wait-for-it.sh

ENV DB_HOST=localhost
ENV DB_PORT=3306
ENV DB_NAME=typo3
ENV DB_USER=typo3
ENV DB_PASSWORD=password
ENV T3_USER=admin
ENV T3_PASSWORD="dvAdmin.1"
ENV T3_CONFIG_SYS_TRUSTEDHOSTSPATTERN=""

RUN chmod +x /usr/bin/startup.sh && \
    chmod +x /wait-for-it.sh && \
    # make /var/www writable for composer
    chown www-data:www-data /var/www

USER www-data

CMD ["/usr/bin/startup.sh"]

VOLUME /var/www/html/dfg-viewer/public/fileadmin
