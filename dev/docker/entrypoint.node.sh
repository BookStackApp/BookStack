#!/bin/sh

set -e

npm install
npm rebuild node-sass

SHELL=/bin/sh exec npm run watch
