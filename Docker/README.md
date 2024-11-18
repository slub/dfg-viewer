# DFG-Viewer Docker

 * [Prerequisites](#prerequisites)
 * [Usage](#usage)
 * [Development](#development)
 * [Further information](#further-information)

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

To build the image, a `build` folder with a subfolder `extension` must be added.

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

## Further information

### Usage of 3D viewer integrations

*There are multiple ways to install the 3D viewer integration. The simplest is to upload the folder that is generated during the [viewer integration installation](https://github.com/slub/dlf-3d-viewers#installation). We have decided to provide an installation based on commands here.*

Once all containers have been started (see [Usage](#usage)), you can continue as follows.

1. Login into `dfg-viewer-app-1` container as root user.

``
docker exec -u root -it dfg-viewer-app-1 bash
``

2. Install unzip command line tool.

``
apt-get update && apt-get install unzip
``

3. Download the current state of main branch of repository [3D viewer integrations for DFG-Viewer](https://github.com/slub/dlf-3d-viewers) as zip file

``
wget "https://github.com/slub/dlf-3d-viewers/archive/refs/heads/main.zip" -O /tmp/dlf-3d-viewers.zip
``

4. Navigate to `fileadmin` folder, unzip the zip file, rename unzipped folder to `dlf_3d_viewers` and remove zip file

```
cd /var/www/html/dfg-viewer/public/fileadmin

unzip /tmp/dlf-3d-viewers.zip

mv dlf-3d-viewers-main dlf_3d_viewers

rm /tmp/dlf-3d-viewers.zip
```

5. Download libraries and frameworks for each viewer integration

``
sh dlf_3d_viewers/install.sh
``

6. Change owner of integration folder, subfolder and files

``
chown -R www-data:www-data dlf_3d_viewers
``
