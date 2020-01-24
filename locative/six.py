#!/usr/bin/python
# -*- coding: utf-8 -*-
#
# Simple Morphidita REST API client
#
# http://ufal.mff.cuni.cz/morphodita
#

import httplib,urllib,json,time,sys,re

SERVER='lindat.mff.cuni.cz'
URL='/services/morphodita/api/generate'
HEADER={'Content-type':'application/x-www-form-urlencoded', 'Accept':'application/json'}

bad = open('six-bad.log', 'w', 0)
out = open('six-out.log', 'w', 0)

with open(sys.argv[1], 'r') as f:
	for line in f.readlines():
		suffix=''
		if re.match('^.+ (pod|nad|u) .+$', line.strip()):# split multiline
			suffix = re.sub('^.+( (pod|nad|u) .+)$','\\1', line.strip())
			line = re.sub('^(.+) (pod|nad|u) .+$','\\1', line.strip())
		c = httplib.HTTPSConnection(SERVER, timeout=10)
		c.request('POST', URL, urllib.urlencode({'data':line, 'output':'json'}), HEADER)
		r = c.getresponse()
		if r.status == 200:
			DATA = json.loads(r.read())['result'][0]
			if len(DATA) == 0:
				bad.write(line)
			else:
				HAS_U,HAS_E,HAS_S = False, False, False
				for tag in [res['tag'] for res in DATA]:
					if re.match('...S.', tag): HAS_S = True
				if HAS_S:
					LOCATIVE = [res['form'] for res in DATA if re.match('NN.S6', res['tag'])]# singular
				else:
					LOCATIVE = [res['form'] for res in DATA if re.match('NN.[P|X][6|X]', res['tag'])]# plural/none
				if LOCATIVE:
					LOCATIVE = [L for L in LOCATIVE if not re.match('.*ovi$', L)]# match 'ovi'
					for L in LOCATIVE:# match 'e/ě'
						if re.match('.*u$', L.encode('utf-8')): HAS_U = True
						if re.match('.*[e|ě]$', L.encode('utf-8')): HAS_E = True
					if HAS_U and HAS_E:# match 'u'
						LOCATIVE = [L for L in LOCATIVE if not re.match('.*u$', L)]
					LOCATIVE = list(dict.fromkeys(LOCATIVE))# duplicity
					LOCATIVE = [L.encode('utf-8') + suffix for L in LOCATIVE]# suffix
					out.write(line.strip() + suffix + ':' + '%%'.join(LOCATIVE) + '\n')
				else:
					bad.write(line)
		else:
			print("Response error: " + str(r.status))
		c.close()
		time.sleep(0.1)# HTTPS rate limiting 100ms

bad.close()
out.close()

