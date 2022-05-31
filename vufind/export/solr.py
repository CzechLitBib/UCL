#!/usr/bin/python3
#
# Vufind - Export Solr module
#

import requests,io

# VAR

SOLR='http://localhost:8983/solr/biblio/select'

LIMIT='10000'

FL = [
	'id',
	'export_100a_str',
	'export_100bc_str',
	'export_245_str',
	'export_260264_str_mv',
	'export_490_str_mv',
	'export_520a_str_mv',
	'article_resource_str_mv',
	'export_6xx_str_mv',
	'export_773g_str_mv',
	'export_787_str_mv',
	'info_resource_str_mv'
]

# INIT

session = requests.Session()

# DEF

def solr_query(query, filter_query):

	fq,q,fl,rows='','q=*:*','fl=','rows='

	for f in filter_query: fq += 'fq=' + f	# FQ
	if query: q = 'q=' + query		# Q
	fl = ','.join(FL)			# FL
	'&rows=' + LIMIT			# ROWS

	return SOLR + '?' + '&'.join(fq, q, fl, rows)

def get_solr(query):
	req = session.get(query)
	if req and req.status_code == 200: return req.json()
	return ''

