#!/usr/bin/python
# -*- coding: utf-8 -*-
#
# Aleph MARC 773 -> 756 convertor
#
#    ID                                                  R           D  M      S	
# 001821821 7730 L $$tRudé právo$$x0032-6569$$gRoč. 30, 1950, č. 55, 5. 3., s. 5$$q30:55<5$$919500305
#    ID                       
# XXXXXXXXX 85642 L $$ uYYY $$y Digitální archiv časopisů $$ 4 N
#                                                    R   M D S
# http://archiv.ucl.cas.cz/index.php?path=RudePravo/1950/3/5/5.png
#
#
# CASPIS
# 001532378 7730 L $$tČeská literatura$$x0009-0468$$gRoč. 41, 1993, č. 1, s. 1-24$$q41:1<1$$91993
#
# XXXXXXXXX 85642 L $$ uYYY $$y Digitální archiv časopisů $$ 4 N
#
# http://archiv.ucl.cas.cz/index.php?path=RudePravo/1950/3/5/5.png
#

import urllib2,sys,re

#----------------------------------

IN='773.txt'
PREFIX='http://archiv.ucl.cas.cz/index.php?path=RudePravo/'

TOTAL=0
MATCH=0
NOROC=0
COLON=0
ATTCH=0
MANUA=0
RUBBI=0

#----------------------------------

def url_response(url):
	try:
		req = urllib2.Request(url)
		req.add_header('User-Agent', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:70.0) Gecko/20100101 Firefox/70.0')
		data = urllib2.urlopen(req, timeout=10)
		if data.getcode() == 200:
			if 'Tisk' in data.read(): return 1
	except: pass
	return 0

def convert_regular(g):
	Y = re.sub('^Roč\. (\d+|\[\d+\]), (\d+), č\. \d+, \d+\. \d+\.?, s\. (\d+\.?|\[\d+\]\.?|\d+, \d+|\d+-\d+|\d+ a \d+)$', '\\2', g)
	D = re.sub('^Roč\. (\d+|\[\d+\]), \d+, č\. \d+, (\d+)\. \d+\.?, s\. (\d+\.?|\[\d+\]\.?|\d+, \d+|\d+-\d+|\d+ a \d+)$', '\\2', g)
	M = re.sub('^Roč\. (\d+|\[\d+\]), \d+, č\. \d+, \d+\. (\d+)\.?, s\. (\d+\.?|\[\d+\]\.?|\d+, \d+|\d+-\d+|\d+ a \d+)$', '\\2', g)
	part = re.sub('^Roč\. (\d+|\[\d+\]), \d+, č\. \d+, \d+\. \d+\.?, s\. (\d+\.?|\[\d+\]\.?|\d+, \d+|\d+-\d+|\d+ a \d+)$', '\\2', g)
	S = re.sub('^\[?(\d+)\]?.*', '\\1', part.strip())
	URL = PREFIX + Y.strip() + '/' + M.strip() + '/' + D.strip() + '/' + S + '.png'
	if int(Y) > 1949:
		if url_response(URL):
			print(ID + ' 86541 L $$u' + URL + '$$yDigitální archiv časopisů$$4N')
		else:
			print('BAD: ' + URL)

def convert_noroc(g):
	Y = re.sub('^Roč\. (\d+|\[\d+\]), (\d+), \d+\. \d+\.?, s\. (\d+\.?|\[\d+\]\.?|\d+, \d+|\d+-\d+|\d+ a \d+)$', '\\2', g)
	D = re.sub('^Roč\. (\d+|\[\d+\]), \d+, (\d+)\. \d+\.?, s\. (\d+\.?|\[\d+\]\.?|\d+, \d+|\d+-\d+|\d+ a \d+)$', '\\2', g)
	M = re.sub('^Roč\. (\d+|\[\d+\]), \d+, \d+\. (\d+)\.?, s\. (\d+\.?|\[\d+\]\.?|\d+, \d+|\d+-\d+|\d+ a \d+)$', '\\2', g)
	part = re.sub('^Roč\. (\d+|\[\d+\]), \d+, \d+\. \d+\.?, s\. (\d+\.?|\[\d+\]\.?|\d+, \d+|\d+-\d+|\d+ a \d+)$', '\\2', g)
	S = re.sub('^\[?(\d+)\]?.*', '\\1', part.strip())
	URL = PREFIX + Y.strip() + '/' + M.strip() + '/' + D.strip() + '/' + S + '.png'
	if int(Y) > 1949:
		if url_response(URL):
			print(ID + ' 86541 L $$u' + URL + '$$yDigitální archiv časopisů$$4N')
		else:
			print('BAD: ' + URL)

def convert_nos(g,q):
	Y = re.sub('^Roč\. (\d+|\[\d+\]), (\d+), č\. \d+, \d+\. \d+\.?$', '\\2', g)
	D = re.sub('^Roč\. (\d+|\[\d+\]), \d+, č\. \d+, (\d+)\. \d+\.?$', '\\2', g)
	M = re.sub('^Roč\. (\d+|\[\d+\]), \d+, č\. \d+, \d+\. (\d+)\.?$', '\\2', g)
	S = re.sub('\d+:\d+<(\d+)', '\\1', q).strip()
	URL = PREFIX + Y.strip() + '/' + M.strip() + '/' + D.strip() + '/' + S + '.png'
	if int(Y) > 1949:
		if url_response(URL):
			print(ID + ' 86541 L $$u' + URL + '$$yDigitální archiv časopisů$$4N')
		else:
			print('BAD: ' + URL)

def convert_noroc_nos(g,q):
	Y = re.sub('^Roč\. (\d+|\[\d+\]), (\d+), \d+\. \d+\.?$', '\\2', g)
	D = re.sub('^Roč\. (\d+|\[\d+\]), \d+, (\d+)\. \d+\.?$', '\\2', g)
	M = re.sub('^Roč\. (\d+|\[\d+\]), \d+, \d+\. (\d+)\.?$', '\\2', g)
	S = re.sub('\d+:\d+<(\d+)', '\\1', q).strip()
	URL = PREFIX + Y.strip() + '/' + M.strip() + '/' + D.strip() + '/' + S + '.png'
	if int(Y) > 1949:
		if url_response(URL):
			print(ID + ' 86541 L $$u' + URL + '.png$$yDigitální archiv časopisů$$4N')
		else:
			print('BAD: ' + URL)

def convert_att(g):
	Y = re.sub('^Roč\. (\d+|\[\d+\]), (\d+), č\. \d+, \d+\. \d+\.?,.*', '\\2', g)
	D = re.sub('^Roč\. (\d+|\[\d+\]), \d+, č\. \d+, (\d+)\. \d+\.?,.*', '\\2', g)
	M = re.sub('^Roč\. (\d+|\[\d+\]), \d+, č\. \d+, \d+\. (\d+)\.?,.*', '\\2', g)
	part = re.sub('.*s\. (\d+\.?|\[\d+\]\.?|\d+, \d+|\d+-\d+|\d+ a \d+)$', '\\1', g)
	S = re.sub('^\[?(\d+)\]?.*', '\\1', part.strip())
	URL = PREFIX + Y.strip() + '/' + M.strip() + '/' + D.strip() + '/' + S + '_.png'
	if int(Y) > 1949:
		if url_response(URL):
			print(ID + ' 86541 L $$u' + URL + '$$yDigitální archiv časopisů$$4N')
		else:
			print('BAD: ' + URL)

#----------------------------------

f = open(IN, 'r')

for LINE in f:
	if re.match('.*L \$\$tRudé právo.*', LINE):
		TOTAL+=1
		ID = LINE[:9]

		G = re.sub('^.*\$\$g(.*)\$\$q.*$', '\\1', LINE)
		Q = re.sub('^.*\$\$q(.*)\$\$.*$', '\\1', LINE)
		# REGULAR
		#                                   R              D     M                              S	
		if re.match('^Roč\. (\d+|\[\d+\]), \d+, č\. \d+, \d+\. \d+\.?, s\. (\d+\.?|\[\d+\]\.?|\d+, \d+|\d+-\d+|\d+ a \d+)$', G):
			MATCH+=1
			convert_regular(G)
			continue
		# NO C.
		if re.match('^Roč\. (\d+|\[\d+\]), \d+, \d+\. \d+\.?, s\. (\d+\.?|\[\d+\]\.?|\d+, \d+|\d+-\d+|\d+ a \d+)$', G):
			NOROC+=1
			convert_noroc(G)
			continue
		# MULTI
		if re.match('.*;.*', G):
			COLON+=1
			SPART=re.sub('^([^;]+).*', '\\1', G).strip()
	
			if re.match('.* [Pp]říl.*', SPART):
				#print(LINE).strip()# 2
				continue
			if re.match('.*s\..*', SPART) or '<' in Q:
				if re.match('^Roč\. (\d+|\[\d+\]), \d+, č\. \d+, \d+\. \d+\.?, s\. (\d+\.?|\[\d+\]\.?|\d+, \d+|\d+-\d+|\d+ a \d+)$', SPART):
					convert_regular(SPART)
					continue
				if re.match('^Roč\. (\d+|\[\d+\]), \d+, \d+\. \d+\.?, s\. (\d+\.?|\[\d+\]\.?|\d+, \d+|\d+-\d+|\d+ a \d+)$', SPART):
					convert_noroc(SPART)
					continue
				if re.match('^Roč\. (\d+|\[\d+\]), \d+, \d+\. \d+\.?$', SPART) and re.match('.*<\d+', Q):
					convert_noroc_nos(SPART,Q)
					continue
				
				if re.match('^Roč\. (\d+|\[\d+\]), \d+, č\. \d+, \d+\. \d+\.?', SPART) and re.match('.*<\d+', Q):
					convert_nos(SPART,Q)
					continue
			#print(LINE).strip()# 5
			continue
		# ATTACH
		if re.match('.* [Pp]říl.*', G):
			ATTCH+=1
			if re.match('^Roč\. (\d+|\[\d+\]), \d+, č\. \d+, \d+\. \d+\.?,.* s\. (\d+\.?|\[\d+\]\.?|\d+, \d+|\d+-\d+|\d+ a \d+)$', G):
				convert_att(G)
				continue
			else:
				#print(LINE).strip()# 9
				continue
		# MANUAL
		if re.match('.*s\..*', G) or re.match('.*<\d+',Q):
			MANUA+=1
			#if re.match('^Roč\. (\d+|\[\d+\]), \d+, č\. \d+, \d+\. \d+\.?,.*$', G):
			#	#print(LINE).strip()
			#	continue
			#if re.match('^Roč\. (\d+|\[\d+\]), \d+, \d+\. \d+\.?,.*$', G):
			#	#print(LINE).strip()
			#	continue
			#if re.match('.*<\d+',Q):
			#	print(LINE).strip()
			#	continue
		
			#print(LINE).strip()# 21
			continue
		# RUBBISH
		RUBBI+=1
		#print(LINE).strip()# 164

#print('-----------------------')
#print('TOTAL: ' + str(TOTAL))
#print('MATCH: ' + str(MATCH))
#print('NOROC: ' + str(NOROC))
#print('COLON: ' + str(COLON))
#print('ATTCH: ' + str(ATTCH))
#print('MANUA: ' + str(MANUA))
#print('RUBBI: ' + str(RUBBI))

f.close()

