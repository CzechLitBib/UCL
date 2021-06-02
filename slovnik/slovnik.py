#!/usr/bin/python
# -*- coding: utf-8 -*-
#
# MediaWiki to Koha Scrapper
#

import requests,StringIO,lxml.html,json,time,sys,re

# VAR ----------------------

OUT='slovnik.csv'

URL='http://slovnikceskeliteratury.cz/list.jsp?startswith='
LINK='http://slovnikceskeliteratury.cz/'
LETTER=['a','b','c','č','d','e','f','g','h','Ch','i','j','k','l','m','n','o','p','q','r','ř','s','š','t','u','v','w','x','y','z','ž']

out = open(OUT, 'w')

# MAIN ----------------------

session = requests.Session()
parser = lxml.html.HTMLParser()

for CHAR in LETTER:
	print("Scrapping.. " + CHAR)
	req = session.get(URL + CHAR)
	if req.status_code == 200:
		t = lxml.html.parse(StringIO.StringIO(req.text), parser)
		o = t.xpath('//div[@class="listLeft"]')
		for i in range(0, len(o)):
			FN,L,UDAY,UPLACE,SDAY,SPLACE = '','','','','',''
			name = o[i].xpath('.//span[@class="title"]')
			if name:
				if name[0].text:
					FN = name[0].text.strip().encode('utf-8')
			link = o[i].xpath('.//a')
			if link:
				L = LINK + link[0].get('href').strip('./').encode('utf-8')
				if link[0].text:
					FN = link[0].text.strip().encode('utf-8')
			uday = o[i].xpath('.//span[@class="datumnarozeni"]')
			if uday: UDAY = uday[0].text.replace('\t','').replace('\n','').strip().encode('utf-8')
			uplace = o[i].xpath('.//span[@class="mistonarozeni"]')
			if uplace: UPLACE = uplace[0].text.replace('\n','').strip().encode('utf-8')
			sday = o[i].xpath('.//span[@class="datumumrti"]')
			if sday: SDAY = sday[0].text.replace('\n','').strip().encode('utf-8')
			splace = o[i].xpath('.//span[@class="mistoumrti"]')
			if splace: SPLACE = splace[0].text.replace('\n','').strip().encode('utf-8')
			# write
			out.write(FN + ';' + L + ';' + UDAY + ';' + UPLACE + ';' + SDAY + ';' + SPLACE + '\n')
	print("Done.")
	time.sleep(1)# do not stress server

# END ----------------------

session.close()
out.close()

sys.exit(0)

