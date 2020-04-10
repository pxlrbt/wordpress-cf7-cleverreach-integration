#!/bin/bash

echo 'Install php-scoper'
wget https://github.com/humbug/php-scoper/releases/download/0.13.1/php-scoper.phar
chmod +x php-scoper.phar
mv php-scoper.phar /usr/local/bin/php-scoper

echo 'Prefixing'
php-scoper add-prefix

echo 'Dump-Autoloader'
cd build/ && composer dump-autoload

echo 'Move build'
cp -r ./* ../

echo 'Cleanup'
cd ../ && rm -rf build/
