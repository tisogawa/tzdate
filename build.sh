#!/usr/bin/env sh

set -eu

cd "$(dirname "$0")"

if ! command -v composer >/dev/null 2>&1; then
  echo "Composer is required: https://getcomposer.org/download/" >&2
  exit 1
fi

# Box is managed in tools/box (its own composer.lock, updated by Dependabot)
composer install --working-dir=tools/box --no-interaction
composer audit --working-dir=tools/box

composer install --no-dev --no-interaction
composer audit --no-dev

php -d error_reporting=24575 tools/box/vendor/bin/box compile && ./tzdate
