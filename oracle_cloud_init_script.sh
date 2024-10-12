#!/bin/bash

# Setup instructions: https://docs.oracle.com/en-us/iaas/developer-tutorials/tutorials/apache-on-ubuntu/01oci-ubuntu-apache-summary.htm

sudo apt update
sudo apt -y install apache2

sudo iptables -I INPUT 6 -m state --state NEW -p tcp --dport 80 -j ACCEPT
sudo netfilter-persistent save

sudo apt -y install php libapache2-mod-php 
sudo apt -y install php-sqlite3 php-mbstring php-dom php-curl

sudo systemctl restart apache2

sudo rm -rf /var/www/html/*
sudo cp /home/ubuntu/php.ini /etc/php/8.3/apache2/php.ini
sudo cp -r /home/ubuntu/src/* /var/www/html

sudo chown -R www-data:www-data /var/www/html
sudo chmod -R u+w /var/www/html

# Set up database
sudo mkdir -p /var/databases/kino-app
sudo chown -R www-data:www-data /var/databases/kino-app/
sudo chmod -R u+w /var/databases/kino-app/

# Clean up home dir 
sudo rm -rf /home/ubuntu/*