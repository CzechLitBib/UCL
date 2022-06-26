#!/usr/bin/python3
#
# Vufind LKR update
#

import requests,sys,io

from pymarc import marcxml,Field,Record,MARCWriter

IN='lkrs_fix.txt'
OUT='LKR.mrc'

LKR={}
COUNT=0

writer = MARCWriter(open(OUT, 'wb'))

with open(IN, 'r') as f:
	for line in f:
		DN=line.split('|')[0].strip()
		UP=line.split('|')[1].strip()
		if UP not in LKR:
			LKR[UP] = [DN]
		else:
			LKR[UP].append(DN)

session = requests.Session()

for UP in LKR:
	# VUFIND
	try:
		req = session.get('http://vufind2-dev.ucl.cas.cz/Record/' + UP + '/Export', params={'style':'MARCXML'}, timeout=10)
		if req.status_code == 200:
			handler = marcxml.XmlHandler()
			marcxml.parse_xml(io.StringIO(req.text), handler)
			metadata = handler.records[0]
		else:
			print("LKR: Record error: " + UP)
			continue
	except:
		print("LKR: Vufind error: " + UP)
		continue
	
	# LKR
	MATCH=False
	for DN in LKR[UP]:
		for F in metadata.get_fields('994'):
			if 'a' in F and 'b' in F and F['a'] == 'DN':
				if F['b'] == DN:
					break
		else:
			MATCH=True
			metadata.add_field(Field(tag='994', indicators=[' ',' '], subfields=['a', 'DN', 'b', DN]))

	if not MATCH: continue

	# RE-ORDER
	metadata_sort = Record()
	metadata_sort.leader = metadata.leader
	for T in sorted(set(F.tag for F in metadata.fields)):
		for F in metadata.fields:
			if T == F.tag: metadata_sort.add_field(F)
	metadata = metadata_sort

	# WRITE
	writer.write(metadata)
	COUNT+=1

session.close()
writer.close()

print(COUNT)

sys.exit(0)

