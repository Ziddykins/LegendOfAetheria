#!/bin/bash

VHOSTFROMINSTALL=$1
PRETTYHOSTNAME=$(hostname -f)
PUBLICIP=$(curl -s -L https://icanhazip.com)
PRETTYPUBRESOLVE=$(dig 8.8.8.8 A +short)

if [[ ! "$VHOSTFROMINSTALL" ]]; then
    echo "This script is intended to be called from the AutoInstaller script."
    echo "If you want to run this script manually, please supply a FQDN as the first argument."
    echo "The FQDN should match up with an existing Apache virtual host."
    echo "Example: /bin/bash certbot.sh example.com"
    exit 1
fi

echo "Installing Certbot for Let's Encrypt on $PRETTYHOSTNAME ($PUBLICIP)"
echo "Public DNS resolution: $PRETTYPUBRESOLVE"
echo
echo "Installing Certbot and Apache plugin for Let's Encrypt..."
echo "This script assumes you have already pointed your FQDN to your servers IP address"
echo 
echo "Continue? (y/n)"
read -r CONTINUE

if [[ "$CONTINUE" != "y" ]]; then
    echo "Installation aborted."
    exit 1
fi

sudo apt install -y python3-certbot-apache certbot

if [[ $? -ne 0 ]]; then
    echo -e "\e[31mFailed to install Certbot. Please check your package manager.\e[0m"
    exit 1
fi

echo -e "\e[32mCertbot installed successfully.\e[0m"

echo "Sending command: 'certbot -d $PRETTYHOSTNAME --apache --non-interactive --agree-tos --email admin@$PRETTYHOSTNAME'"
echo "Continue? (y/n)"
read -r CONTINUE

if [[ "$CONTINUE" != "y" ]]; then
    echo -e "\e[31mSSL certificate request has been aborted\e[0m"
    exit 1
fi

certbot -d $PRETTYHOSTNAME --apache --non-interactive --agree-tos --email "admin@$PRETTYHOSTNAME"