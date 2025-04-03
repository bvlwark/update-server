#!/bin/bash

version=$(jq -r .version package.json)

mkdir -p releases

zip -r releases/bvlwark-update-server-"$version".zip \
  src \
  vendor \
  bvlwark-update-server.php \
  composer.json \
  composer.lock \
  package.json \
  README.md \
  readme.txt