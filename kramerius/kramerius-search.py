#!/usr/bin/python3
#
# https://registr.digitalniknihovna.cz/
#
# Search ISSN root over digital registry.
#

import requests,json

KRAMERIUS,ISSN,ROOT=[],[],{}

with open('issn.txt', 'r') as f: ISSN = f.read().splitlines()
with open('kramerius.json', 'r') as f: KRAMERIUS = json.loads(f.read())

for K in KRAMERIUS:
	session = requests.Session()
	for I in ISSN:
		if '#' in I: I =I.split('#')[0]
		try:
			req = session.get(K + 'api/v5.0/search?q=issn:' + I + '&wt=json')
			if req.status_code == 200:
				resp = json.loads(req.text)
				if resp['response']['numFound'] > 0:
					for doc in resp['response']['docs']:
						if I not in ROOT: ROOT[I]={}
						if K not in ROOT[I]: ROOT[I][K]=[]
						if doc['root_pid'] not in ROOT[I][K]:
							ROOT[I][K].append(doc['root_pid'])
		except: pass
	session.close()

with open('root.json', 'w') as f: f.write(json.dumps(ROOT))

