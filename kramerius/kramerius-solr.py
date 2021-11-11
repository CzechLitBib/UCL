#!/usr/bin/python3
#
# Kramerius Solr ISSN download.
#

import requests,json,uuid,sys

ROOT='root.json'

with open(ROOT, 'r') as f: MAP = json.loads(f.read())
	for ISSN in ROOT:
		for KRAMERIUS in ROOT[ISSN]:
			session = requests.Session()
			for ROOT in MAP[ISSN][KRAMERIUS]:
				query = 'root_pid:' + ROOT.replace('-','\-').replace(':','\:') + ' AND document_type:page'
				try: req = session.get(KRAMERIUS + 'api/v5.0/search?q=' + query + '&fl=PID,rok,parent_pid,title,dc.title&rows=1000000&wt=json')
				except:
					sys.stderr.write('Session error: ' + ID + '\n')
					continue
				if req.status_code == 200:
					try:
						FN = uuid.uuid4()
						with open('issn/' + FN + '.json', 'w') as f:
							f.write(json.loads(resp))
							print(ISSN + '#' + KRAMERIUS + '#' + FN)
					except:
						sys.stderr.write('JSON error: ' + ID + '\n')
						continue
			session.close()

