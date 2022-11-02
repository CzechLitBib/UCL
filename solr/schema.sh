#!/bin/bash

CORE='clo'

for F in $(cat field_string.txt); do
	./solr-schema.py --add "$CORE" "$F" string
	sleep 1
done

for F in $(cat field_all.txt); do
	./solr-schema.py --add "$CORE" "$F" strings
	sleep 1
done

for F in $(cat subfield_string.txt); do
	./solr-schema.py --add "$CORE" "$F" string
	sleep 1
done

for F in $(cat subfield_all.txt); do
	./solr-schema.py --add "$CORE" "$F" strings
	sleep 1
done

