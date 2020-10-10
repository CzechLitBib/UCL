#!/usr/bin/python
# -*- coding: utf-8 -*-

from __future__ import print_function

import subprocess,requests,lxml.html,StringIO,time,os
from pymarc import MARCReader

ISSN='demo.csv'

URL_HEAD='https://aleph.nkp.cz/F/?func=direct&doc_number='
URL_TAIL='&local_base=SKC&format=001'

OUT='scrap.txt'

session = requests.Session()
session.headers.update({'User-Agent':'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:70.0) Gecko/20100101 Firefox/70.0'})

def check_issn(issn,name):
	global session
	print('Trying.. ' + issn)
	scrap.write('Trying .. ' + issn + ' ' + name + '\n')
	os.chdir('ramdisk')
	# create config, bin
	with open('z3950.cfg', 'w') as f:
		f.write('open tcp:aleph.nkp.cz:9991/SKC-UTF\n')
		f.write('set_marcdump rec.bin\n')
		f.write('find @attr 1=8 "' + issn + '"\n')# 8 - issn http://aleph.nkp.cz/web/Z39_NK_cze.htm
		f.write('show 1+100\n')# fetch 1 to 100 records = all
		f.write('close\n')
		f.write('quit\n')
	# call client
	data = subprocess.check_output(['yaz-client', '-f', 'z3950.cfg'])
	# paprse output
	reader = MARCReader(open('rec.bin','rb'))
	for rec in reader:
		if '998' in rec:
			print(URL_HEAD + rec['998'].value() + URL_TAIL)
			req = session.get(URL_HEAD + rec['998'].value() + URL_TAIL)
			if req.status_code == 200:
				p = lxml.html.HTMLParser()
				t = lxml.html.parse(StringIO.StringIO(req.text), p)
				o = t.xpath("//td[@class='td1']")
				for i in range(0,len(o)):
					if o[i].text == '911':
						scrap.write('->' + o[i+1].text.encode('utf-8') + '\n')
			else:
				print("HTTPS error.")
			time.sleep(1)# hold on a second
	# cleanup
	os.remove('z3950.cfg')		
	os.remove('rec.bin')
	os.chdir('../')

scrap = open(OUT, 'w')

with open(ISSN, 'r') as f:
	for line in f:
		cnt,issn,name = line.split('||')
		url = check_issn(issn.strip(),name.strip())

scrap.close()

