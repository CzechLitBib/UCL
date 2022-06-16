#!/usr/bin/python3
#
# Vufind - Export MARC21 format module
#

from io import BytesIO

# MARC21

def buff(data):
	ret = BytesIO()
	for record in data['response']['docs']:
		ret.write(record['fullrecord'].encode('utf-8'))
	ret.seek(0)
	return ret

