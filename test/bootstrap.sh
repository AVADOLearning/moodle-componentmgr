#!/usr/bin/env bash

set -euo pipefail
IFS=$'\n\t'

root='/tmp/kitchen/data'
componentmgr="${root}/bin/componentmgr"
project="${root}/test/fixtures/componentmgr.json"

moodle_tarball='moodle-30.tar.gz'
moodle_url='https://download.moodle.org/download.php/direct/stable30/moodle-latest-30.tgz'

sudo apt-get update
sudo apt-get install -y curl git-core php5-cli php5-curl php5-json ruby1.9.1

pushd "$root"
curl -o 'composer.phar' 'https://getcomposer.org/composer.phar'
chmod +x 'composer.phar'
'./composer.phar' install
popd

"$componentmgr" refresh --project-file="$project"

dir=/tmp/install
mkdir -p "$dir"
pushd $dir
curl -sS -o "$moodle_tarball" "$moodle_url"
tar -xzf "$moodle_tarball"
pushd moodle
cp "$project" .
"$componentmgr" install
popd
popd

"$componentmgr" package --project-file="$project" \
        --package-format=Directory --package-destination=/tmp/package
