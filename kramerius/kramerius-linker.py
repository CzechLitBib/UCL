#!/usr/bin/python3
#
# Kramerius Solr ISSD linker.
#

import sqlite3,json,sys

IN='in.json'
DB='kramerius.db'
DK='https://www.digitalniknihovna.cz/'

# DEF

def solr(ITEM, PAGE, ID, I, Y, R, C, S=None):
	URL=[]
	for page in PAGE:
		if S == page['title'] and Y == page['rok']:
			for item in ITEM:
				if page['parent_pid'][0] == item['PID']:
					print(item['details'][0].replace('#',''))
					sys.exit(1)
					if R == item['details'][0].replace('#',''):
						print(Y + ' ' + R + ' ' + C + ' ' + S)
						print(item)
						print(page)

	


	#	if 'view' in PREFIX[K]:
	#		URL.append( PREFIX[K] +	parent_pid + '?page=' + page_pid)
	#	elif 'i.jsp' in PREFIX[K]:
	#		URL.append(PREFIX[K] + page_pid)
	#	else:
	#		URL.append(DK + PREFIX[K] + '/view/' + parent_pid + '?page=' + page_pid)
	#return URL

# MAIN

with open(IN,'r') as f: DATA = json.loads(f.read())
with open(DB, 'r') as f: SOLR = f.read().splitlines()
with open(ISSN,'r') as f: ISSN = f.read().splitlines()
with open(BASE, 'r') as f: PREFIX = json.loads(f.read())

MAP={}
for line in SOLR:
	i,k,r,t,f = line.split('|')
	if i not in MAP: MAP[i]={}
	if k not in MAP[i]: MAP[i][k]={}
	if r not in MAP[i][k]: MAP[i][k][r]={}
	MAP[i][k][r][t]=f

#MATCH=0
#ALL=0

for I in ISSN:
	if '#' in I: I = I.split('#')[0]
	# skin no Kramerius
	if I not in MAP: continue

	# preload kram. root file
	for K in MAP[I]:
		for R in MAP[I][K]:
			if 'periodicalitem' in MAP[I][K][R] and 'page' in MAP[I][K][R]:
				with open('issn/'+ MAP[I][K][R]['periodicalitem'], 'r') as f: ITEM = json.loads(f.read()) 
				with open('issn/'+ MAP[I][K][R]['page'], 'r') as f: PAGE = json.loads(f.read()) 

			for rec in DATA['response']['docs']:
				ID,G,Q = '','',''

				if I != rec['subfield_773-x'][0]: continue# not current iter.
			
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
					LINK = solr((i for i in ITEM['response']['docs']), (p for p in PAGE['response']['docs']), ID, I, Y, R, C, S)
					if LINK:
						print(ID + ' 85641 L $$u' + L + '$$yKramerius$$4N')
						MATCH+=1
				elif Y and R and C:
					LINK = solr((i for i in ITEM['response']['docs']), (p for p in PAGE['response']['docs']), ID, I, Y, R, C)
					if LINK:
						print(ID + ' 85641 L $$u' + L + '$$yKramerius$$4N')
						MATCH+=1

	if MATCH == 10: sys.exit(1)
	ALL+=1

#print(MATCH)
#print(ALL)

