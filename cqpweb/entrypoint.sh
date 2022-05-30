#!/bin/bash

# check if autosetup.php is not executed
# if file /root/autosetup.php.executed exists, then exit
# else execute autosetup.php

if [ -f /root/autosetup.php.executed ]; then
    echo "autosetup.php already executed"
else
  echo "Waiting to mysql to be ready"
  while ! mysqladmin ping -hdb -uroot -pletmein --silent; do
    sleep 1
  done

  echo "execute autosetup.php"
  cd /var/www/html/CQPweb/bin
  php ./autosetup.php
  touch /root/autosetup.php.executed
fi


apachectl -D FOREGROUND
