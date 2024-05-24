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

Build the DFG-Viewer documentation with the documentation rendering tool for Typo3.

```bash
docker run --rm -v $(pwd):/project -it ghcr.io/typo3-documentation/render-guides:latest --config ./Documentation
```

Take a look at the documentation by opening the file `Index.html` in the folder `Documentation-GENERATED-temp` in your browser.

### Troubleshooting

#### Permission

The container runs as a non-root user. If there are some problem regarding the permission of container user you can link the UID and GID of host into the container using the `--user` parameter.

**Example:**
```
docker run --rm --user=$(id -u):$(id -g) [...]
```

#### Output directory

The default documentation directory name is `Documentation-GENERATED-temp`. If you want to change the directory name add the `--output` parameter at the end of the building command.

**Example:**
```
[...] --config ./Documentation --output="My_Documentation_Directory"
```

### Provide with http.server module

If Python 3 is installed on your system you can provide the documentation via the `http.server` module.

```
cd Documentation-GENERATED-temp
python3 -m http.server 9000
```

Take a look at the documentation by opening http://localhost:9000/Index.html in your browser.
