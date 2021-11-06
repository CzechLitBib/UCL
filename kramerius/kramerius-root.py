#!/usr/bin/python3
#
# Download complete ISSN structure.
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

import requests,json,sys

ROOTS={}

with open('root.txt', 'r') as f: ROOTS = json.loads(f.read())

for K in ROT 

for I in ROOTS:

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

	with open( + ISSN + '.json', 'w') as f: f.write(json.dumps(DATA))

