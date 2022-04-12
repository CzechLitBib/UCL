#!/usr/bin/python3
#
# Vufind LKR update
#

import requests,sys,io

from pymarc import marcxml,Field,Record,MARCWriter

VUFIND='https://xxx'
DEVEL='https://xxx'

# ARG -------------------

if len(sys.argv) != 2:
	print("LKR Usage: vufind-lkr.py [filepath]")
	sys.exit(1)

# LKR -------------------

LKR={}
try:
	req = requests.get(DEVEL + '/api/GetLkrs', timeout=10)
	if req.status_code == 200:
		for line in req.text.splitlines():
			DN=line.split('|')[0] 
			UP=line.split('|')[1] 
			if UP not in LKR:
				LKR[UP] = [DN]
			else:
				LKR[UP].append(DN)
except:
	print("LKR: API error.")
	sys.exit(1)

# VUFIND -------------------

try:
	writer = MARCWriter(open(sys.argv[1], 'wb'))
except:
	print("LKR: Invalid file path.")
	sys.exit(1)

session = requests.Session()

for UP in LKR:
	# VUFIND
	try:
		req = session.get(VUFIND + '/Record/' + UP + '/Export', params={'style':'MARCXML'}, timeout=10)
		if req.status_code == 200:
			handler = marcxml.XmlHandler()
			marcxml.parse_xml(io.StringIO(req.text), handler)
			metadata = handler.records[0]
		else:
			print("LKR: Record error.")
			continue
	except:
		print("LKR: Vufind error.")
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

# EXIT -------------------

session.close()
writer.close()

sys.exit(0)

