#!/usr/bin/python
# -*- coding: utf-8 -*-
#
# Gen. 856 from JSON/BIB data
#

from __future__ import print_function

import json,sys,re
from pymarc import MARCReader

KRAMERIUS='ceslit.json'
BIB='ceslit.bib'
LOG='ceslit.log'
OUT='ceslit.link'

URL='https://kramerius.lib.cas.cz/uuid/'

TOTAL=0
MATCH=0
MONTH=0
MANUAL=0

# ------------------------

def convert_q(q,g,i,out,log,db):
	Y =  re.sub('^g[Rr]oč\. \d+, (\d+), .*$', '\\1', g).strip()
	S = re.sub('q\d+:(\d+|\d+\/\d+)<(\d+)', '\\2', q).strip()

	HIT=False

	for volume in range(0, len (db)):
		if Y == db[volume]['volume_year'] and int(Y) < 2011:
			for issue in range(0, len(db[volume]['issue'])):
				for page in db[volume]['issue'][issue]['page']:
					if page == S:
						HIT=True
						url = URL + db[volume]['issue'][issue]['page'][page]
						out.write(i + ' 85641 L $$u' + url + u'$$yKramerius' + '$$4N\n')
	if not HIT: log.write(i + ' FAIL\n')

def convert_month(g,i,out,log,db):
	Y = re.sub('^g[Rr]oč\. \d+, (\d+), .*$', '\\1', g).strip()
	part = re.sub('^.*, s\. (\d+|\d+-\d+|\d+\/\d+)$', '\\1', g)
	S = part.split('-')[0].split('/')[0].strip()

	HIT=False

	for volume in range(0, len (db)):
		if Y == db[volume]['volume_year'] and int(Y) < 2011:
			for issue in range(0, len(db[volume]['issue'])):
				for page in db[volume]['issue'][issue]['page']:
					if page == S:
						HIT=True
						url = URL + db[volume]['issue'][issue]['page'][page]
						out.write(i + ' 85641 L $$u' + url + u'$$yKramerius' + '$$4N\n')
	if not HIT: log.write(i + ' FAIL\n')

# ------------------------

log = open(LOG,'w')
out = open(OUT,'w')

with open(KRAMERIUS, 'r') as k:
	db = json.loads(k.read())

with open(BIB,'r') as b:
	for line in b:
		TOTAL+=1
		field = line.strip().split(chr(0x1F))
		ID = field[0].strip()
		G = field[4].strip()
		Q = field[5].strip()
		if re.match('q\d+:(\d+|\d+\/\d+)<\d+', Q):
			MATCH+=1
			convert_q(Q,G,ID,out,log,db)
			continue
		# MONTH LITERAL
		if re.match('^g[Rr]oč\. \d+, \d+, č\. (\d+|\d+\/\d+), \D+, s\. (\d+|\d+-\d+|\d+\/\d+)$', G):
			MONTH+=1
			convert_month(G,ID,out,log,db)
			continue
		# MONTH NUMERIC
		if re.match('^g[Rr]oč\. \d+, \d+, č\. (\d+|\d+\/\d+), \d+\. \d+\., s\. (\d+|\d+-\d+|\d+\/\d+)$', G):
			MONTH+=1
			convert_month(G,ID,out,log,db)
			continue
		# MANUAL
		MANUAL+=1
		log.write(ID + ' MANUAL\n')

print('TOTAL: ' + str(TOTAL))
print('MATCH: ' + str(MATCH))
print('MONTH: ' + str(MONTH))
print('MANUAL: ' + str(MANUAL))

log.close()
out.close()

