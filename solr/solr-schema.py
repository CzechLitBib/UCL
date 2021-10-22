#!/usr/bin/python3
#
# Solr API v2 schema tool
#

import argparse,requests,json,sys

SOLR='http://localhost:8983/solr/'
RELOAD='http://localhost:8983/solr/admin/cores?action=RELOAD&core='
STATUS='http://localhost:8983/solr/admin/cores?action=STATUS&core='

# ARGS

parser = argparse.ArgumentParser(description='Solr V2 API tool.')
required = parser.add_argument_group('API')
required.add_argument('--add', help='Add [string|strings] field to core.', nargs=3, metavar=('CORE','FIELD','TYPE'))
required.add_argument('--delete', help='Delete field from core.', nargs=2, metavar=('CORE','FIELD'))
required.add_argument('--delete-copy', help='Delete copy field from core.', nargs=2, metavar=('CORE','FIELD'))
required.add_argument('--delete-all', help='Delete all core data.', metavar='CORE')
required.add_argument('--list', help='List core [fields|schema].', nargs=2, metavar=('CORE','TARGET'))
required.add_argument('--reload', help='Reload core.', metavar='CORE')
required.add_argument('--status', help='Core satus.', metavar='CORE')
args = parser.parse_args()

if not any(vars(args).values()): parser.error('Argument is required.')

# MAIN

session = requests.Session()
session.headers.update({'Content-type':'application/json'})

if args.add:# Add Fields
	field = {'add-field':{'name':'', 'type':''}}# indexed / stored / uninvertible (default true)

	field['add-field']['name'] = args.add[1]
	field['add-field']['type'] = args.add[2]

	resp = session.post(SOLR + args.add[0] + '/schema', data=json.dumps(field))

	if resp and resp.status_code == 200:
		print('.', end='')
	else:
		print('!', end='')

if args.delete:# Delete Fields
	field = {'delete-field':{'name':''}}

	field['delete-field']['name'] = args.delete[1]

	resp = session.post(SOLR + args.delete[0] + '/schema', data=json.dumps(field))

	if resp and resp.status_code == 200:
		print('.', end='')
	else:
		print('!', end='')

if args.delete_copy:# Delete Copy Fields
	field = {'delete-copy-field':{'source':'','dest':''}}

	field['delete-copy-field']['source'] = args.delete_copy[1]
	field['delete-copy-field']['dest'] = args.delete_copy[1] + '_str'

	resp = session.post(SOLR + args.delete_copy[0] + '/schema', data=json.dumps(field))

	if resp and resp.status_code == 200:
		print('.', end='')
	else:
		print('!', end='')

if args.delete_all:# Delete All
	query ={'delete':{'query':'*:*'}}

	resp = session.post(SOLR + args.delete_all + '/update', data=json.dumps(query))

	if resp and resp.status_code == 200:
		print('ok')
	else:
		print(resp.text)

if args.list:# List Fields
	if args.list == 'fields':
		resp = session.get(SOLR + args.list + '/schema/fields')
	else:
		resp = session.get(SOLR + args.list + '/schema')
	
	if resp and resp.status_code == 200:
		data = json.loads(resp.text)
		if args.list[1] == 'fields':
			for F in data['fields']:
				print(F['name'])
		else:
			print(data)
			
	else:
		print(resp.text)

if args.reload:# Reload Core
	resp = session.get(RELOAD + args.reload)
	if resp and resp.status_code == 200:
		print('ok')
	else:
		print(resp.text)

if args.status:# Core Status
	resp = session.get(STATUS + args.status)
	if resp and resp.status_code == 200:
		curr = json.loads(resp.text)['status'][args.status]['index']['current']
		if curr:
			print('ok')
		else:
			print('pending')
	else:
		print(resp.text)

session.close()
sys.exit(0)

