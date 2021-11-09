#!/usr/bin/python3
#
# Kramerius UUID to Aleph 856 linker
#

import json,re

IN='in.json'
MAP='map.json'
OUT='ucla.aleph'

def parse_Q(sysno,issn,q):

	# REGEXP MAP BY ISSN
	year = re.sub('^g[Rr]oč\. \d+, (\d+), .*$', '\\1', g).strip()
	page = re.sub('q\d+:(\d+|\d+\/\d+)<(\d+)', '\\2', q).strip()

	# ....
	# ....

	# COMPARATOR BY MAP

	for volume in range(0, len (db)):
		if year == MAP[issn][volume]['volume_year'] and int(Y) < 2011:
			for issue in range(0, len(db[volume]['issue'])):
				for page in db[volume]['issue'][issue]['page']:
					if page == S:
						url = MAP[issn].. + db[volume]['issue'][issue]['page'][page]
						return sysno + ' 85641 L $$u' + url + u'$$yKramerius' + '$$4N'
	return ''

def parse_G(sysno,issn,g):

	# REGEXP MAP BY ISSN
	year = re.sub('^g[Rr]oč\. \d+, (\d+), .*$', '\\1', g).strip()
	page = re.sub('^.*, s\. (\d+|\d+-\d+|\d+\/\d+)$', '\\1', g).split('-')[0].split('/')[0].strip()

	# ....
	# ....

	# COMPARATOR BY MAP

	for volume in range(0, len (db)):
		if Y == db[volume]['volume_year'] and int(Y) < 2011:
			for issue in range(0, len(db[volume]['issue'])):
				for page in db[volume]['issue'][issue]['page']:
					if page == S:
						url = MAP[issn] + db[volume]['issue'][issue]['page'][page]
						 return sysno + ' 85641 L $$u' + URL + u'$$yKramerius' + '$$4N'
	return ''

# MAIN

with open(MAP, 'r') as f: MAP = json.loads(f.read())

aleph = open(OUT, 'w')

with open(IN,'r') as f:
	for rec in json.loads(f)['response']['docs']:

	 	ID = rec['id'] 
		ISSN = rec['subfield_773-x'][0] 
		G = rec['subfield_773-g'][0] 
		Q = rec['subfield_773-q'][0] 
		DATA=''

		if ISSN in MAP:
			if re.match('\d+:(\d+|\d+\/\d+)<\d+', Q):
				DATA = parse_Q( ID, ISSN, Q)
			elif re.match('^[Rr]oč\. \d+, \d+, č\. (\d+|\d+\/\d+), \D+, s\. (\d+|\d+-\d+|\d+\/\d+)$', G):
				DATA = parse_Q( ID, ISSN, G)
			
			if DATA:
				aleph.write(DATA + '\n')
		
			#elif re.match('^g[Rr]oč\. \d+, \d+, č\. (\d+|\d+\/\d+), \d+\. \d+\., s\. (\d+|\d+-\d+|\d+\/\d+)$', G):

aleph.close()

