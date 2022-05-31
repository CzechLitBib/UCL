#!/usr/bin/python3
#
# Vufind - Export Solr module
#

import requests,io

# VAR

SOLR='http://localhost:8983/solr/biblio/select'

LIMIT='37'

FIELD_LIST = [
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

	fq,q,fl,rows=[],'q=*:*','fl=','rows='

	fq = '&'.join([ 'fq=' + f for f in filter_query ])	#FQ
	if query: q = 'q=' + query				# Q
	fl += ','.join(FIELD_LIST)				# FL
	rows += LIMIT						# ROWS

	return SOLR + '?' + '&'.join([fq, q, fl, rows])

def solr_get(query):
	req = session.get(query)
	if req and req.status_code == 200: return req.json()
	return ''

