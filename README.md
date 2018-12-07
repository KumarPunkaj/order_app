# Docker Laravel RESTful API

## About

- [Docker](https://www.docker.com/) as the container service to isolate the environment.
- [Php](https://php.net/) to develop backend support.
- [Laravel](https://laravel.com) as the server framework / controller layer
- [MySQL](https://mysql.com/) as the database layer
- [NGINX](https://docs.nginx.com/nginx/admin-guide/content-cache/content-caching/) as a proxy / content-caching layer

## How to Install & Run

1.  Clone the repo
2.  Set Google Distance API key in environment file located in ./code .env file
3.  Run `./start.sh` to build docker containers, executing migration and PHPunit test cases
4.  After starting container following will be executed automatically:
	- Table migrations using artisan migrate command.
	- Dummy Data imports using artisan db:seed command.

## Manually Migrating tables and Data Seeding

1. To run manually migrations use this command `docker exec order_app_php php artisan migrate`
2. To run manually data import use this command `docker exec order_app_php php artisan db:seed`

## Manually Starting the docker and test Cases

1. You can run `docker-compose up` from terminal
2. Server is accessible at `http://localhost:8080`
3. Run manual testcase suite by :
	- Integration test by this `docker exec order_app_php php ./vendor/phpunit/phpunit/phpunit /var/www/html/tests/Feature/OrderIntegrationTest.php` &
	- Unit Tests by this `docker exec order_app_php php ./vendor/phpunit/phpunit/phpunit /var/www/html/tests/Unit/OrderUnitTest.php`

## How to Run Tests (Explicity from cli)

 Test Cases can be executed by:
- Integration test by this `docker exec order_app_php php ./vendor/phpunit/phpunit/phpunit /var/www/html/tests/Feature/OrderIntegrationTest.php` &
- Unit Tests by this `docker exec order_app_php php ./vendor/phpunit/phpunit/phpunit /var/www/html/tests/Unit/OrderUnitTest.php`

## API Reference Documentation

- `localhost:8080/orders?page=:page&limit=:limit` :

    GET Method - to fetch orders with page number and limit
    1. Header :
        - GET /orders?page=0&limit=5 HTTP/1.1
        - Host: localhost:8080
        - Content-Type: application/json

    2. Responses :

    ```
            - Response
            [
              {
                "id": 1,
                "distance": 46732,
                "status": 1
              },
              {
                "id": 2,
                "distance": 46731,
                "status": 0
              },
              {
                "id": 3,
                "distance": 56908,
                "status": 0
              },
              {
                "id": 4,
                "distance": 49132,
                "status": 0
              },
              {
                "id": 5,
                "distance": 46732,
                "status": 0
              }
            ]
    ```

        Code                    Description
        - 200                   successful operation
        - 422                   Invalid Request Parameter
        - 500                   Internal Server Error

- `localhost:8080/orders` :

    POST Method - to create new order with origin and distination
    1. Header :
        - POST /orders HTTP/1.1
        - Host: localhost:8080
        - Content-Type: application/json

    2. Post-Data :
    ```
         {
            "origin" :["28.704060", "77.102493"],
            "destination" :["28.535517", "77.391029"]
         }
    ```

    3. Responses :
    ```
            - Response
            {
              "id": 44,
              "distance": 46732,
              "status": "UNASSIGN"
            }
    ```

        Code                    Description
        - 200                   successful operation
        - 400                   Api request denied or not responding
        - 422                   Invalid Request Parameter

- `localhost:8080/orders/:id` :

    PATCH method to update status for taken.(Handled simultaneous update request from multiple users at the same time with response status 409)
    1. Header :
        - PATCH /orders/44 HTTP/1.1
        - Host: localhost:8080
        - Content-Type: application/json
    2. Post-Data :
    ```
         {
            "status" : "TAKEN"
         }
    ```

    3. Responses :
    ```
            - Response
            {
              "status": "SUCCESS"
            }
    ```

        Code                    Description
        - 200                   successful operation
        - 422                   Invalid Request Parameter
        - 409                   Order already taken
        - 417                   Invalid Order Id

## App Structure

**./tests**

- this folder contains test cases written under /tests/Feature/OrderIntegrationTest.php

**./app**

- contains all the server configuration file and controllers and models
- migration files are written under database folder in migrations directory
	- To run manually migrations use this command `docker exec order_app_php php artisan migrate`
- Dummy data seeding is performed using faker under database seeds folder
	- To run manually data import use this command `docker exec order_app_php php artisan db:seed`
- `OrderController` contains all the api's methods :
    1. localhost:8080/orders?page=1&limit=4 - GET url to fetch orders with page and limit
    2. localhost:8080/orders - POST method to insert new order with origin and distination
    3. localhost:8080/orders - PATCH method to update status for taken.(Handled simultaneous update request from multiple users at the same time with response status 409)
- PHPUnit.xml provides the unit test case and code coverage

**.env**

- config contains all project configuration like it provides app configs, Google API Key, database connection

- Set Google Distance API key
