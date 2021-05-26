#!/usr/bin/python
# -*- coding: utf-8 -*-
#
# Aleph2894 parser
#

from __future__ import print_function

import sys,os,re

IN='Aleph2894.txt'
OUT='2894.csv'

def get_field(buff):
	subs=''
	data = buff.split('|')[1:]
	for sub in data:
		subs += '$' + sub[:1] + sub[1:].strip()
	return subs

with open(IN, 'r') as f:

	TOKEN=False
	TOKEN_SYS=False
	TOKEN_022=False
	TOKEN_100=False
	TOKEN_245=False
	TOKEN_260=False
	TOKEN_264=False
	TOKEN_856=False

	DATA_SYS=''
	DATA_022=''
	DATA_100=''
	DATA_245=''
	DATA_260=''
	DATA_264=''
	DATA_856=''

	IDENT=''
	BUFFER=''

	for line in f:
		#if TOKEN: print('-------------------------------')
		if TOKEN_SYS:
			DATA_SYS += line.strip()
			if not line.strip():
				#print(DATA_SYS)
				IDENT=DATA_SYS
				TOKEN_SYS=False
				DATA_SYS=''
		if TOKEN_022:
			DATA_022 += line.strip()
			if not line.strip():
				#print(DATA_022)
				TOKEN_022=False
				BUFFER+=IDENT + ';022;' + get_field(DATA_022) + '\n'
				DATA_022=''
		if TOKEN_100:
			DATA_100 += line.strip()
			if not line.strip():
				#print(DATA_100)
				TOKEN_100=False
				BUFFER+=IDENT + ';100;' + get_field(DATA_100) + '\n'
				#print(BUFFER)
				DATA_100=''
		if TOKEN_245:
			DATA_245 += line.strip()
			if not line.strip():
				#print(DATA_245)
				TOKEN_245=False
				BUFFER+=IDENT + ';245;' + get_field(DATA_245) + '\n'
				DATA_245=''
		if TOKEN_260:
			DATA_260 += line.strip()
			if not line.strip():
				#print(DATA_260)
				TOKEN_260=False
				BUFFER+=IDENT + ';260;' + get_field(DATA_260) + '\n'
				DATA_260=''
		if TOKEN_264:
			DATA_264 += line.strip()
			if not line.strip():
				#print(DATA_264)
				TOKEN_264=False
				BUFFER+=IDENT + ';264;' + get_field(DATA_264) + '\n'
				DATA_264=''
		if TOKEN_856:
			DATA_856 += line.strip()
			if not line.strip():
				#print(DATA_856)
				TOKEN_856=False
				BUFFER+=IDENT + ';856;' + get_field(DATA_856) + '\n'
				DATA_856=''

		if 'Záznam dokumentu' in line: TOKEN=True
		if re.match('^      001$', line.rstrip()): TOKEN_SYS=True
		if re.match('^      022$', line.rstrip()): TOKEN_022=True
		if re.match('^      1001$', line.rstrip()): TOKEN_100=True
		if re.match('^      245[0,1]0$', line.rstrip()): TOKEN_245=True
		if re.match('^      260$', line.rstrip()): TOKEN_260=True
		if re.match('^      264 1$', line.rstrip()): TOKEN_264=True
		if re.match('^      856( 1|4|41)$', line.rstrip()): TOKEN_856=True

with open(OUT, 'w') as f:
	f.write(BUFFER)

sys.exit(0)

