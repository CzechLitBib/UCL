#!/usr/bin/python3
#
# RAW/XML/JSON REST API
#
# https://vyvoj.ucl.cas.cz:4433/api/GetRecord/1
# https://vyvoj.ucl.cas.cz:4433/api/ListRecords/1611700000-1611750000
#
# headers={"accept":"application/json"}
#

import sqlite3,json

from flask import Flask,make_response,g# global
from flask_restful import Resource, Api

DB='vufind.db'# ident | timestamp |  metadata

HELP='''
<!DOCTYPE html>
<html><head><title>DEVEL REST API</title><meta charset="utf-8"></head>
<body bgcolor="lightgrey">
<div align="center">
<br><b>DEVEL REST API</b><br><br>
<table cellspacing="4">

<tr><td><b>GET</b></td></tr>
<tr><td></td><td>/api/GetRecord/&lt;ident&gt;</td></tr>
<tr><td></td></tr>
<tr><td></td><td><i>https://vyvoj.ucl.cas.cz/api/GetRecord/1</i></td></tr>
<tr><td><b>GET</b></td></tr>
<tr><td></td><td>/api/ListIdentifiers/&lt;iso8601_datetime_from&gt;/&lt;iso8601_datetime_until&gt;</td></tr>
<tr><td></td></tr>
<tr><td></td><td><i>https://vyvoj.ucl.cas.cz/api/ListIdentifiers/2021-07-11 09:00:00/2021-07-11 10:00:00</i></td></tr>
<tr><td><b>GET</b></td></tr>
<tr><td></td><td>/api/ListRecords/&lt;iso8601_datetime_from&gt;/&lt;iso8601_datetime_until&gt;</td></tr>
<tr><td></td></tr>
<tr><td></td><td><i>https://vyvoj.ucl.cas.cz/api/ListRecords/2021-07-11 09:00:00/2021-07-11 10:00:00</i></td></tr>

<tr><td height="20px"></td></tr>

<tr><td><b>Header:</b></td><td>(<i>default</i>)</td></tr>
<tr><td></td><td>Accept: application/json</td></tr>
<tr><td></td><td>Response: <i>MARC-JSON</i></td></tr>
<tr><td><b>Header</b></td></tr>
<tr><td></td><td>Accept: application/xml</td></tr>
<tr><td></td><td>Response: <i>MARC-XML</i></td></tr>
<tr><td><b>Header</b></td></tr>
<tr><td></td><td>Accept: application/octet-stream</td></tr>
<tr><td></td><td>Response: <i>MARC-21</i></td></tr>
</table>

</div>
</body>
</html>
'''

#API

app = Flask(__name__)
api = Api(app)

def get_db():
	db = getattr(g, '_database', None)
	if db is None:
		db = g._database = sqlite3.connect(DB)
	db.row_factory = sqlite3.Row
	return db

@app.teardown_appcontext
def close_connection(exception):
	db = getattr(g, '_database', None)
	if db is not None:
		db.close()

def query_db(query, args=(), one=False):
	cur = get_db().execute(query, args)
	rv = cur.fetchall()
	cur.close()
	return (rv[0] if rv else None) if one else rv

@api.representation('application/xml')
def output_xml(data, code, headers=None):
    res = make_response('<?xml version="1.0" encoding="UTF-8" standalone="no" ?><data>' + data + '</data>', code)
    res.headers.extend(headers or {})
    return res

class API(Resource):
	def get(self):
		headers = {'Content-Type': 'text/html'}
		return make_response(HELP, 200, headers)

class GetRecord(Resource):
	def get(self, ident):
		data = query_db("SELECT metadata FROM record WHERE ident = ?", (ident,), one=True)
		return json.loads(data['metadata'])

class ListRecords(Resource):
	def get(self, iso8601_from, iso8601_until):
		data = query_db("SELECT metadata FROM record WHERE timestamp BETWEEN ? AND ? ORDER BY timestamp;", (iso8601_from, iso8601_until))
		return json.loads([row['metadata'] for row in data][1])

api.add_resource(API, '/api/')
api.add_resource(GetRecord, '/api/GetRecord/<ident>')
api.add_resource(ListRecords, '/api/ListRecords/<iso8601_from>/<iso8601_until>')

if __name__ == '__main__':
    app.run(debug=True)

