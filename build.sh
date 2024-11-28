#!/usr/bin/env sh

curl -sLO https://github.com/box-project/box/releases/latest/download/box.phar
curl -sLO https://github.com/composer/composer/releases/latest/download/composer.phar

if [ -e ./composer.lock ]; then
  php ./composer.phar update --no-dev
else
  php ./composer.phar install --no-dev
fi

php -d error_reporting=24575 ./box.phar compile && ./tzdate
