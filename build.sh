#!/usr/bin/env sh

wget https://github.com/box-project/box/releases/latest/download/box.phar -O ./box.phar
wget https://github.com/composer/composer/releases/latest/download/composer.phar -O ./composer.phar

if [ ! -f ./composer.lock ]; then
  php ./composer.phar install --no-dev
else
  php ./composer.phar update --no-dev
fi

php ./box.phar compile && ./tzdate
