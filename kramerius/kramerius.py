#!/usr/bin/python3
#
# kramerius5.nkp.cz
# digitalniknihovna.cz/mzk
# kramerius.lib.cas.cz
#
# https://registr.digitalniknihovna.cz/
#
# 0009-0468 Česká literatura
#
# Ceska literatura uuid:f9f595d7-4116-11e1-99e8-005056a60003
#
# [ {
#     'volume_year' : volume_year,
#     'volume_number' : volume_number,
#     'volume_pid' : volume_pid,
#     'issue' : [
#                  {
#                    'issue_date' : issue_date
#                    'issue_pid' : issue_pid
#	             'page' : {
#                                page_name : page_pid
#                                ......
#                             }
#                  }
#                  ......
#               ]
#   }
#   ......
# ]
#

import requests,json,time,sys

#print(json.dumps(resp, indent=4))

KRAMERIUS,ISSN,ROOT=[],[],{}

with open('issn.txt', 'r') as f: ISSN = f.read().splitlines()
with open('kramerius.txt', 'r') as f: KRAMERIUS = f.read().splitlines()

# PRE-SEARCH
for I in ISSN:
#for I in ['0009-0468']:
	if '#' in I: continue# TODO: split milti-issn
	for K in KRAMERIUS:
		session = requests.Session()
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

with open('root.txt', 'w') as f: f.write(json.dumps(ROOT))

sys.exit(1)

# LOOP
VOLUME_INDEX=0
ISSUE_INDEX=0

session = requests.Session()

req = session.get('/item/UUID/children')
if req.status_code == 200:
	# VOLUUME
	for volume in json.loads(req.text, strict=False):
		volume_year = volume['details']['year']
		volume_number = volume['details']['volumeNumber']
		volume_pid = volume['pid']
		DATA.append({
				'volume_year':volume_year,
				'volume_number':volume_number,
				'volume_pid':volume_pid,
				'issue':[]
		})
		#print('volume: ' + volume_year)
		req = session.get(URL + volume_pid + '/children')
		if req.status_code == 200:
			# ISSUE
			for issue in json.loads(req.text, strict=False):
				if issue['model'] != 'periodicalitem': continue# skip index listing page
				issue_date = issue['details']['date']
				issue_pid = issue['pid']
				DATA[VOLUME_INDEX]['issue'].append({
					'issue_date':issue_date,
					'issue_pid':issue_pid,
					'page':{}
				})
				#print('   issue: ' + issue_date)
				req = session.get(URL + issue_pid + '/children')
				if req.status_code == 200:
					# PAGE
					for page in json.loads(req.text, strict=False):
						page_name = page['title']
						page_pid = page['pid']
						DATA[VOLUME_INDEX]['issue'][ISSUE_INDEX]['page'][page_name] = page_pid
						#print('         page: ' + page_name)
				# update indexes
				ISSUE_INDEX+=1
		# update /reset indexes
		VOLUME_INDEX+=1
		ISSUE_INDEX=0
		time.sleep(1)

with open('ceslit.json', 'w') as ceslit:
	ceslit.write(json.dumps(DATA))

