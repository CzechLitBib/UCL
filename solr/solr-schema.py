#!/usr/bin/python3
#
# Solr API v2 schema tool
#

import requests,json

URL='http://localhost:8983/solr/core/schema'

session = requests.Session()
session.headers.update({'Content-type':'application/json'})

# Add Field

field = {'add-field':{'name':'', 'type':''}}# indexed + stored + uninvertible: Default = True

field['add-field']['name'] = 'tag_245'
field['add-field']['type'] = 'strings'

resp = session.post(URL, data=json.dumps(field))

if resp and resp.status_code == 200:
	print("ok")
else:
	print(resp.status_code)
	print(resp.text)

# List Fields

resp = session.get(URL + '/fields')

if resp and resp.status_code == 200:
	data = json.loads(resp.text)
	for F in data['fields']:
		print(F['name'])
else:
	print('REquest failed.')

