FROM wordpress:7.1.2-php8.2-apache

#Cloud SQL server CA 
COPY cloudsql-ca.pem /usr/local/share/ca-certificates/cloudsql-ca.crt
RUN update-ca-certificates

# Build version, updated on each release served at /VERSION
COPY VERSION /var/www/html/VERSION

# Must-use plugin
# Loaded via define('WPMU_PLUGIN_DIR', '/var/www/html/mu-plugins') in WORDPRESS_CONFIG_EXTRA
COPY mu-plugins/ /var/www/html/mu-plugins/
