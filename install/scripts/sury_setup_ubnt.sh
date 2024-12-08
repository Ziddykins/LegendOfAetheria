#!/bin/bash

if [ "$(whoami)" != "root" ]; then
    SUDO=sudo
fi

echo -e "\n" | ${SUDO} add-apt-repository ppa:ondrej/php
apt update