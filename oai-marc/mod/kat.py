#!/usr/bin/python3
#
# 'CAT/KAT' module
#

import json

from datetime import datetime,timedelta

# VAR -------------------

KAT_CODE='/usr/local/bin/code/kat.txt'
LAST_MONTH=(datetime.today()-timedelta(days=1)).strftime('%y%m')
DATA_DIR='/var/www/html/kat/data/' + (datetime.today()-timedelta(days=1)).strftime('%Y/%m')

# DEF -------------------

def get_sif_map():
        try:
                kat_code = {}
                with open(KAT_CODE, 'r') as f:
                        for line in f:
                                acct_name, acct_code = line.split(':')
                                sif_code[acct_name] = acct_code.strip()
                return kat_code
        except:
                return {}

def get_kat(record):
	out = []
	for F in record.get_fields('CAT','KAT'):
		if 'a' in F and 'BATCH' not in F['a']:# not a bot
			if 'c' in F and F['c'][0:6] == LAST_MONTH.replace('/',''):
				out.append(F['a'])
	return out

def get_key(val,lst):
	for sif,cat in lst.items():
		if cat == val:
			return sif

def run(DATA):

	BUFF={}

	# initialize data
	for key in kat_map():
		BUFF[key] = {
			'sif_count':0,
			'cat_count':0,
			'sif_cat_count':0,
			'other': {},
			'cat_id':[]
		}

	# SIF/CAT map
	sif_cat_map = get_sif_map()

	for record in DATA:

		# ident
		record['001'].value()
	
		# SIF
		SIF=''
		if 'SIF' in record and 'a' in record['SIF']:
				SIF = record['SIF']['a'].upper()

		# SIF count
		if SIF in sif_cat_map:
			BUFF[SIF]['sif_count']+=1

		# KAT count	
		for cat in get_cat_list(record):
			if cat in sif_cat_map.values():
				if SIF in sif_cat_map:
					# SELF
					if SIF == get_key(cat,SIF_MAP):
						# KAT count
						DATA[SIF]['cat_count']+=1
						# SIF/KAT count
						DATA[SIF]['sif_cat_count']+=1
						# SIF ID
						if ALEPH not in DATA[SIF]['cat_id']:
							DATA[SIF]['cat_id'].append(ALEPH)
					# OTHER
					else:
						# KAT count
						DATA[get_key(cat,SIF_MAP)]['cat_count']+=1
						# OTHER count
						DATA[SIF]['other'][get_key(cat,SIF_MAP)]+=1
						# SIF ID
						if ALEPH not in DATA[get_key(cat,SIF_MAP)]['cat_id']:
							DATA[get_key(cat,SIF_MAP)]['cat_id'].append(ALEPH)
				else:
					# KAT count
					DATA[get_key(cat,SIF_MAP)]['cat_count']+=1
					# KAT ID
					if not ALEPH in DATA[get_key(cat,SIF_MAP)]['cat_id']:
						DATA[get_key(cat,SIF_MAP)]['cat_id'].append(ALEPH)

