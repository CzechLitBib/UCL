#!/usr/bin/python3
#
# Kramerius Solr ISSN querier.
#

import requests,json,uuid,sys

I='issn.txt'
K='kramerius.json'

with open(K, 'r') as f: KRAMERIUS = json.loads(f.read())
with open(I, 'r') as f: ISSN = f.read().splitlines()

# DEF

def escape(uuid):
	return uuid.replace(':','\:').replace('-','\-')

# MAIN

for K in KRAMERIUS:
	session = requests.Session()
	for I in ISSN:
		if '#' in I: I = I.split('#')[0]# multi-issn value
		# ISSN ROOT
		ROOT=[]
		try: req = session.get(K['solr'] + 'api/v5.0/search?q=issn:' + escape(I) + '&fl=root_pid&rows=1000000&wt=json')
		except:
			sys.stderr.write('Session error: ' + I + ' -> ' + K['solr'] + '\n')
			continue
		if req.status_code == 200:
			try:
				resp = json.loads(req.text)
			except:
				sys.stderr.write('JSON error: ' + I + ' -> ' + K['solr'] + '\n')
				continue
			if 'response' in resp and 'numFound' in resp['response']:
				if resp['response']['numFound'] > 0:
					for R in resp['response']['docs']:
						if R['root_pid'] not in ROOT: ROOT.append(R['root_pid'])
		for R in ROOT:
			# ISSN ROOT PERIODICALITEM
			try: req = session.get(K['solr'] + 'api/v5.0/search?q=root_pid:' + escape(R) + ' AND document_type:periodicalitem' + '&fl=PID,title,dc.title,details&rows=1000000&wt=json')
			except:
				sys.stderr.write('Session error: ' + I + ' -> ' + K['solr'] + ' -> ' + R + '\n')
				continue
			FN = str(uuid.uuid4())
			try:
				with open('issn/' + FN + '.json', 'w') as f:
					f.write(req.text)
					print(I + '|' + K['solr'] + '|' + R + '|periodicalitem|' + FN + '.json')
			except:
				sys.stderr.write('JSON error: ' + I + ' -> ' + K['kramerius'] + ' -> ' + R + '\n')
				continue
			# ISSN ROOT PAGE
			try: req = session.get(K['solr'] + 'api/v5.0/search?q=root_pid:' + escape(R) + ' AND document_type:page&fl=PID,parent_pid,rok,title,dc.title&rows=1000000&wt=json')
			except:
				sys.stderr.write('Session error: ' + I + ' -> ' + K['solr'] + ' -> ' + R + '\n')
				continue
			FN = str(uuid.uuid4())
			try:
				with open('issn/' + FN + '.json', 'w') as f:
					f.write(req.text)
					print(I + '|' + K['solr'] +'|' + R + '|page|' + FN + '.json')
			except:
				sys.stderr.write('JSON error: ' + I + ' -> ' + K['solr'] + ' -> ' + R + '\n')
				continue
	session.close()

