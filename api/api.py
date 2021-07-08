#!/usr/bin/python3
#
# DEVEL REST API
#

import sqlite3,json

from pymarc import marcxml

from flask import Flask,make_response,g
from flask_restful import Resource, Api

# VAR -------------------------

DB='vufind.db'# record [ ident TEXT | timestamp INT |  metadata TEXT ]

HELP='''<!DOCTYPE html>
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
<tr><td><b>Header</b></td></tr>
<tr><td></td></tr>
<tr><td></td><td>Accept: application/json</td></tr>
<tr><td></td><td>Response: <i>MARC-JSON (default)</i></td></tr>
<tr><td><b>Header</b></td></tr>
<tr><td></td></tr>
<tr><td></td><td>Accept: application/xml</td></tr>
<tr><td></td><td>Response: <i>MARC-XML</i></td></tr>
<tr><td><b>Header</b></td></tr>
<tr><td></td></tr>
<tr><td></td><td>Accept: application/octet-stream</td></tr>
<tr><td></td><td>Response: <i>MARC-21</i></td></tr>
</table>
</div>
</body>
</html>'''

XML_HEAD='''<?xml version="1.0" encoding="UTF-8"?>
<collection
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xsi:schemaLocation="http://www.loc.gov/MARC21/slim http://www.loc.gov/standards/marcxml/schema/MARC21slim.xsd"
  xmlns="http://www.loc.gov/MARC21/slim">'''

XML_FOOT='</collection>'

# INIT -------------------------

app = Flask(__name__)
api = Api(app, default_mediatype='application/json')

# DATABASE -------------------------

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

# CONTENT -------------------------

@api.representation('application/json')
def output_json(data, code, headers=None):
	resp = make_response(json.dumps(marcxml.as_json(data)), code)
	resp.headers.extend(headers or {})
	return resp

@api.representation('application/xml')
def output_xml(data, code, headers=None):
	resp = make_response(XML_HEAD + data + XML_FOOT, code)
	resp.headers.extend(headers or {})
	return resp

@api.representation('application/octet-stream')
def output_raw(data, code, headers=None):
	resp = make_response(''.join(data), code)
	resp.headers.extend(headers or {})
	return resp

api.representations['application/json'] = output_json
api.representations['application/xml'] = output_xml
api.representations['application/ocetet-stream'] = output_raw

# ROUTING -------------------------

class API(Resource):
	def get(self):
		headers = {'Content-Type': 'text/html'}
		return make_response(HELP, 200, headers)

class GetRecord(Resource):
	def get(self, ident):
		data = query_db("SELECT metadata FROM record WHERE ident = ?", (ident,), one=True)
		if not data: return {'null':'nada'}
		return data['metadata']

class ListRecords(Resource):
	def get(self, iso8601_from, iso8601_until):
		data = query_db("SELECT metadata FROM record WHERE timestamp BETWEEN ? AND ? ORDER BY timestamp;", (iso8601_from, iso8601_until))
		return (row['metadata'] for row in data)

api.add_resource(API, '/api/')
api.add_resource(GetRecord, '/api/GetRecord/<ident>')
api.add_resource(ListRecords, '/api/ListRecords/<iso8601_from>/<iso8601_until>')

# MAIN -------------------------

if __name__ == '__main__':
    app.run(debug=True)

