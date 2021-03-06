#!/usr/bin/env bash

set -euo pipefail
IFS=$'\n\t'

export DEBIAN_FRONTEND=noninteractive
export LC_ALL=C

root='/tmp/kitchen/data'
componentmgr="${root}/bin/componentmgr"
project_install="${root}/test/fixtures/componentmgr.install.json"
project_package="${root}/test/fixtures/componentmgr.package.json"

moodle_tarball='moodle-31.tar.gz'
moodle_url='https://download.moodle.org/download.php/direct/stable31/moodle-latest-31.tgz'

sudo DEBIAN_FRONTEND="$DEBIAN_FRONTEND" apt-get update
sudo DEBIAN_FRONTEND="$DEBIAN_FRONTEND" apt-get install -y software-properties-common
LC_ALL=C.UTF-8 sudo add-apt-repository -y ppa:ondrej/php

sudo DEBIAN_FRONTEND="$DEBIAN_FRONTEND" apt-get update
sudo DEBIAN_FRONTEND="$DEBIAN_FRONTEND" apt-get install -y \
        curl git-core \
        php-cli php-curl php-json php-mbstring php-xml php-zip \
        ruby2.3

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
