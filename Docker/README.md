# DFG-Viewer Docker

 * [Prerequisites](#prerequisites)
 * [Usage](#usage)
 * [Development](#development)

The Kitodo.Production can be started quickly with the provided Docker image. However, a MySQL/MariaDB database and ElasticSearch are required to start the application. Additionally, a Docker Compose file is available for a faster setup.

## Prerequisites

Install Docker Engine
https://docs.docker.com/get-docker/

Install Docker Compose
https://docs.docker.com/compose/install/

## Usage

Go to the directory where you've put `docker-compose.yml`.

Copy the environment file `.env.example` inside the directory and rename it to `.env`. Adjust the configuration of the respective service to suit your needs. The variables are marked with the prefix of the service e.g. `APP_` for our Typo3 application with DFG-Viewer.

*It is recommended to adjust the password of the TYPO3 admin user `APP_T3_PASSWORD`, the database password `DB_PASSWORD` in general, especially in an productive environment.*

Download images and start all service containers
```
docker compose up -d
```

*When running `docker compose up` all services e.g. DFG-Viewer (APP) and database (DB) in our `docker-compose.yml` will be started and each as separate Docker container.*

Stops all service containers
```
docker compose stop
```

Stops and remove all service containers
```
docker compose down
```

## Development

To build the image, a folder named `extensions` must be added under folder `build`.

```
mkdir -p build/extensions
```

Then, the three extension repositories, [DFG Viewer](https://github.com/slub/dfg-viewer), [Kitodo.Presentation](https://github.com/kitodo/kitodo-presentation), [SLUB Digital Collections](https://github.com/slub/slub_digitalcollections), must be cloned into this folder.

```
git clone https://github.com/slub/dfg-viewer build/extensions
git clone https://github.com/kitodo/kitodo-presentation build/extensions
git clone https://github.com/slub/slub_digitalcollections build/extensions
```

Adjust the `composer.json` files of the checkouts as follows.

`composer.json` of the [DFG Viewer](https://github.com/slub/dfg-viewer) checkout `build/extensions/dfg-viewer`:

```
  ...
  "require": {
    ...
    "kitodo/presentation": "@dev"
    "slub/slub-digitalcollection": "@dev"
    ...
  },
  ...
```

`composer.json` of the [SLUB Digital Collections](https://github.com/slub/slub_digitalcollections) checkout `build/extensions/slub_digitalcollections`:

```
  ...
  "require": {
    ...
    "kitodo/presentation": "@dev"
    ...
  },
  ...
```

Build the image.

*Ensure that the `.env` file has been created. It is recommended to adjust the password of the TYPO3 admin user `APP_T3_PASSWORD`, the database password `DB_PASSWORD` in general and the `APP_IMAGE` name for building custom images in `.env` file.*

`docker compose build dfg-viewer-app`
