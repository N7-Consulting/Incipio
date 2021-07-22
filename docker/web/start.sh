#!/bin/sh
set -xe

if [ "$APP_ENV" != 'prod' ]; then
    composer install --prefer-dist --no-progress --no-interaction
else
    composer install --prefer-dist --no-dev --no-progress --optimize-autoloader
fi

# Start Apache with the right permissions after removing pre-existing PID file
rm -f /var/run/apache2/apache2.pid
exec docker/web/start_safe_perms -DFOREGROUND
