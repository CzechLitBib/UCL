#!/usr/bin/python
# -*- coding: utf-8 -*-
#
# Gen. 856 from JSON/BIB data
#

from __future__ import print_function

import json,re
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
	Y =  re.sub('^[Rr]oč\. \d+, (\d+), .*$', '\\1', g).strip()
	R = re.sub('(\d+):(\d+|\d+\/\d+)<\d+', '\\1', Q).strip()
	C = re.sub('\d+:(\d+|\d+\/\d+)<\d+', '\\1', Q).strip().split('/')
	S = re.sub('\d+:(\d+|\d+\/\d+)<(\d+)', '\\2', Q).strip()

	for volume in range(0, len (db)):
		if Y == db[volume]['volume_year']:
			if int(Y) < 1990:
		
			else:
				for issue in range(0, len(db[volume]['issue']):

def convert_month(g,i,out,log,db):
	R = re.sub('^[Rr]oč\. (\d+), .*$', '\\1', g).strip()
	Y = re.sub('^[Rr]oč\. \d+, (\d+), .*$', '\\1', g).strip()
	C = re.sub('^[Rr]oč\. \d+, \d+, č\. (\d+|\d+\/\d+), .*$', '\\1', g).strip().split('/')
	part = re.sub('^.*, s\. (\d+|\d+-\d+|\d+\/\d+)$', '\\1', g)
	S = part.split('-')[0].split('/')[0].strip()


# ------------------------

log = open(LOG,'w')
out = open(LOG,'w')

with open(KRAMERIUS, 'r') as k:
	db = json.loads(k.read())

with open(BIB,'r') as b:
	for line in b:
		TOTAL+=1
		field = line.strip().split(chr(0x1F))
		ID = field[0].strip()
		G = field[3].strip()
		Q = field[4].strip()
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
		log.write(ID + ' MANUAL ' + LINE)

print('TOTAL: ' + str(TOTAL))
print('MATCH: ' + str(MATCH))
print('MONTH: ' + str(MONTH))
print('MANUAL: ' + str(MANUAL))

log.close()
out.close()

