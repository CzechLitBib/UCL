#!/usr/bin/python3
#
# Download complete UUID structure.
#
# [ {
#     'volume_year' : volume_year,
#     'volume_number' : volume_number,
#     'volume_pid' : volume_pid,
#     'issue' : [
#                  {
#                    'issue_date' : issue_date
#                    'issue_pid' : issue_pid
#                    'issue_number' : issue_number
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

import requests,json,uuid

ROOT={}
MAP={}

with open('root.json', 'r') as f: ROOT = json.loads(f.read())

for I in ROOT:
	for K in ROOT[I]:
	
		session = requests.Session()
		print(I + ' Downloading from .. ' + K)
		for R in ROOT[I][K]:
	
			print('Root: ' + R)

			DATA=[]
		
			VOLUME_INDEX=0
			ISSUE_INDEX=0
		
			try: req = session.get(K + 'api/v5.0/item/' + R + '/children')
			except:
				print('Connection error.')
				continue
			if req.status_code == 200:
				# VOLUME
				for volume in json.loads(req.text, strict=False):
					if volume['model'] != 'periodicalvolume': continue#  skip no volume
					volume_year = volume['details']['year']
					volume_number=''
					if 'volumeNumber' in volume['details']: volume_number = volume['details']['volumeNumber']
					volume_pid = volume['pid']
					DATA.append({
							'volume_year':volume_year,
							'volume_number':volume_number,
							'volume_pid':volume_pid,
							'issue':[]
					})
					try: req = session.get(K + 'api/v5.0/item/' + volume_pid + '/children')
					except:
						print('Connection error.')
						continue
					if req.status_code == 200:
						# ISSUE
						for issue in json.loads(req.text, strict=False):
							if issue['model'] != 'periodicalitem': continue# skip no issue
							issue_date = issue['details']['date']
							issue_pid = issue['pid']
							issue_number = ''
							if 'partNumber' in issue['details']: issue_number = issue['details']['partNumber']
							DATA[VOLUME_INDEX]['issue'].append({
								'issue_date':issue_date,
								'issue_pid':issue_pid,
								'issue_number':issue_number,
								'page':{}
							})
							try: req = session.get(K + 'api/v5.0/item/' + issue_pid + '/children')
							except:
								print('Connection error.')
								continue
							if req.status_code == 200:
								# PAGE / ARTICLE
								for page in json.loads(req.text, strict=False):
									if page['model'] not in ['page', 'article']: continue# skip no p/a
									page_name = page['title']
									page_pid = page['pid']
									DATA[VOLUME_INDEX]['issue'][ISSUE_INDEX]['page'][page_name] = page_pid
							# update indexes
							ISSUE_INDEX+=1
					# update /reset indexes
					VOLUME_INDEX+=1
					ISSUE_INDEX=0
			# write ISSN
			if DATA:
				if I not in MAP: MAP[I]={}
				if K not in MAP[I]: MAP[I][K]={}

				FILE = str(uuid.uuid4()) + '.json'

				MAP[I][K][R]=FILE# update MAP

				with open('issn/' + FILE, 'w') as f: f.write(json.dumps(DATA))

			print(R + ' Done.')
		# close session
		session.close()

with open('map.json', 'w') as f: f.write(json.dumps(MAP))

