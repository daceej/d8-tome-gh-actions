#!/bin/bash

directory=$(pwd)

if [ $directory != '/home/cj/Code/drex_v1/docroot' ]; then
  echo 'ERROR: You must run this script from the docroot.'
  exit 1
fi

echo 'INFO: Ensuring that all Drupal dependencies are present.'
yes | composer install

drush sql:create -y
drush site:install [PROFILE] -y
drush cr
drush uli -l [SITE URL]

echo "DONE"
