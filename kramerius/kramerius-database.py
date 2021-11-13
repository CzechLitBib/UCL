#!/usr/bin/python3
#
# Kramerius Solr ISSN database.
#

import sqlite3,json,sys,re

KRAMERIUS='kramerius.json'
ISSN='issn.txt'
DB='solr.txt'

# DEF

def kramerius_index(solr):
	index=0
	for k in (k['solr'] for k in KRAM):
		if solr == k: return index
		index+=1

def page_db_write(issn, k, fn):
	try:
		with open(fn, 'r') as f: data = json.loads(f.read())
	except:
		print('JSON broken: ' + fn)
		return

	BATCH=[]
	for page in data['response']['docs']:

		parent,year,title,dc='','','',''

		pid = page['PID']
		kid = kramerius_index(k)

		if 'parent_pid' in page: parent = page['parent_pid'][0]
		if 'rok' in page: year = page['rok']
		if 'title' in page: title = page['title']
		if 'dc.title' in page: dc = page['dc.title']

		BATCH.append((kid, pid, parent, year, title, dc))

	con = sqlite3.connect('db/' + issn + '-page.db')
	cur = con.cursor()
	cur.executemany("INSERT INTO page (kid, pid, parent, year, title, dc) VALUES (?,?,?,?,?,?);", BATCH)
	con.commit()
	con.close()

def item_db_write(issn, k, fn):
	try:
		with open(fn, 'r') as f: data = json.loads(f.read())
	except:
		print('JSON broken: ' + fn)
		return
	BATCH=[]
	for item in data['response']['docs']:

		title,dc,detail='','',''

		pid = item['PID']
		kid = kramerius_index(k)

		if 'title' in item: title = item['title']
		if 'dc.title' in item: dc = item['dc.title']
		if 'details' in item: detail = item['details'][0]

		BATCH.append((kid, pid, title, dc, detail))

	con = sqlite3.connect('db/'+ issn + '-item.db')
	cur = con.cursor()
	cur.executemany("INSERT INTO item (kid, pid, title, dc, detail) VALUES (?,?,?,?,?);", BATCH)
	con.commit()
	con.close()


# INIT

with open(DB, 'r') as f: SOLR = f.read().splitlines()
with open(ISSN,'r') as f: ISSN = f.read().splitlines()
with open(KRAMERIUS,'r') as f: KRAM = json.loads(f.read())

MAP={}
for line in SOLR:
	i,k,r,t,f = line.split('|')
	if i not in MAP: MAP[i]={}
	if k not in MAP[i]: MAP[i][k]={}
	if r not in MAP[i][k]: MAP[i][k][r]={}
	MAP[i][k][r][t]=f

for issn in ISSN:
	# multi-issn
	if '#' in issn: issn = issn.split('#')[0]
	# skip
	if issn not in MAP: continue

	try:# skip dup
		con = sqlite3.connect('db/' + issn + '-page.db')
		cur = con.cursor()
		cur.execute("CREATE TABLE page (kid INTEGER, pid TEXT, parent TEXT, year TEXT, title TEXT, dc TEXT);")
		cur.execute("CREATE INDEX 'pid_index' ON page (pid);")
		cur.execute("CREATE INDEX 'parent_index' ON page (parent);")
		con.commit()
		con.close()

		con = sqlite3.connect('db/' + issn + '-item.db')
		cur = con.cursor()
		cur.execute("CREATE TABLE item (kid INTEGER, pid TEXT, title TEXT, dc TEXT, detail TEXT);")
		cur.execute("CREATE INDEX 'pid_index' ON item (pid);")
		con.commit()
		con.close()
	except: pass

for issn in ISSN:
	# multi-issn
	if '#' in issn: issn = issn.split('#')[0]
	# skip
	if issn not in MAP: continue

	for k in MAP[issn]:
		for r in MAP[issn][k]:
			if 'page' in MAP[issn][k][r]: page_db_write(issn, k, 'issn/' + MAP[issn][k][r]['page'])
			if 'periodicalitem' in MAP[issn][k][r]: item_db_write(issn, k, 'issn/' + MAP[issn][k][r]['periodicalitem'])

