#!/usr/bin/python
#
# Simple Morphidita REST API client
#
# http://ufal.mff.cuni.cz/morphodita
#

import httplib,urllib,json,time,sys,re

SERVER='lindat.mff.cuni.cz'
URL='/services/morphodita/api/generate'
HEADER={'Content-type':'application/x-www-form-urlencoded', 'Accept':'application/json'}

bad = open('six-bad.log', 'a', 0)
out = open('six-out.log', 'a', 0)

try:
	with open(sys.argv[1], 'r') as f:
		for line in f.readlines():
			c = httplib.HTTPSConnection(SERVER, timeout=10)
			c.request('POST', URL, urllib.urlencode({'data':line, 'output':'json'}), HEADER)
			r = c.getresponse()
			if r.status == 200:
				DATA = json.loads(r.read())['result'][0]
				if len(DATA) == 0:
					bad.write(line)
				else:
					for res in DATA:
						if re.match('NN.S6', res['tag']):
							out.write(line.strip() + ':' + res['form'].encode('utf-8') + '\n')
			else:
				print("Response error: " + str(r.status))
			time.sleep(1)# HTTPS rate limiting
except: print('Error.')

bad.close()
out.close()

