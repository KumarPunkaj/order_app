#!/usr/bin/env bash

red=$'\e[1;31m'
grn=$'\e[1;32m'
blu=$'\e[1;34m'
mag=$'\e[1;35m'
cyn=$'\e[1;36m'
white=$'\e[0m'

sudo apt update
sudo apt install -y curl

echo " $red ----- Installing Pre requisites ------- $white "
sudo docker-compose down && docker-compose up --build -d

echo " $grn -------Installing Dependencies -----------$blu "
sudo sleep 200s #this line is included for composer to finish the dependency installation so that test case can execute without error.

echo " $red ----- Running Migrations & Data Seeding ------- $white "
sudo chmod 777 -R ./code/*
docker exec order_app_php php artisan migrate
docker exec order_app_php php artisan db:seed

echo " $red ----- Running Intergration test cases ------- $white "
docker exec order_app_php php ./vendor/phpunit/phpunit/phpunit /var/www/html/tests/Feature/OrderIntegrationTest.php

echo " $red ----- Running Unit test cases ------- $white "
docker exec order_app_php php ./vendor/phpunit/phpunit/phpunit /var/www/html/tests/Unit/OrderUnitTest.php

exit 0
