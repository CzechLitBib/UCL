#!/usr/bin/python3
#
# 'CAT/KAT' module
#

import json

from datetime import datetime,timedelta

# VAR -------------------

KAT_CODE='/usr/local/bin/code/kat.txt'

JSON='/var/www/html/kat/data/' + (datetime.today()-timedelta(days=1)).strftime('%Y/%m/') + 'data.json'

# DEF -------------------

def get_sif_map():
	try:
		sif_code = {}
		with open(KAT_CODE, 'r') as f:
			for line in f:
				user_name, user_code = line.split('#')
				sif_code[user_name] = user_code.strip()
		return sif_code
	except:
		return {}

def get_cat(record):
	out = []
	for F in record.get_fields('CAT','KAT'):
		if 'a' in F and 'BATCH' not in F['a']:# not a bot
			if 'c' in F and F['c'][0:6] == (datetime.today()-timedelta(days=1)).strftime('%y%m'):
				out.append(F['a'])
	return out

def get_key(val,lst):
	for sif,cat in lst.items():
		if cat == val:
			return sif

def run(DATA):

	buff={}

	# SIF/CAT map
	sif_cat_map = get_sif_map()

	# initialize data
	for sif_parent in sif_cat_map:
		buff[sif_parent] = {
			'sif_count': 0,
			'cat_count': 0,
			'sif_cat_count': 0,
			'other': {},
			'ident': []
		}
		for sif_child in sif_cat_map:
			if sif_parent != sif_child:
				buff[sif_parent]['other'][sif_child] = 0

	for record in DATA:

		# ident
		ident = record['001'].value()

		# SIF
		SIF=''
		if 'SIF' in record and 'a' in record['SIF']:
				SIF = record['SIF']['a'].upper()

		# SIF count
		if SIF in sif_cat_map:
			buff[SIF]['sif_count']+=1

		# KAT count	
		for cat in get_cat(record):
			if cat in sif_cat_map:
				if SIF in sif_cat_map.values():
					# SELF
					if SIF == get_key(cat,sif_cat_map):
						# KAT count
						buff[SIF]['cat_count']+=1
						# SIF/KAT count
						buff[SIF]['sif_cat_count']+=1
						# IDENT
						if ident not in buff[SIF]['ident']:
							buff[SIF]['ident'].append(ident)
					# OTHER
					else:
						# KAT count
						buff[get_key(cat,sif_cat_map)]['cat_count']+=1
						# OTHER count
						buff[SIF]['other'][get_key(cat,sif_cat_map)]+=1
						# IDENT
						if ident not in buff[get_key(cat,sif_cat_map)]['ident']:
							buff[get_key(cat,sif_cat_map)]['ident'].append(ident)
				else:
					# KAT count
					buff[get_key(cat,sif_cat_map)]['cat_count']+=1
					# IDENT
					if not ident in buff[get_key(cat,sif_cat_map)]['ident']:
						buff[get_key(cat,sif_cat_map)]['ident'].append(ident)

	# JSON
	with open(JSON, 'w') as f:
		json.dump(buff, f)

