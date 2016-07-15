#!/usr/bin/env bash

set -euo pipefail
IFS=$'\n\t'
export LC_ALL=C

root='/tmp/kitchen/data'
componentmgr="${root}/bin/componentmgr"
project_install="${root}/test/fixtures/componentmgr.install.json"
project_package="${root}/test/fixtures/componentmgr.package.json"

moodle_tarball='moodle-30.tar.gz'
moodle_url='https://download.moodle.org/download.php/direct/stable30/moodle-latest-30.tgz'

sudo apt-get update
sudo apt-get install -y software-properties-common
LC_ALL=C.UTF-8 sudo add-apt-repository -y ppa:ondrej/php

sudo apt-get update
sudo apt-get install -y \
        curl git-core \
        php-cli php-curl php-json php-xml php-zip ruby1.9.1

pushd "$root"
curl -o 'composer.phar' 'https://getcomposer.org/composer.phar'
chmod +x 'composer.phar'
'./composer.phar' install
popd

# Necessary for hosts that don't use POSIX-like filesystem permissions
chmod +x "$componentmgr"

"$componentmgr" refresh --project-file="$project_install"

dir=/tmp/install
mkdir -p "$dir"
pushd $dir
curl -sS -o "$moodle_tarball" "$moodle_url"
tar -xzf "$moodle_tarball"
pushd moodle
cp "$project_install" 'componentmgr.json'
"$componentmgr" install
popd
popd

pushd "${root}"
"$componentmgr" package --project-file="$project_package" \
        --package-format=Directory --package-destination=/tmp/package
popd
