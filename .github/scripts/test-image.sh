#!/bin/bash
docker compose up -d

# run wait for it
chmod +x ./Docker/build/wait-for-it.sh
./Docker/build/wait-for-it.sh localhost:80 --strict -- echo "Application container is up"

# wait 60 seconds until the installation process has finished
sleep 60

# testing the container
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" localhost);
if [ $HTTP_CODE -ne 200 ]; then
    echo "Expected HTTP status code to be 200, but got $HTTP_CODE"
    exit 1
fi

HTML_TITLE=$(curl localhost | grep -m 1 -oP '(?<=<title>).+?(?=</title>)');
if [ "$HTML_TITLE" != "DFG Viewer" ]; then
    echo "Expected content of the HTML <title> tag should to be \"DFG Viewer\", but got \"$HTML_TITLE\""
    exit 1
fi


