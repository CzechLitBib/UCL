#!/usr/bin/python3
#
# Kramerius Solr querier.
#
# model_path: /periodical/periodicalvolume/periodicalitem/page
#

import requests,json,re,sys

IN='in.json'
ROOT='root.json'
BASE='kramerius.json'
DK='https://www.digitalniknihovna.cz/'

MATCH=0
ALL=0

def solr(G, ID, ISSN, Y, R, C, S=None):
	URL=[]
	for KRAMERIUS in MAP[ISSN]:
		session = requests.Session()
		for ROOT in MAP[ISSN][KRAMERIUS]:
			query = (
				'root_pid:' + ROOT.replace('-','\-').replace(':','\:') +
				' AND rok:' + Y +
				' AND (title:' + S + ' OR dc.title:' + S + ')' +
				' AND document_type:page'
			)
			try: req = session.get(KRAMERIUS + 'api/v5.0/search?q=' + query + '&fl=PID,parent_pid&rows=10000&wt=json')
			except:
				continue
			if req.status_code == 200:
				resp = json.loads(req.text)
				for PAGE in resp['response']['docs']:
					PAGE_PID = PAGE['PID']
					try: req = session.get(KRAMERIUS + 'api/v5.0/item/' + PAGE['parent_pid'][0])
					except:
						continue
					resp = json.loads(req.text)
					if 'partNumber' in resp['details']:
						if C == resp['details']['partNumber']:
							if 'view' in PREFIX[KRAMERIUS]:
								URL.append(
									PREFIX[KRAMERIUS] +
									resp['pid'] +
									'?page=' + PAGE_PID
								)
							elif 'i.jsp' in PREFIX[KRAMERIUS]:
								URL.append(PREFIX[KRAMERIUS] + PAGE_PID)
							else:
								URL.append(
									DK + PREFIX[KRAMERIUS] +
									'/view/' +
									resp['pid'] +
									'?page=' + PAGE_PID
								)
							break
					else:
						try: req = session.get(
							KRAMERIUS +
							'api/v5.0/search?q=PID:' +
							PAGE['parent_pid'][0] +
							'&fl=PID,details&wt=json'
						)
						except:
							continue
						if req.status_code == 200:
							resp = json.loads(req.text)
							if re.match('^.*##' + C + '$', resp['response']['docs']['details'][0]):
								if 'view' in PREFIX[KRAMERIUS]:
									URL.append(
										PREFIX[KRAMERIUS] +
										resp['response']['docs']['PID'] +
										'?page=' + PAGE_PID
									)
								elif 'i.jsp' in PREFIX[KRAMERIUS]:
									URL.append(PREFIX[KRAMERIUS] + PAGE_PID)
								else:
									URL.append(
										DK + PREFIX[KRAMERIUS] +
										'/view/' +
										resp['response']['docs']['PID'] +
										'?page=' + PAGE_PID
									)
								break
		session.close()
		return URL

with open(IN,'r') as f: DATA = json.loads(f.read())
with open(ROOT, 'r') as f: MAP = json.loads(f.read())
with open(BASE, 'r') as f: PREFIX = json.loads(f.read())

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
			for L in LINK:
				print(ID + ' 85641 L $$u' + L + '$$yKramerius$$4N')
				MATCH+=1
	elif Y and R and C:
		LINK = solr(G, ID, ISSN, Y, R, C)
		if LINK:
			for L in LINK:
				print(ID + ' 85641 L $$u' + L + '$$yKramerius$$4N')
				MATCH+=1

	if MATCH == 10: sys.exit(1)
	ALL+=1

print(MATCH)
print(ALL)

