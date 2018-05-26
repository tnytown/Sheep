#!/usr/bin/env bash

# php ext. install from pmmp/PocketMine-MP
echo | pecl install channel://pecl.php.net/yaml-2.0.2
git clone https://github.com/krakjoe/pthreads.git
cd pthreads
git checkout d32079fb4a88e6e008104d36dbbf0c2dd7deb403
phpize
./configure
make
make install
cd ..
echo "extension=pthreads.so" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini

# PM + devtools setup
pm_url=$(curl -fsL "https://update.pmmp.io/api?channel=development" | jq -r ".download_url")
dt_url=$(curl -fsL "https://poggit.pmmp.io/releases.json?name=DevTools&latest-only" | jq -r ".[0].artifact_url")

echo $pm_url
echo $dt_url

pm_dir=$TRAVIS_BUILD_DIR/../pm
mkdir $pm_dir
cd $pm_dir
mkdir plugins/

curl -fsL $pm_url -o server.phar
curl -fsL $dt_url -o plugins/DevTools.phar

cp $TRAVIS_BUILD_DIR/.travis/PluginChecker.php plugins/
