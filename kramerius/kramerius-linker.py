#!/usr/bin/python3
#
# Kramerius Solr ISSD linker.
#

import sqlite3,json,sys

IN='in.json'
ISSN='issn.txt'
KRAMERIUS='kramerius.json'

PAGE='db/solr-page.db'
ITEM='db/solr-item.db'

BASE='https://www.digitalniknihovna.cz/'

# DEF

def page_db(db, issn, ID, Y, R, C, S):
	cur = db.execute("SELECT * FROM page WHERE issn = ? AND rok = ? AND title = ?;", (issn,Y , S))
	res = cur.fetchall()
	cur.close()
	
	if res:
		print(res)

# MAIN

with open(IN,'r') as f: DATA = json.loads(f.read())
with open(ISSN,'r') as f: I = f.read().splitlines()
with open(KRAMERIUS, 'r') as f: KRAM = json.loads(f.read())

db_page = sqlite3.connect(PAGE)
db_item = sqlite3.connect(ITEM)

for rec in DATA['response']['docs']:

	issn = rec['subfield_773-x']

	if issn not in I: continue# invalid issn

	ID,G,Q = '','',''

	ID = rec['id']
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
		ALEPH = query_page(db_page, issn, ID, Y, R, C, S)
		if ALEPH: print(ID + ' 85641 L $$u' + ALEPH + '$$yKramerius$$4N')

	sys.exit(1)

db_page.close()
db_item.close()

