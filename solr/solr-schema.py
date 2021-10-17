#!/usr/bin/python3
#
# Solr API v2 schema tool
#

import argparse,requests,json,sys

URL='http://localhost:8983/solr/core/schema'

# ARGS

parser = argparse.ArgumentParser(description='Solr V2 API tool.')
required = parser.add_argument_group('API')
required.add_argument('--add', help='Add fields.', action='store_true')
required.add_argument('--delete', help='Delete fields.', dest='field')
required.add_argument('--list', help='List fields.', action='store_true')
args = parser.parse_args()

if not (args.add or args.field or args.list):
	parser.error('Argument is required.')

# MAIN

session = requests.Session()
session.headers.update({'Content-type':'application/json'})

if args.add:# Add Fields
	field = {'add-field':{'name':'', 'type':''}}# indexed / stored / uninvertible (default true)

	field['add-field']['name'] = '245'
	field['add-field']['type'] = 'strings'# string | strings (mv)

	resp = session.post(URL, data=json.dumps(field))

	if resp and resp.status_code == 200:
		print("ok")
	else:
		print(resp.text)

if args.field:# Delete Fields
	field = {'delete-field':{'name':''}}

	field['delete-field']['name'] = args.field

	resp = session.post(URL, data=json.dumps(field))

	if resp and resp.status_code == 200:
		print("ok")
	else:
		print(resp.text)

if args.list:# List Fields
	resp = session.get(URL + '/fields')

	if resp and resp.status_code == 200:
		data = json.loads(resp.text)
		for F in data['fields']:
			print(F['name'])
	else:
		print(resp.text)

session.close()
sys.exit(0)

