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

### Local Preview Server

To preview the rendered output and automatically rebuild documentation on changes, you may spawn a local server. This supports auto-refresh and is faster than the official preview build, but omits some features such as syntax highlighting.

This requires Python 2 to be installed.

```bash
# First start: Setup Sphinx in a virtualenv
composer docs:setup

# Spawn server
composer docs:serve
composer docs:serve -- -E  # Don't use a saved environment (useful when changing toctree)
composer docs:serve -- -p 8000  # Port may be specified
```

By default, the output is served to http://127.0.0.1:8000.

### Official Preview Build

Build documentation using the [official Docker image](https://docs.typo3.org/m/typo3/docs-how-to-document/main/en-us/RenderingDocs/Quickstart.html):

```bash
# Full build
composer docs:t3 makehtml

# Only run sphinx-build
composer docs:t3 just1sphinxbuild

# (Alternative) Run docker-compose manually
docker-compose -f ./Build/Documentation/docker-compose.t3docs.yml run --rm t3docs makehtml
```
