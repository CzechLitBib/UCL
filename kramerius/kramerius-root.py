#!/usr/bin/python3
#
# Download complete UUID structure.
#
# TODO: skip DUP root
# TODO: fix struct
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

import requests,json,uuid,sys

ROOT={}
MAP={}

with open('root.json', 'r') as f: ROOT = json.loads(f.read())

# ANALYZE STRUCT
for I in ROOT:
	for K in ROOT[I]:
		session = requests.Session()
		for R in ROOT[I][K]:
			req = session.get(K + 'api/v5.0/item/' + R + '/children')
			if req.status_code == 200:
				print(I + ' -> ' + K + ' -> ' + R)
				PASS=True
				# HAS VOLUME
				raw = json.loads(req.text, strict=False)
				data = json.loads(req.text, strict=False)[:1]
				if data:
					if data[0]['model'] == 'periodicalvolume':
						if not 'details' in data[0]:
							print("No detail.")
							PASS=False
						else:
							if not 'year' in data[0]['details']:
								print("No volume year.")
								PASS=False
							#if not 'volumeNumber' in data[0]['details']:
							#	print("No volume number.")
							#	PASS=False
					else:
						print(data[0])
						print("Not volume: " + data[0]['model'])
					if not 'pid' in data[0] or 'volume_pid' in data[0]:
						print("No volume PID.")
						PASS=False
					if not PASS:
						print(data[0])
						input("Press Enter to continue...")
				else:
					print("No data.")
sys.exit(1)

# HARVEST
for I in ROOT:
	if I not in MAP: MAP[I]={}# update MAP
	for K in ROOT[I]:
		if K not in MAP[I]: MAP[I][K]={}# update MAP

		session = requests.Session()

		for R in ROOT[I][K]:
		
			DATA=[]
			FILE = str(uuid.uuid4()) + '.json'
			MAP[I][K][R]=FILE# update MAP
			
			VOLUME_INDEX=0
			ISSUE_INDEX=0

			req = session.get(K + 'api/v5.0/item/' + R + '/children')
		
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
					req = session.get(K + 'api/v5.0/item/' + volume_pid + '/children')
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
							req = session.get(K + 'api/v5.0/item/' + issue_pid + '/children')
							if req.status_code == 200:
								# PAGE
								for page in json.loads(req.text, strict=False):
									page_name = page['title']
									page_pid = page['pid']
									DATA[VOLUME_INDEX]['issue'][ISSUE_INDEX]['page'][page_name] = page_pid
							# update indexes
							ISSUE_INDEX+=1
					# update /reset indexes
					VOLUME_INDEX+=1
					ISSUE_INDEX=0
			# write ISSN
			with open('issn/' + FILE, 'w') as f: f.write(json.dumps(DATA))
		# close session
		session.close()

with open('map.json', 'w') as f: f.write(json.dumps(MAP))

