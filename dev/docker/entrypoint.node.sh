#!/bin/sh

set -e

npm install
npm rebuild node-sass

exec npm run watch