#!/usr/bin/env bash
trap 'echo -e "\e[0;32m" && echo -ne $(date "+%Y-%m-%d %H:%M:%S") && echo " >> Executing: $BASH_COMMAND" && echo -e "\e[0m"' DEBUG

mysql -h127.0.0.1 -uroot -e "CREATE DATABASE application"
mysql -h127.0.0.1 -uroot application < dump.sql

composer install --optimize-autoloader -n

bin/console kimai:install -n

trap - DEBUG
