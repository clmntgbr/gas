# Gas Price by Gas Stations

backend part for a Gas Price project using PHP8, Nginx, MariaDb, RabbitMQ, MailDev.

## Getting Started

1. Clone this repo
2. Run `cp .env.dist .env`
3. Edit the .env file to change PROJECT_NAME variable for renaming containers & directory
4. Run `make build` to build fresh images for docker
4. Run `make start` to start containers
4. Run `make init` to initialize the project
5. You can run `make help` to see all commands available

## Overview

Open `https://traefik.docker.localhost/dashboard/#/` in your favorite web browser for traefik dashboard

Open `https://cheapest-maildev.docker.localhost` in your favorite web browser for maildev

Open `https://cheapest-rabbitmq.docker.localhost` in your favorite web browser for rabbitmq

Open `https://cheapest.docker.localhost` in your favorite web browser for symfony app

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

`make consume`

`make consume-high`

`make consume-low`

**Enjoy!**
