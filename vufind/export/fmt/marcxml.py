#!/usr/bin/python3
#
# Vufind - Export MARCXML format module
#

from io import BytesIO
from pymarc import marcxml,MARCReader,XMLWriter

# MARCXML

def buff(data):
	ret = BytesIO()
	writer = XMLWriter(ret)
	for record in data['response']['docs']:
		for rec in MARCReader(record['fullrecord'].encode('utf-8')):
			writer.write(rec)	
	writer.close(close_fh=False)
	ret.seek(0)
	return ret

