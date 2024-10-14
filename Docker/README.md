
# DFG-Viewer Docker

 * [Prerequisites](#prerequisites)
 * [Quickstart](#quickstart)
 * [Services](#services)

The Kitodo.Production can be started quickly with the provided Docker image. However, a MySQL/MariaDB database and ElasticSearch are required to start the application. Additionally, a Docker Compose file is available for a faster setup.

## Prerequisites

Install Docker Engine
https://docs.docker.com/get-docker/

Install Docker Compose
https://docs.docker.com/compose/install/

## Quickstart 

Go to the directory where you've put `docker-compose.yml`.

Copy the environment file `.env.example` inside the directory and rename it to `.env`. 

Download images and start all service containers
```
docker compose up -d --build
```

Stops all service containers
```
docker compose stop
```

Stops and remove all service containers
```
docker compose down
```

## Services

When running `docker compose up` all services e.g. DFG-Viewer (APP) and database (DB) in our `docker-compose.yml` will be started and each as separate Docker container.

### Environment file

To configure our services copy the environment file `.env.example` inside the directory and rename it to `.env`. Adjust the configuration of the respective service to suit your needs. The variables are marked with the prefix of the service e.g. `APP_` for our Typo3 application with DFG-Viewer.