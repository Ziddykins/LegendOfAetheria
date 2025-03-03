#!/bin/sh
apt install -y software-properties-common
yes | LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php
yes | LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/apache2
apt update