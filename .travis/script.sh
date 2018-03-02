#!/usr/bin/env bash

pm_dir=$TRAVIS_BUILD_DIR/../pm
PHP=$pm_dir/bin/php7/bin/php
cd $pm_dir

$PHP -dphar.readonly=0 plugins/DevTools.phar --make $TRAVIS_BUILD_DIR --entry stub.php --out plugins/Sheep.phar
$PHP server.phar --no-wizard --disable-readline --debug.level=2 --pluginchecker.target=Sheep
