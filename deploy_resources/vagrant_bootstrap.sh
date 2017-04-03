#!/usr/bin/env bash
sudo apt-get update

### System level items
# PHP
sudo apt-get install -y php
sudo apt-get install -y php-mysql
sudo apt-get install -y php-curl php-json php-cgi libapache2-mod-php
sudo apt-get install -y php-gd
# Python
sudo apt-get install -y python
# Zip
sudo apt-get install -y zip

#### Web Tools
# Composer
curl -Ss https://getcomposer.org/installer | php
sudo mv composer.phar /usr/bin/composer
# PHPUnit
wget https://phar.phpunit.de/phpunit.phar
chmod +x phpunit.phar
sudo mv phpunit.phar /usr/local/bin/phpunit
# Node
sudo curl -sL https://deb.nodesource.com/setup_7.x | sudo -E bash -
sudo apt-get install -y nodejs
sudo apt-get install -y build-essential
sudo npm install npm -g
# Python
sudo apt-get install -y python-setuptools python-dev build-essential 
sudo easy_install pip
sudo pip install requests