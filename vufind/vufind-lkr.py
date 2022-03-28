#!/usr/bin/python3
#
# Vufind LKR update
#

import requests,sys,io

from pymarc import marcxml,Field,Record,MARCWriter

LKRS='TEST.csv'

# INIT -------------------

writer = MARCWriter(open('LKR.mrc', 'wb'))

session = requests.Session()

LKR={}
with open(LKRS, 'r') as f:
	for line in f.read().splitlines():
		DN=line.split('|')[0] 
		UP=line.split('|')[2] 
		if UP not in LKR:
			LKR[UP] = [DN]
		else:
			LKR[UP].append(DN)

# MAIN -------------------

for UP in LKR:
	# VUFIND
	try:
		req = session.get('https://vufind.ucl.cas.cz/Record/' + UP + '/Export', params={'style':'MARCXML'}, timeout=10)
		if req.status_code == 200:
			handler = marcxml.XmlHandler()
			marcxml.parse_xml(io.StringIO(req.text), handler)
			metadata = handler.records[0]
	except:
		print(ident + ': Vufind connection error.')
		continue
	
	# LKR
	MATCH=False
	for DN in LKR[UP]:
		for F in metadata.get_fields('994'):
			if 'a' in F and 'b' in F and F['a'] == 'DN':
				if F['b'] == DN: break
		else:
			MATCH=True # will write
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

# EXIT -------------------

writer.close()
sys.exit(0)

