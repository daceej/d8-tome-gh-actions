#!/bin/bash

DRUSH=../vendor/bin/drush
SITE_URL=$1

pushd ../
composer install
cp ci_secure_settings.php secure_settings.php
popd

echo "Downloading site database"
$DRUSH @d8-tome-gh-actions-demo.cms sql-dump > tome.sql

echo "Importing site database"
$DRUSH @self sql-create -y
$DRUSH @self sql-cli < tome.sql
rm tome.sql

echo "Downloading site files"
$DRUSH -y rsync @d8-tome-gh-actions-demo.cms:/var/www/tome-demo/docroot/sites/default/files/ sites/default/files -- --delete

echo "Building the static site"
$DRUSH cr
$DRUSH tome:static --uri=$SITE_URL

# Add CNAME and other goodies.
cp ../tome_build_extras/* ../html/.
echo "${SITE_URL##*/}" > ../html/CNAME
