#!/usr/bin/python
#
# Morphidita REST API client:
#
# https://lindat.mff.cuni.cz/services/morphodita/api/generate?data=Brno&output=json
#

import httplib,urllib,json,sys,re

SERVER='lindat.mff.cuni.cz'
URL='/services/morphodita/api/generate'
HEADER={'Content-type':'application/x-www-form-urlencoded', 'Accept':'application/json'}

try:
	with open(sys.argv[1], 'r') as f:
		for line in f.readlines():
			c = httplib.HTTPSConnection(SERVER, timeout=10)
			c.request('POST', URL, urllib.urlencode({'data':line, 'output':'json'}), HEADER)
			r = c.getresponse()
			if r.status == 200:
				for res in json.loads(r.read())['result'][0]:
					if re.match('NN[N|F]S6', res['tag']):
						print(res['form'])
except:	print('Error.')
