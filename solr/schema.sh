#!/bin/bash

for F in $(cat clo.txt); do
	./solr-schema.py --add uclo "$F" strings
	sleep 1
done

./solr-schema.py --add uclo field_VER string

