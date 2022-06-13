#!/usr/bin/python3
#
# Vufind - Export JSON format module
#

import json

from io import BytesIO
from pymarc import MARCReader

# JSON

def buff(data):
	ret = BytesIO()
	buff=[]
	for record in data['response']['docs']:
		for rec in MARCReader(record['fullrecord'].encode('utf-8')):
			buff.append(rec.as_dict())
	ret.write(json.dumps(buff).encode('utf-8'))
	ret.seek(0)
	return ret

