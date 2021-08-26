#!/bin/bash
#
# Downloads update file from server and run solr update.
# Data are generated one hout back.
#
                                                                                                                                                      
declare -x VUFIND_HOME="/usr/local/vufind"
declare -x VUFIND_LOCAL_DIR="/usr/local/vufind/local"

DIR="/root/vufind_updates/"

FROM="$(date -d '1 hour ago' '+%Y-%m-%d %H:00:00')"
UNTIL="$(date '+%Y-%m-%d %H:00:00')"

NAME="vufind-$(date '+%Y-%m-%d-%H').mrc"

URL="https://vyvoj.ucl.cas.cz/api/ListRecords?from=$FROM&until=$UNTIL"

echo "$URL"

wget -O "$DIR/$NAME" --header='Accept:application/octet-stream' "$URL"

if [[ "$?" != 0 ]]; then
	echo "ERROR downloading file $NAME"
	exit 1
else
	echo "Successfuly downloaded file $NAME"
fi

[ -s "$DIR/$NAME" ] && /usr/local/vufind/import-marc.sh "$DIR/*.mrc"

rm "$DIR/*"

exit 0

