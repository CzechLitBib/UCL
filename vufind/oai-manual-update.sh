#!/bin/bash
#
# Download data from API server and run Solr update.
# Remove deleted records.
#

declare -x VUFIND_HOME="/usr/local/vufind"
declare -x VUFIND_LOCAL_DIR="/usr/local/vufind/local"

# UPDATE

FILE="/root/vufind-manual.mrc"

FROM="$(date -u --date '1 hour ago' '+%Y-%m-%d %H:00:00')"
UNTIL="$(date -u '+%Y-%m-%d %H:00:00')"

URL="https://vyvoj.ucl.cas.cz/api/ListRecords?from=$FROM&until=$UNTIL"

wget -O "$FILE" --header='Accept:application/octet-stream' "$URL"

[ -s "$FILE" ] && /usr/local/vufind/import-marc.sh "$FILE"

rm "$FILE" 2>/dev/null

exit 0

