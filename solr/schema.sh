#!/bin/bash

for F in $(cat special_all.txt); do
	./solr-schema.py --add $F --type string
	sleep 1
done

echo

