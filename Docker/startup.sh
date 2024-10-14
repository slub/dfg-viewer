#!/bin/bash


echo "Wait for database container."
/wait-for-it.sh -t 0 $DB_HOST:$DB_PORT

# run only once
if ! test -f "public/typo3conf/LocalConfiguration.php"; then 
    # Remove next line when Kitodo.Presentation 6 is released and DFG-Viewer supports this version
    # composer require --dev kitodo/presentation:dev-master

    # Install extensions from mounted extension folder
    composer req kitodo/presentation:@dev
    composer req slub/slub-digitalcollections:@dev
    composer req slub/dfgviewer:@dev

    ./vendor/bin/typo3cms install:setup --no-interaction \
   --database-user-name="$DB_USER" \
   --database-user-password="$DB_PASSWORD" \
   --database-host-name="$DB_HOST" \
   --database-port="$DB_PORT" \
   --database-name="$DB_NAME" \
   --admin-user-name="$T3_NAME" \
   --admin-password="$T3_PASSWORD" \
   --site-name="DFG-Viewer" \
   --use-existing-database
    ./vendor/bin/typo3cms configuration:set 'EXTENSIONS/dlf/fileGrpAudio' 'AUDIO'
    ./vendor/bin/typo3cms configuration:set 'EXTENSIONS/dlf/fileGrpVideo' 'VIDEO,DEFAULT'
    ./vendor/bin/typo3cms configuration:set --json 'FE/cacheHash/requireCacheHashPresenceParameters' '["tx_dlf[id]"]'
    ./vendor/bin/typo3cms configuration:set 'FE/pageNotFoundOnCHashError' 0 && \
    ./vendor/bin/typo3cms configuration:set 'EXTCONF/lang/availableLanguages' '["de"]' --json
    ./vendor/bin/typo3cms language:update
fi

exec apache2-foreground