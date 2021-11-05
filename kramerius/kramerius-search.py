#!/usr/bin/python3
#
# https://registr.digitalniknihovna.cz/
#

import requests,json

KRAMERIUS,ISSN,ROOT=[],[],{}

with open('issn.txt', 'r') as f: ISSN = f.read().splitlines()
with open('kramerius.txt', 'r') as f: KRAMERIUS = f.read().splitlines()

for K in KRAMERIUS:
	print(K + '.. ')
	session = requests.Session()
	for I in ISSN:
		if '#' in I: continue# TODO: split milti-issn
		try:
			req = session.get(K + 'api/v5.0/search?q=issn:' + I + '&wt=json')
			if req.status_code == 200:
				resp = json.loads(req.text)
				if resp['response']['numFound'] > 0:
					for doc in resp['response']['docs']:
						if doc['dostupnost'] == 'public':
							if I not in ROOT: ROOT[I]={}
							if K not in ROOT[I]: ROOT[I][K]=[]
							if doc['root_pid'] not in ROOT[I][K]:
								ROOT[I][K].append(doc['root_pid'])
		except:
			pass# SSL error.
	session.close()
	print('Done.')

with open('root.txt', 'w') as f: f.write(json.dumps(ROOT))

