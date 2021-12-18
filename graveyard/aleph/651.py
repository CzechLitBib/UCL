#!/usr/bin/python
# -*- coding: utf-8 -*-

# INCLUDE -------------------

from __future__ import print_function

import sys,os,re

from pymarc import marcxml

# VAR -------------------

XML='ucla.xml'

IN='input/651_wm-UTF8.txt'
OUT='651_wm.aleph'
OUTSTAT='651_wm_stat.txt'

# DEF -----------

def get_value(field):
	ret = []
	for sub in field:
		#if sub[0] != '4':
		if sub[0] != '4' and sub[0] != 'w':
			ret.append(sub[1].strip())
	return ' '.join(ret)

# MAP -----------

MAP={}
STAT={}

with open(IN, 'r') as src:
	for line in src:
		DATA=[]
		orig,raw = line.strip().split(';;;')
		if not raw:
			continue
		field = raw.split('|')
		if '150' not in field[0] and '151' not in field[0]:
			continue
		# 150/151
		DATA.append(field[0].strip())
		# SUB
		for R in field[1:]:
			DATA.append(R[0])
			DATA.append(R[1:].strip())
		#MAP[orig.replace('OPRAVA UCL','').strip()] = DATA
		MAP[orig] = DATA

print(MAP)

sys.exit(1)

# PARSE / WRITE ----------

aleph = open(OUT, 'w')

def validate(record):

	metadata = record

	IDENT = metadata['001'].value()

	if '651' in metadata:
		MOD=False
		for F in metadata.get_fields('651'):
			if get_value(F).encode('utf-8') in MAP:
				MOD=True
				break
		if MOD:
			for F in metadata.get_fields('651'):
				VAL=get_value(F).encode('utf-8')
				if VAL in MAP:
					# stat
					if VAL not in STAT:
						STAT[VAL]=1
					else:
						STAT[VAL]+=1
					if MAP[VAL][0] == '151':
						# Aleph
						SUB=''
						for i in range(0, len(MAP[VAL][1:])/2):
							SUB+='$$' +  MAP[VAL][1:][i*2] +  MAP[VAL][1:][i*2+1]
						SUB+='$$2czenas'
						aleph.write(str(IDENT + ' 651 7 L ') + SUB + '\n')
				else:
					# default
					SUB=''
					for i in range(0, len(F.subfields)/2):
						SUB+='$$' + F.subfields[i*2] + F.subfields[i*2+1]
					aleph.write(str(IDENT + ' 651' + F.indicator1 + F.indicator2 + ' L ') + SUB.encode('utf-8') + '\n')

# MAIN ------------------------------------------------------

marcxml.map_xml(validate, XML)

with open(OUTSTAT, 'w') as f:
	for match in STAT:
		f.write(str(STAT[match]) + str(' -> ') + match + '\n')

aleph.close()

# EXIT -------------------

sys.exit(0)

