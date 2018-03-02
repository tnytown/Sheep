#!/usr/bin/env bash

pm_dir=$TRAVIS_BUILD_DIR/../pm
PHP=$pm_dir/bin/php7/bin/php
cd $pm_dir

$PHP -dphar.readonly=0 $pm_dir/plugins/DevTools.phar --make $TRAVIS_BUILD_DIR --entry stub.php --out $pm_dir/plugins/Sheep.phar
$PHP $pm_dir/server.phar --no-wizard --disable-readline --debug.level=2 --pluginchecker.target=Sheep
