# Development

A DDEV-based development system is available at https://github.com/slub/ddev-dfgviewer.

## Frontend Build

Build JavaScript and CSS bundles using Grunt:

```bash
cd Build/
nvm use
npm ci
npm run watch
npm run build
```

## Documentation

Build the DFG-Viewer documentation using the `docs:build` script with Composer.
This script generates the documentation using the rendering tool for Typo3 and
places it in the `Documentation-GENERATED-temp` folder.

```bash
composer docs:build
```

Take a look at the documentation by opening the file `Index.html` in the folder
`Documentation-GENERATED-temp` in your browser.

### Provide via Http Server (optional)

Starts the http server and mounts the mandatory directory `Documentation-GENERATED-temp`.

```bash
composer docs:start
```

Take a look at the documentation by opening <http://localhost:8000>
in your browser.

The server runs in detached mode, so you will need to stop the http server manually.

```bash
composer docs:stop
```

### Troubleshooting

#### Permission

The documentation container runs as a non-root user. If there are some problem regarding
the permission of container user you can link the UID and GID of host into the container
using the `--user` parameter.

**Example:**

```bash
docker run --rm --user=$(id -u):$(id -g) [...]
```

_In the `docs:build` Composer script, this parameter is already included.
If any issues arise, you can adjust or remove it as needed._

#### Output directory

The default documentation directory name is `Documentation-GENERATED-temp`.
If you want to change the directory name add the `--output` parameter at the
end of the building command.

**Example:**

```bash
[...] --config ./Documentation --output="My_Documentation_Directory"
```
