#!/usr/bin/python3
#
# 'CAT/KAT' module
#

import json,sys

from datetime import datetime,timedelta

# VAR -------------------

JSON='/var/www/html/cat/data/' + (datetime.today().replace(day=1)-timedelta(days=1)).strftime('%Y/%m/') + 'data.json'

# DEF -------------------

def valid_cat(field):
	if 'c' in field and field['c'][0:6] == (datetime.today().replace(day=1)-timedelta(days=1)).strftime('%Y%m'): return True
	return False

def get_key(val,lst):
	for sif,cat in lst.items():
		if cat == val: return sif

def run(DATA,db):

	buff={}

	# SIF/CAT map
	sif_aleph_map = dict(db.execute("SELECT code,aleph FROM user WHERE aleph IS NOT NULL AND aleph != '';").fetchall())

	# initialize data
	for sif in sif_aleph_map:
		buff[sif] = {
			'new_count': 0,
			'fix_count': 0,
			'fix_other_count': 0,
			'ident': []
		}

	for record in DATA:

		# ident
		if '001' in record:
			ident = record['001'].value()
		else:
			continue

		# SIF
		SIF=''
		if 'SIF' in record and 'a' in record['SIF']: SIF = record['SIF']['a']

		# CAT/KAT list
		cats = record.get_fields('CAT','KAT')

		# creation CAT
		for cat in cats[:1]:
			if valid_cat(cat) and SIF in sif_aleph_map:
				buff[SIF]['new_count'] += 1
				if ident not in buff[SIF]['ident']: buff[SIF]['ident'].append(ident)
		# fix CAT
		for cat in cats[1:]:
			# bot
			if 'a' in cat and cat['a'] in ['BATCH-UPD', 'OIT']: continue
			# old
			if not valid_cat(cat): continue
			# aleph
			if 'a' in cat and cat['a'] in sif_aleph_map.values():
			
				owner = get_key(cat['a'], sif_aleph_map)

				if owner == SIF: # own
					buff[owner]['fix_count'] += 1
					if ident not in buff[owner]['ident']: buff[owner]['ident'].append(ident)
				elif owner: # other
					buff[owner]['fix_other_count'] += 1
					if ident not in buff[owner]['ident']: buff[owner]['ident'].append(ident)

	# Drop zero
	for sif in sif_aleph_map:
		if buff[sif]['new_count'] == buff[sif]['fix_count'] == buff[sif]['fix_other_count'] == 0:
			 del buff[sif]

	# JSON
	with open(JSON, 'w') as f: json.dump(buff, f)

