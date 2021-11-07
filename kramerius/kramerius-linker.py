#!/usr/bin/python3
#
# Kramerius UUID to Aleph 856 linker
#

import json,sys,re

IN='ucla.csv'
OUT='ucla.aleph'

MAP='map.json'

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

aleph = open(OUT,'w')

TOTAL=0
MATCH=0

with open(IN,'r') as f:
	for line in f:
		data = line.split('$')
		if line[5] in MAP:# issn
			buff=''
			# prase Q
			if re.match('q\d+:(\d+|\d+\/\d+)<\d+', data[3]):
				buff = parse_Q(data[0], data[5], data[3])
			# prase G
			elif re.match('^g[Rr]oč\. \d+, \d+, č\. (\d+|\d+\/\d+), \D+, s\. (\d+|\d+-\d+|\d+\/\d+)$', data[4]):
			#elif re.match('^g[Rr]oč\. \d+, \d+, č\. (\d+|\d+\/\d+), \d+\. \d+\., s\. (\d+|\d+-\d+|\d+\/\d+)$', G):
				buff = parse_G(data[0], data[5], data[3])
			
			if buff: aleph.write(buff + '\n')
aleph.close()

