#!/usr/bin/python
# -*- coding: utf-8 -*-
#
# kramerius5.nkp.cz
# digitalniknihovna.cz/mzk
# kramerius.lib.cas.cz
#
# Kramerius grab & link
#
# TODO: Collection -> Volume -> Issue -> Page
#

from __future__ import print_function

import requests,json,time

# 0009-0468 Česká literatura
# Ceska literatura uuid:f9f595d7-4116-11e1-99e8-005056a60003
#
# [ {
#     'volume_year' : volume_year,
#     'volume_number' : volume_number,
#     'volume_uuid' : volume_uuid
#     'volume_page' : {
#                       '1a' = page_uuid
#                       '......
#   }
#   ......
# ]                      '
#

CESLIT='uuid:f9f595d7-4116-11e1-99e8-005056a60003'
DATA=[]
INDEX=0

session = requests.Session()

req = session.get('https://kramerius.lib.cas.cz/search/api/v5.0/item/' + CESLIT + '/children')
if req.status_code == 200:
	for parent in json.loads(req.text, strict=False):
		volume_parent = parent['pid']
		volume_number = parent['details']['volumeNumber']
		volume_year = parent['details']['year']
		DATA.append({'volume_year':volume_year,'volume_number':volume_number})
		# VOLUME
		req = session.get('https://kramerius.lib.cas.cz/search/api/v5.0/item/' + volume_parent + '/children')
		if req.status_code == 200:
			for volume in json.loads(req.text, strict=False):
				volume_uuid = volume['pid']
				DATA[INDEX]['volume_uuid'] = volume_uuid
				DATA[INDEX]['volume_page'] = {}
				# ISSUE
				req = session.get('https://kramerius.lib.cas.cz/search/api/v5.0/item/' + volume_uuid + '/children')
				if req.status_code == 200:
					for page in json.loads(req.text, strict=False):
						page_pid = page['pid']
						page_name = page['title']
						DATA[INDEX]['volume_page'][page_name] = page_pid

		INDEX+=1
		time.sleep(1)

with open('ceslit.json', 'w') as ceslit:
	ceslit.write(json.dumps(DATA))

