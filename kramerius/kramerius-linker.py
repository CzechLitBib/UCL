#!/usr/bin/python3
#
# Kramerius Solr ISSD linker.
#

import sqlite3,json,re,sys

IN='in.json'
ISSN='issn.txt'
KRAMERIUS='kramerius.json'

# DEF

def query(db_page, db_item, ID, Y, R, C, S):
	ret=[]
	# kid, pid, parent, year, title, dc
	cur = db_page.execute("SELECT * FROM page WHERE year = ? AND dc = ?;", (Y , S))
	res = cur.fetchall()
	cur.close()
	
	for page in res:
		# kid, pid, title, dc, detail
		cur = db_item.execute("SELECT * FROM item WHERE pid = ? AND kid = ?;", (page[2],page[0]))# parent + kid
		res = cur.fetchall()
		cur.close()
		for item in res:
			if re.match('.*##\d+(\*+)?$', item[4]):
				if C == re.sub('^.*##(\d+)(\*+)?$', '\\1',item[4]):
					client = KRAM[item[0]]['client']
					if 'i.jsp' in client:
						ret.append(client + page[1])
					else:
						ret.append(client + item[1] + '?page=' + page[1])
			elif re.match('.*##\d+ ?- ?\d+(\*+)?$', item[4]):
				if C in range(int(re.sub('^.*##(\d+) ?- ?\d+(\*+)?$', '\\1',item[4])),int(re.sub('^.*##\d+ ?- ?(\d+)(\*+)?$', '\\1',item[4])) + 1):
					client = KRAM[item[0]]['client']
					if 'i.jsp' in client:
						ret.append(client + page[1])
					else:
						ret.append(client + item[1] + '?page=' + page[1])
	return ret
					
# MAIN

with open(ISSN,'r') as f: I = f.read().splitlines()
with open(KRAMERIUS, 'r') as f: KRAM = json.loads(f.read())

for i in I:

	TOTAL=0
	MATCH=0

	sys.stderr.write("Linking.. " + i + '\n')

	try:
		with open('db/' + i + '-page.db','r') as f: pass
		with open('db/' + i + '-item.db','r') as f: pass
		db_page = sqlite3.connect('db/' + i + '-page.db')
		db_item = sqlite3.connect('db/' + i + '-item.db')
	except:
		sys.stderr.write("Incomplete DB. " + i + '\n')
		continue
	with open('in/' + i + '.json', 'r') as f: DATA = json.loads(f.read())
	for rec in DATA:

		TOTAL+=1

		ID,G,Q = '','',''

		ID = rec['id']
		if 'subfield_773-g' in rec: G = rec['subfield_773-g'][0] 
		if 'subfield_773-q' in rec: Q = rec['subfield_773-q'][0] 

		Y,R,C,S = '','','',''# year, volume, issue, page

		#if Q and re.match('^\d+:(\d+|\d+\/\d+)<\d+$', Q):
		if Q and re.match('^[\[]?\d+[\]]?:[\[]?(\d+|\d+\/\d+)[\]]?<[\[]?([A-Z]?\d+|[A-Z]|[ivxIVX]+)[\]]?$', Q):# R:C<S
			R = re.sub('^[\[]?(\d+)[\]]?:[\[]?(\d+|\d+\/\d+)[\]]?<[\[]?([A-Z]?\d+|[A-Z]|[ivxIVX]+)[\]]?$', '\\1', Q)
			C = re.sub('^[\[]?\d+[\]]?:[\[]?(\d+|\d+\/\d+)[\]]?<[\[]?([A-Z]?\d+|[A-Z]|[ivxIVX]+)[\]]?$', '\\1', Q)
			S = re.sub('^[\[]?\d+[\]]?:[\[]?(\d+|\d+\/\d+)[\]]?<[\[]?([A-Z]?\d+|[A-Z]|[ivxIVX]+)[\]]?$', '\\2', Q)
	
		#if G and re.match('^Roč\. \d+, \d+, č\. (\d+|\d+\/\d+), \d+\. \d+\., s\. (\d+|\d+-\d+|\d+\/\d+)$', G):
		if G and re.match('^(Roč\.|roč\|R|R\.) \d+, (\d{4}|\d{4}\/\d{4}), .*$', G):
			Y = re.sub('^(Roč\.|roč\|R|R\.) \d+, (\d{4}|\d{4}\/\d{4}), .*', '\\2', G).split('/')[0]

		if Y and R and C and S:
			LINK = query(db_page,db_item, ID, Y, R, C, S)
			if LINK:
				for L in LINK:
					print(ID + ' 85641 L $$u' + L + '$$yKramerius$$4N')
				MATCH+=1
	db_page.close()
	db_item.close()
	sys.stderr.write('TOTAL: ' + str(TOTAL) + '\n') 
	sys.stderr.write('MATCH: ' + str(MATCH) + '\n')

