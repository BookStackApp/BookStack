#!/bin/sh

set -e

npm install

SHELL=/bin/sh exec npm run watch
