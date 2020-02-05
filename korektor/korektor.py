#!/usr/bin/python
# -*- coding: utf-8 -*-
#
# Simple Korektor REST API client
#
# http://ufal.mff.cuni.cz/korektor
#

import httplib,urllib,json,time,sys,re

SERVER='lindat.mff.cuni.cz'
#URL='/services/korektor/api/suggestions'
URL='/services/korektor/api/correct'
HEADER={'Content-type':'application/x-www-form-urlencoded', 'Accept':'application/json'}

MATCH=0
NOTMATCH=0

f = open('5xx.txt', 'r')
o = open('korektor.txt', 'a')
lines = f.readlines()
f.close()

for line in lines:
	c = httplib.HTTPSConnection(SERVER, timeout=10)
	c.request('POST', URL, urllib.urlencode({'data':line, 'output':'json'}), HEADER)
	r = c.getresponse()
	if r.status == 200:
		DATA = json.loads(r.read())['result'].strip()
		if DATA != line.decode('utf-8').strip():
			o.write('---------------------------------------' + '\n')
			o.write(line + '\n')
			o.write(DATA.encode('utf-8') + '\n')
			NOTMATCH+=1
		else:
			MATCH+=1
	c.close()
	time.sleep(0.1)# HTTPS rate limiting 100ms

print('Match: ' + str(MATCH))
print('Not match: ' + str(NOTMATCH))

o.close()

