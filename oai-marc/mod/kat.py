#!/usr/bin/python3
#
# 'CAT/KAT' module
#

import sys,os,re

# VAR -------------------

LAST_MONTH=(datetime.today()-timedelta(days=1)).strftime('%y%m')
LAST_MONTH_DIR=(datetime.today()-timedelta(days=1)).strftime('%Y/%m')

KAT_CODE='/usr/local/bin/code/kat.txt'
OUT_DATA='/var/www/html/kat/data/' + LAST_MONTH_DIR

DATA={}

# initialize struct
other={}
for key in list(SIF_MAP.keys()): other[key] = 0
# initialize data
for key in list(SIF_MAP.keys()): DATA[key] = {'sif_count':0, 'cat_count':0, 'sif_cat_count':0, 'other':other.copy(), 'cat_id':[]}

# DEF -------------------

def get_catlist(metadata):
	catlist = metadata.get_fields('CAT','KAT')
	out = []
	for F in catlist:# remove all bots
		if 'a' in F and 'BATCH' not in F['a']:
			if 'c' in F and F['c'][0:6] == LAST_MONTH_DIR.replace('/',''):
				out.append(F['a'])
	return out

def get_key(val,lst):
	for sif,cat in lst.items():
		if cat == val:
			return sif

def run(records):

	for record in records:

		header = record[0]
		metadata = record[1]

		# skip deleted records
		if header.isDeleted(): continue

		ALEPH = re.sub('^.*-(\d+)$', '\\1', header.identifier()).encode('utf-8')

		SIF=''
		if 'SIF' in metadata:
			if 'a' in metadata['SIF']:
				SIF = metadata['SIF']['a'].upper().encode('utf-8')

		catlist = get_catlist(metadata)

		# SIF count
		if SIF in list(SIF_MAP.keys()):
			DATA[SIF]['sif_count']+=1

		# KAT count	
		for cat in catlist:
			if cat in list(SIF_MAP.values()):
				if SIF in list(SIF_MAP.keys()):
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

