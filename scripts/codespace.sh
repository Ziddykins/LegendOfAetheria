#! /data/data/com.termux/files/usr/bin/bash

ACTION=$1

case $ACTION in
    "start")
        service apache2 start
        service mariadb start
        service php8.4-fpm start
        gh codespace ports forward --codespace $(gh cs list --json name | sed 's/\[{"name":"//' | sed 's/"}]//') 8000:8000 &
        ;;
    "stop")
        service apache2 stop
        service mariadb stop
        service php8.4-fpm stop
        ;;
    *)
        echo "Usage: $0 {start|stop}"
        exit 1
        ;;
esac