#!/usr/bin/python3
#
# Kramerius UUID to Aleph 856 linker
#

import json,re

IN='in.json'
MAP='map.json'
OUT='ucla.aleph'

ALL=0
MATCH=0

#def linker(ID,ISSN,Y,R,C,S):
#	for volume in range(0, len (db)):
#		if year == MAP[issn][volume]['volume_year'] and int(Y) < 2011:
#			for issue in range(0, len(db[volume]['issue'])):
#				for page in db[volume]['issue'][issue]['page']:
#					if page == S:
#						url = MAP[issn] + db[volume]['issue'] + [issue]['page'][page]
#						return sysno + ' 85641 L $$u' + url + u'$$yKramerius' + '$$4N'
#	return ''

with open(MAP, 'r') as f: MAP = json.loads(f.read())
with open(IN,'r') as f: data = json.loads(f.read())

#aleph = open(OUT, 'w')

for rec in data['response']['docs']:
	
	ID,G,Q = rec['id'],'',''

	ISSN = rec['subfield_773-x'][0] 

	if ISSN not in MAP: continue
	
	if 'subfield_773-g' in rec: G = rec['subfield_773-g'][0] 
	if 'subfield_773-q' in rec: Q = rec['subfield_773-q'][0] 

	Y,R,C,S = '','','',''# year, volume, issue, page

	if Q and re.match('^\d+:(\d+|\d+\/\d+)<\d+$', Q):
		R = re.sub('(\d+):(\d+|\d+\/\d+)<(\d+)', '\\1',Q)
		C = re.sub('(\d+):(\d+|\d+\/\d+)<(\d+)', '\\2',Q)
		S = re.sub('(\d+):(\d+|\d+\/\d+)<(\d+)', '\\3',Q)
	elif Q and re.match('^\d+:(\d+|\d+\/\d+)$', Q):
		R = re.sub('(\d+):(\d+|\d+\/\d+)', '\\1',Q)
		C = re.sub('(\d+):(\d+|\d+\/\d+)', '\\2',Q)
	if G and re.match('^Roč\. \d+, \d+, č\. (\d+|\d+\/\d+), \d+\. \d+\., s\. (\d+|\d+-\d+|\d+\/\d+)$', G):
		Y = re.sub('^Roč\. \d+, (\d+),.*', '\\1', G)

	if Y and R and C and S:
		MATCH+=1
	elif Y and R and C:
		MATCH+=1
	ALL+=1

print(MATCH)
print(ALL)

#aleph.close()

