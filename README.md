# Gas Price by Gas Stations

backend part for a Gas Price project using PHP8, Nginx, MariaDb, RabbitMQ, MailDev.

## Getting Started

1. Clone https://github.com/clmntgbr/setup and run `make start`
2. Clone this repo
3. Run `cp .env.dist .env`
4. Edit the .env file to change PROJECT_NAME variable for renaming containers & directory
5. Run `make build` to build fresh images for docker
6. Run `make start` to start containers
7. Run `make init` to initialize the project
8. Run `make jwt` to generate the public and private keys used for signing JWT tokens
9. You can run `make help` to see all commands available

## Overview

Open `https://docker.localhost/dashboard/#/` in your favorite web browser for traefik dashboard

Open `https://maildev.docker.localhost` in your favorite web browser for maildev

Open `https://rabbitmq.docker.localhost` in your favorite web browser for rabbitmq

Open `https://gas.docker.localhost` in your favorite web browser for symfony app

## Features

* PHP 8.1.3
* Nginx 1.20
* RabbitMQ 3-management
* MariaDB 10.4.19
* MailDev
* Traefik latest
* Symfony 6.0.5

## Helpful commands

`make gas-update`

`make gas-details`

`make gas-closed`

`make gas-year`

`make consume`

**Enjoy!**
