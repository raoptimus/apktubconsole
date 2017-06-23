#!/usr/bin/env bash
sudo -su root
port install php55-openssl
curl -sS https://getcomposer.org/installer | sudo php55
mv composer.phar /opt/local/bin/composer
composer global require "fxp/composer-asset-plugin:1.0.0"
composer install