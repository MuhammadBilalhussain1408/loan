#!/bin/bash
cd /var/www/html/mopado.com
# activate maintenance mode
echo "Activating maintenance mode";
php artisan down
# update source code
echo "Resetting repository";
git reset --hard HEAD
echo "Pulling latest changes";
git pull
rm -rf node_modules
npm install
npm run build
# update PHP dependencies
echo "Running composer update";
composer update --no-interaction
# --no-interaction Do not ask any interactive question
# --no-dev  Disables installation of require-dev packages.
# --prefer-dist  Forces installation from package dist even for dev versions.
# --ignore-platform-reqs  Ignores platform requirements.
# update database
echo "Running Migrations";
php artisan migrate --force
# --force  Required to run when in production.
# stop maintenance mode
echo "Disabling maintenance mode";
php artisan up
php artisan cache:clear
#restart queue
echo "Restarting queue";
php artisan queue:restart

php artisan permissions:reset

