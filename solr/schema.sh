#!/bin/bash

for F in $(cat schema5.txt); do
	./solr-schema.py --add $F
	sleep 1
done

