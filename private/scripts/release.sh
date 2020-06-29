#!/bin/bash


DRUSH=../vendor/bin/drush

$DRUSH updb -y
$DRUSH cim sync -y
$DRUSH cr