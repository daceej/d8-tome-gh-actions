#!/bin/bash

DRUSH=../vendor/bin/drush

echo 'INFO: Ensuring that all Drupal dependencies are present.'
pushd ../
composer install
popd

$DRUSH @d8-tome-gh-actions-demo.cms sql-dump > tome.sql
$DRUSH @self sql-create -y
$DRUSH @self sql-cli < tome.sql
rm tome.sql
$DRUSH -y rsync @d8-tome-gh-actions-demo.cms:/var/www/tome-demo/docroot/sites/default/files/ sites/default/files -- --delete

../private/scripts/release.sh

$DRUSH @self cr
$DRUSH @self uli -l http://local.tome.com

echo "DONE"
