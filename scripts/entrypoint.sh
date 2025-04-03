#!/bin/bash

until mysql -h"$DB_HOST" -P"3306" -uroot -p"$DB_PASS" &> /dev/null
do
  echo "Mysql is unavailable - sleeping"
  sleep 1
done

echo "Installing WordPress..."

cd /var/www/html
wp core config \
   --dbhost="${DB_HOST}" \
   --dbname="${DB_NAME}" \
   --dbuser="${DB_USER}" \
   --dbpass="${DB_PASS}"
wp core install \
   --url="${LOCAL_WEB}" \
   --title="BVLWARK Theme & Plugin Update Server" \
   --admin_user=admin \
   --admin_password=admin \
   --admin_email=drazen@bvlwark.com

echo "Enabling debug mode"

wp config set WP_DEBUG true --raw
wp config set WP_DEBUG_DISPLAY true --raw
wp config set WP_DEBUG_LOG false --raw

echo "Activating the plugin"

wp plugin delete akismet
wp plugin delete hello
wp plugin activate bvlwark-update-server

echo "~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-"
php -v
composer --version
echo "Local Web: $LOCAL_WEB"
echo "~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-"
exec php-fpm -F -R
