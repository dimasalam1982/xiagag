# Poll website task

## Task

https://github.com/dimasalam1982/xiagag/blob/master/task.md


## Architecture description

This project developed as Single Page Application. 

Frontend created with native JS and jQuery. 

Backend created with native PHP as API. You can use Swagger UI to research API. See links block below.

## Requirements

Recommended operation system is Ubuntu 20. Further documentation refers to Ubuntu.

This project based on docker images. Therefore you need to install docker (https://docs.docker.com/engine/install/ubuntu/) and docker-compose (https://docs.docker.com/compose/install/)

You need free ports 8081 and 8082.

## Installation

Create project directory and go to it

`mkdir xiag`

`cd xiag`

Clone repository

`git clone https://github.com/dimasalam1982/xiagag.git`

Go to code directory

`cd xiagag`

Build docker

`docker-compose up -d --build`

Install dependencies. Docker container with php-fpm named xiag_php_fpm

`docker exec -it xiag_php_fpm bash -c 'cd /var/www/dev; composer install'`

Make migrations

`docker exec -it xiag_php_fpm bash -c 'cd /var/www/dev/app/migrations; php migrate.php'`

if migrations will be failed that restart container:

`docker-compose down`

`docker-compose up -d --build`

and make migaration again.

Note: you can rollback migrations by command

`docker exec -it xiag_php_fpm bash -c 'cd /var/www/dev/app/migrations; php downmigrate.php'`

## Settings

Environment settings are placed in `.env` file. 

If you’ll change parameters in `.env` it’s need to restart container in order changes will be applied. Go to directory with project and make:

`docker-compose down` 

`docker-compose up` 

If you want to change site port you need to generate swagger file again. At first make rights on yaml file by editing:

`docker exec -it xiag_php_fpm bash -c 'cd /var/www/dev/public/openapi; chmod 777 openapi_online.yaml'`

next go to link in browser:

[update swagger yaml file - http://localhost:8082/swagger/generate](http://localhost:8082/swagger/generate)

## Links

[Swagger UI - http://localhost:8082/openapi/index.html](http://localhost:8082/openapi/index.html)

[PhpMyAdmin - http://localhost:8081](http://localhost:8081)

[Create question - http://localhost:8082/front/question.html?task=new](http://localhost:8082/front/question.html?task=new)

[Question page - http://localhost:8082/front/question.html?task=question&question=15981997225f4297aa044b5](http://localhost:8082/front/question.html?task=question&question=15981997225f4297aa044b5)
