#!/usr/bin/env bash

# WARN: This file is for build server only
red=`tput setaf 1`
green=`tput setaf 2`
reset=`tput sgr0`

DB_NAME=kl_rulemailer_test
DB_PASSWORD=topsecret
HOST_NAME=localhost:8080
MAGENTO_ROOT='src/'

# Install composer
echo "${green}Installing composer dependencies...${reset}"
composer install --prefer-source --no-interaction

echo "${green}Removing old database...${reset}"
mysql -uroot -p${DB_PASSWORD} -e "drop database if exists ${DB_NAME};"

# Remove
if [ -f "./${MAGENTO_ROOT}/app/etc/local.xml" ]; then
    echo "${green}Removing old local.xml config...${reset}"
    rm -rf ${MAGENTO_ROOT}/app/etc/local.xml
fi

# Install n98-magerun tool
if [ ! -f "./n98-magerun.phar" ]; then
    echo "${green}Downloading n98-magerun...${reset}"
    curl "https://raw.githubusercontent.com/netz98/n98-magerun/master/n98-magerun.phar" -o "n98-magerun.phar"
fi

php n98-magerun.phar install \
    --noDownload \
    --dbHost="127.0.0.1" \
    --dbUser="root" \
    --dbPass="${DB_PASSWORD}" \
    --dbName="${DB_NAME}" \
    --useDefaultConfigParams=yes \
    --installationFolder="${MAGENTO_ROOT}" \
    --baseUrl="${HOST_NAME}"

if ps aux | grep "[p]hp -S ${HOST_NAME} -t src/" > /dev/null ; then
    echo "${green}Server is already running...${reset}"
else
    php -S ${HOST_NAME} -t ${MAGENTO_ROOT} &
fi

echo "${green}Done installing.${reset}"
