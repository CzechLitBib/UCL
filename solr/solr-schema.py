#!/usr/bin/python3
#
# Solr API v2 schema tool
#

import argparse,requests,json,sys

SCHEMA='http://localhost:8983/solr/core/schema'
UPDATE='http://localhost:8983/solr/core/update'
RELOAD='http://localhost:8983/solr/admin/cores?action=RELOAD&core=core'

# ARGS

parser = argparse.ArgumentParser(description='Solr V2 API tool.')
required = parser.add_argument_group('API')
required.add_argument('--add', help='Add fields.', metavar='FIELD')
required.add_argument('--type', help='Field type. [string] [strings]')
required.add_argument('--delete', help='Delete fields.', metavar='FIELD')
required.add_argument('--delete-copy', help='Delete copy fields.', metavar='FIELD')
required.add_argument('--delete-all', help='Delete all data.', action='store_true')
required.add_argument('--reload', help='Reload core.', action='store_true')
required.add_argument('--list', help='List. [fields] [schema]', metavar='TARGET')
args = parser.parse_args()

if not (args.add or args.delete or args.delete_copy or args.delete_all or args.reload or args.list):
	parser.error('Argument is required.')
if args.list and args.list not in ('fields', 'schema'):
	parser.error('Invalid list argument.')
if args.type and args.type not in ('string', 'strings'):
	parser.error('Invalid type argument.')
if args.add and not args.type:
	parser.error('Type argument required.')

# MAIN

session = requests.Session()
session.headers.update({'Content-type':'application/json'})

if args.add and args.type:# Add Fields
	field = {'add-field':{'name':'', 'type':''}}# indexed / stored / uninvertible (default true)

	field['add-field']['name'] = args.add
	field['add-field']['type'] = args.type

	resp = session.post(SCHEMA, data=json.dumps(field))

	if resp and resp.status_code == 200:
		print('.', end='')
	else:
		print('!', end='')

if args.delete:# Delete Fields
	field = {'delete-field':{'name':''}}

	field['delete-field']['name'] = args.delete

	resp = session.post(SCHEMA, data=json.dumps(field))

	if resp and resp.status_code == 200:
		print('.', end='')
	else:
		print('!', end='')

if args.delete_copy:# Delete Copy Fields
	field = {'delete-copy-field':{'source':'','dest':''}}

	field['delete-copy-field']['source'] = args.delete_copy
	field['delete-copy-field']['dest'] = args.delete_copy + '_str'

	resp = session.post(SCHEMA, data=json.dumps(field))

	if resp and resp.status_code == 200:
		print('.', end='')
	else:
		print('!', end='')

if args.delete_all:# Delete All
	query ={'delete':{'query':'*:*'}}

	resp = session.post(UPDATE, data=json.dumps(query))

	if resp and resp.status_code == 200:
		print('ok')
	else:
		print(resp.text)

if args.list:# List Fields
	if args.list == 'fields':
		resp = session.get(SCHEMA + '/fields')
	else:
		resp = session.get(SCHEMA)
	
	if resp and resp.status_code == 200:
		data = json.loads(resp.text)
		if args.list == 'fields':
			for F in data['fields']:
				print(F['name'])
		else:
			print(data)
			
	else:
		print(resp.text)

if args.reload:# Reload Core
	resp = session.get(RELOAD)
	if resp and resp.status_code == 200:
		print('ok')
	else:
		print(resp.text)

session.close()
sys.exit(0)

