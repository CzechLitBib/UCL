#!/usr/bin/python
# -*- coding: utf-8 -*-
#

from __future__ import print_function

import json,sys,os,re

IN='tmp/retrobi.json'

COLON='brig/colon.csv'
DOT='brig/dot.csv'
DOTS='brig/dots.csv'
BRACKET='brig/bracket.csv'
TRIPLE='brig/triple.csv'

# DEF-----------------

def find(path,json):
	node = path.split('/')
	# root
	if node and node[0] not in json: return ''
	root = json[node[0]]
	# leave
	for leaf in node[1:]:
		if leaf in root:
			root = root[leaf][0]
		else:
			return ''
	return root.encode('utf-8')

# MAIN -----------------

clean = open('retro_clean.json', 'w')
#dot = open(DOT, 'w')
#dots = open(DOTS, 'w')
#bracket = open(BRACKET, 'w')
#triple = open(TRIPLE, 'w')

STATE = []

with open(IN, 'rb') as f:
	for LINE in f:

		try:
			jsn = json.loads(LINE.strip().rstrip(','), strict=False)
		except: continue

		ID = jsn['_id']

	#	if find('',j):
	#	print(find('tree/bibliograficka_cast/zdroj/nazev',j))

		if find('state', jsn):
			state =  jsn['state']
			if state not in STATE:
				STATE.append(state)
			
	print(STATE)
				
		#	clean.write(LINE.strip().rstrip(',') + '\n')
		

		#if 'segment_bibliography' in j:
		#	data = j['segment_bibliography'].replace('In: ', '').strip().rstrip('|') 
		#	if name:
		#		print(ID + ' ' + name + ' -> ' + re.sub('(\D+).*','\\1', data))
		#	else:
		#		print(ID + ' ' + re.sub('(\D+).*','\\1', data))

		#if 'segment_title' in j:
			#tit = j['segment_title']
			# colon
			#if ':' not in tit:
			#	col.write(str(ID) + '||' +  tit.encode('utf-8') + '\n')
			# dot
			#if '.' not in tit:
			#	dot.write(str(ID) + '||' +  tit.encode('utf-8') + '\n')
			# dots
			#if u'přel' not in tit and u'Přel' not in tit and len(re.findall('\.', tit)) >= 2:
			#	dots.write(str(ID) + '||' +  tit.encode('utf-8') + '\n')
			# bracket
			#if ']' not in tit.rstrip('|'):
			#	bracket.write(str(ID) + '||' +  tit.encode('utf-8') + '\n')
			# triple			
			#if u'[Básně]' not in tit and  u'[báseň]' not in tit and u'[Báseň]' not in tit and '...' in tit:
			#	triple.write(str(ID) + '||' +  tit.encode('utf-8') + '\n')
		#else:
		#	print(ID)
# EXIT -------------------

clean.close()

#col.close()
#dot.close()
#dots.close()
#bracket.close()
#triple.close()

