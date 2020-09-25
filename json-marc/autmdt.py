#!/usr/bin/python
# -*- coding: utf-8 -*-

from __future__ import print_function

import subprocess,argparse,StringIO,sys,os,re

from datetime import datetime,date
from oaipmh.client import Client
from oaipmh.metadata import MetadataRegistry
from pymarc import marcxml,MARCReader
from lxml.etree import tostring

AUT='aut.csv'

def check_mdt(i,n1):
	ret = 'Failed.'
	# create config, bin
	with open('z3950.cfg', 'w') as f:
		f.write('open tcp:aleph.nkp.cz:9991/AUT-UTF\n')
		f.write('set_marcdump rec.bin\n')
		f.write('find @attr 1=12 "' + i + '"\n')# sys. number http://aleph.nkp.cz/web/Z39_NK_cze.htm
		f.write('show 1\n')
		f.write('close\n')
		f.write('quit\n')
	# call client
	data = subprocess.check_output(['yaz-client', '-f', 'z3950.cfg'])
	# paprse output
	reader = MARCReader(open('rec.bin','rb'))
	for rec in reader:
		if '100' in rec and 'a' in rec['100']:
			if n1.strip(',').decode('utf-8') != rec['100']['a'].strip(','):
				for F in rec.get_fields('400'):
					if 'a' in F:
						if n1.strip(',').decode('utf-8') == F['a'].strip(','):
							ret = '400'
				for F in rec.get_fields('500'):
					if 'a' in F:
						if n1.strip(',').decode('utf-8') == F['a'].strip(','):
							ret = '500'
			else:
				ret = '100'
	# cleanup
	os.remove('z3950.cfg')		
	os.remove('rec.bin')
	return ret

with open(AUT, 'r') as f:
	for line in f:
		ident, name1, name2 = line.split('|') 
		print(ident + ' | ' + name1 + ' | ' + check_mdt(ident,name1))

