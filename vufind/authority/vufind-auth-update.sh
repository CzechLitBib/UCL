#!/bin/bash
#
# Download data from API server and run Solr update.
# Remove deleted records.
#

declare -x VUFIND_HOME="/usr/local/vufind"
declare -x VUFIND_LOCAL_DIR="/usr/local/vufind/local"

# UPDATE

FILE="/root/import/CLO.mrc"

[ -s "$FILE" ] && /usr/local/vufind/import-marc-auth.sh "$FILE" > import.log 2>&1 &

exit 0

