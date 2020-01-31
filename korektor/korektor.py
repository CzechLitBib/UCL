#!/usr/bin/python
# -*- coding: utf-8 -*-
#
# Simple Korektor REST API client
#
# http://ufal.mff.cuni.cz/korektor
#

import httplib,urllib,json,time,sys,re

SERVER='lindat.mff.cuni.cz'
URL='/services/koretor/api/correct'
HEADER={'Content-type':'application/x-www-form-urlencoded', 'Accept':'application/json'}

c = httplib.HTTPSConnection(SERVER, timeout=10)
c.request('POST', URL, urllib.urlencode({'data':line, 'output':'json'}), HEADER)
r = c.getresponse()
if r.status == 200:
	DATA = json.loads(r.read())['result'][0]
c.close()
time.sleep(0.1)# HTTPS rate limiting 100ms

