#!/usr/bin/python3
#
# Kramerius UUID to Aleph 856 linker
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
#                    'page' : {
#                                page_name : page_pid
#                                ......
#                             }
#                  }
#                  ......
#               ]
#   }
#   ......
# ]


import json,re,sys

IN='in.json'
MAP='map.json'

ALL=0
MATCH=0

def link(ID,ISSN,Y,R,C,S):
	for KRAMERIUS in MAP[ISSN]:
		for ROOT in MAP[ISSN][KRAMERIUS]:
			with open('issn/' + MAP[ISSN][KRAMERIUS][ROOT], 'r') as f: DATA = json.loads(f.read())
			for volume in DATA:
				if Y == volume['volume_year']:
					for issue in volume['issue']:
						for page in issue['page']:
							if S == page:
								URL = (KRAMERIUS.replace('search','view') +
									issue['issue_pid'] + '?page=' +
									issue['page'][page]
								)
								return ID + ' 85641 L $$u' + URL + '$$yKramerius' + '$$4N'

with open(MAP, 'r') as f: MAP = json.loads(f.read())
with open(IN,'r') as f: data = json.loads(f.read())

for rec in data['response']['docs']:
	
	ID,G,Q = rec['id'],'',''

	ISSN = rec['subfield_773-x'][0] 

	if ISSN not in MAP: continue

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
		LINK = link(ID, ISSN, Y, R, C, S)
		if LINK:
			print(LINK)
			MATCH+=1
	elif Y and R and C:
		LINK = link(ID, ISSN, Y, R, C, '')
		if LINK:
			print(LINK)
			MATCH+=1
	ALL+=1

print(MATCH)
print(ALL)

