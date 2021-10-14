#!/usr/bin/python
# -*- coding: utf-8 -*-
#
# Aleph KAT/SIF  to CSV 
#
# SysNo; SIF; 1. výskyt CAT-c rok; 1. výskyt CAT-c měsíc; CATa; CATc-rok; CATc-měsíc; CATc cele
#
# 000989107 CAT   L $$aUCLMI$$b50$$c20170215$$lKNA01$$h2309
# 000989107 CAT   L $$aUCLLS$$b50$$c20190321$$lKNA01$$h1148
# 000989107 SIF   L $$ami
#

from __future__ import print_function

import sys,os,re

IN='uclacatsif.bib'
#IN='demo.bib'
OUT='uclacatsif.csv'

BUFF=[]
FIRST=''

csv = open(OUT, 'a', 0)

with open(IN, 'r') as f:
	for LINE in f:
		DATA = LINE.strip().split(' ')
		# SIF
		if DATA[1] == 'SIF':
			SIF = re.sub('\$\$a(.*)', '\\1', DATA[5]).decode('utf-8')
			for BATCH in BUFF:
				BATCH.insert(1,SIF)
				csv.write('||'.join(BATCH).encode('utf-8') + '\n')
			FIRST=''
			BUFF=[]
			#sys.exit(0)
		# DATA
		else:
			IDENT = DATA[0]
			A,C,M='','',''
			for VAL in DATA[5].strip().split('$$'):
				if re.match('^a', VAL): KAT = re.sub('^a(.*)', '\\1', VAL)
				if re.match('^c', VAL):
					Y = VAL[1:5] # YEAR
					M = VAL[5:7] # MONTH
			if not FIRST: FIRST=(Y,M)
			BUFF.append([IDENT,FIRST[0],FIRST[1],KAT,Y,M,DATA[5].strip()])
			#print([IDENT,FIRST[0],FIRST[1],KAT,Y,M,DATA[5].strip()])
			#sys.exit(1)
csv.close()

