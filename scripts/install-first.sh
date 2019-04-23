#!/usr/bin/env bash

# Make us independent from working directory
pushd `dirname $0` > /dev/null
SCRIPT_DIR=`pwd`
popd > /dev/null

set -e

# Installing dependencies
echo "Preparing PHP dependencies..."
APP_ENV=dev $SCRIPT_DIR/backend.sh composer install
APP_ENV=dev $SCRIPT_DIR/backend.sh bin/console d:mi:m --no-interaction
echo ""
echo "Preparing JavaScript/CSS dependencies..."
echo ""
$SCRIPT_DIR/frontend.sh yarn
echo ""
echo "Preparing JavaScript/CSS dependencies..."
echo ""
$SCRIPT_DIR/frontend.sh yarn run encore dev

echo "Open your browser at http://127.0.0.1:8000"