#!/usr/bin/python3
#
# Kramerius Solr querier.
#
# model_path: /periodical/periodicalvolume/periodicalitem/page
#

import requests,json,re,sys

IN='in.json'
ROOT='root.json'

MATCH=0
ALL=0

def solr(G, ID, ISSN, Y, R, C, S=None):
	for KRAMERIUS in MAP[ISSN]:
		for ROOT in MAP[ISSN][KRAMERIUS]:
			session = requests.Session()
			query = (# PAGE
				'root_pid:' + ROOT.replace('-','\-').replace(':','\:') +
				' AND rok:' + Y +
				' AND title:' + S +
				' AND document_type:page'
			)
			try: req = session.get(KRAMERIUS + 'api/v5.0/search?q=' + query + '&fl=PID,title,rok,datum_str,parent_pid,details,pid_path,document_type&rows=10000&wt=json')
			except:
				print('Connection error.')
				continue
			if req.status_code == 200:
				resp = json.loads(req.text)
				print(G)
				print(Y + ' ' + R + ' ' + C + ' ' + S)
				for PAGE in resp['response']['docs']:
					PAGE_PID = PAGE['PID']
					#query = 'PID:' + PAGE['parent_pid'][0].replace('-','\-').replace(':','\:')
					#try: req = session.get(KRAMERIUS + 'api/v5.0/search?q=' + query + '&fl=PID,title,details&rows=1&wt=json')
					#except:
					#	print('Connection error.')
					#	continue
					#resp = json.loads(req.text)
					#if re.match('^.*##' + C + '$' , resp['response']['docs'][0]['details'][0]):
					#	URL = (
					#		KRAMERIUS.replace('search', 'view') +
					#		resp['response']['docs'][0]['PID'] + '?page=' +
					#		PAGE_PID
					#	)
					#	return URL
					
					try: req = session.get(KRAMERIUS + 'api/v5.0/search/item/' + PAGE['parent_pid'])
					except:
						print('Connection error.')
						continue
					resp = json.loads(req.text)
					if C = resp['details'][partNumber]):
						URL = (
							KRAMERIUS.replace('search', 'view') +
							resp['response']['pid'] + '?page=' +
							PAGE_PID
						)
						return URL
		session.close()
		sys.exit(1)

with open(IN,'r') as f: DATA = json.loads(f.read())
with open(ROOT, 'r') as f: MAP = json.loads(f.read())

for rec in DATA['response']['docs']:

	ID,G,Q = '','',''

	ISSN = rec['subfield_773-x'][0] 

	if ISSN not in MAP: continue

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
		LINK = solr(G, ID, ISSN, Y, R, C, S)
		if LINK:
			MATCH+=1
	elif Y and R and C:
		LINK = solr(G, ID, ISSN, Y, R, C)
		if LINK:
			MATCH+=1
	ALL+=1

print(MATCH)
print(ALL)

