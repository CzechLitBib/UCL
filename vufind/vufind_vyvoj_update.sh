#!/bin/bash
#
# Download data from API server and run Solr update.
#

declare -x VUFIND_HOME="/usr/local/vufind"
declare -x VUFIND_LOCAL_DIR="/usr/local/vufind/local"

FILE="/root/vufind-$(date '+%Y-%m-%d-%H').mrc"

FROM="$(date -d '1 hour ago' '+%Y-%m-%d %H:00:00')"
UNTIL="$(date '+%Y-%m-%d %H:00:00')"

URL="https://vyvoj.ucl.cas.cz/api/ListRecords?from=$FROM&until=$UNTIL"

# UPDATE

wget -O "$FILE" --header='Accept:application/octet-stream' "$URL"

[ -s "$FILE" ] && /usr/local/vufind/import-marc.sh "$FILE"

rm "$FILE" 2>/dev/null

# DELETE

#FILE="/root/deletes.txt"

#URL="https://vyvoj.ucl.cas.cz/api/GetDeletes"

#wget -O "$FILE" "$URL"

#cd "$VUFIND_HOME/utils"

#[ -s "$FILE" ] && php deletes.php "$FILE" flat
#[ -s "$FILE" ] && php optimize.php

# rm "$FILE" 2>/dev/null

#cd ~

exit 0

