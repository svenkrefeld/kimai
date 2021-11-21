#!/usr/bin/env bash
trap 'echo -e "\e[0;32m" && echo -ne $(date "+%Y-%m-%d %H:%M:%S") && echo " >> Executing: $BASH_COMMAND" && echo -e "\e[0m"' DEBUG

mysql -h127.0.0.1 -uroot -e "CREATE DATABASE application"
# mysql -h127.0.0.1 -uroot application < application.sql

composer install

php bin/console kimai:install -n

php bin/console kimai:user:create sven info@svenkrefeld.de ROLE_SUPER_ADMIN

trap - DEBUG
