#!/bin/bash
#
# Indexing / Import
#

URL='http://localhost:8983/solr/core/update' 
SOLRJ='/opt/solr-8.10.0/dist/solrj-lib'

if [ -n "$1" ]; then
	java -jar solrmarc_core_3.4.jar IndexDriver -config index.properties -solrj "$SOLRJ" -u "$URL" "$1" > import.log 2>&1
else
	echo 'Usage: import [file]'
fi

