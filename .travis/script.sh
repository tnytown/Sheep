#!/usr/bin/env bash

PHP=bin/php7/bin/php
pm_dir=$TRAVIS_BUILD_DIR/../pm

$PHP -dphar.readonly=0 $pm_dir/plugins/DevTools.phar --make $TRAVIS_BUILD_DIR --entry stub.php --out $pm_dir/plugins/Sheep.phar
$PHP server.phar --no-wizard --disable-readline --debug.level=2 --pluginchecker.target=Sheep
