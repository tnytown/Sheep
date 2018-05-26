#!/usr/bin/env bash

pm_url=$(curl -fsL "https://update.pmmp.io/api?channel=development" | jq -r ".download_url")
php_url="https://jenkins.pmmp.io/job/PHP-7.2-Aggregate/lastSuccessfulBuild/artifact/PHP_Linux-x86_64.tar.gz"
dt_url=$(curl -fsL "https://poggit.pmmp.io/releases.json?name=DevTools&latest-only" | jq -r ".[0].artifact_url")

echo $pm_url
echo $dt_url

pm_dir=$TRAVIS_BUILD_DIR/../pm
mkdir $pm_dir
cd $pm_dir
mkdir plugins/

curl -fsL $pm_url -o server.phar
curl -fsL $php_url -o php.tar.gz
curl -fsL $dt_url -o plugins/DevTools.phar

tar -zxf php.tar.gz

cp $TRAVIS_BUILD_DIR/.travis/PluginChecker.php plugins/
