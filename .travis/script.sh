#!/usr/bin/env bash

PHP=bin/php7/bin/php

$PHP -dphar.readonly=0 plugins/DevTools.phar --make $TRAVIS_BUILD_DIR --out plugins/Sheep_$(echo $TRAVIS_COMMIT | cut -c 1-6 -).phar
$PHP server.phar --no-wizard --disable-readline --debug.level=2 --pluginchecker.target=Sheep
