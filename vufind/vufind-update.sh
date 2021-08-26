#!/bin/bash
#
# This script download data via OAI and import them to solr
#
 
declare -x VUFIND_HOME="/usr/local/vufind"
declare -x VUFIND_LOCAL_DIR="/usr/local/vufind/local"

FILE="/tmp/oai-import.lock"

if test -f "$FILE"; then
	echo "Another instance is running. Exiting" >> /root/import.log
	exit 1
fi

touch $FILE

DATE=$(date +%Y-%m-%d-%H:%M:%S)

echo "starting import at $DATE" >> /root/import.log

cd /usr/local/vufind/harvest >> /root/import2.log 2>&1

php harvest_oai.php >> /root/import2.log 2>&1

DATE=$(date +%Y-%m-%d-%H:%M:%S)

echo "Downloading OAI done at $DATE. Now importing" >> /root/import.log

cd ../local

./batch-import-marc.sh aleph >> /root/import.log

DATE=$(date +%Y-%m-%d-%H:%M:%S)

echo "Import done at $DATE" >> /root/import.log

rm $FILE

