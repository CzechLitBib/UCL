#!/usr/bin/python3
#
# DEVEL REST API
#

import sqlite3

from flask import Flask,make_response,g,render_template,request
from flask_restful import Resource, Api

# VAR -------------------------

DB='vufind.db'# record [ ident TEXT | timestamp INT |  metadata TEXT ]

XML_HEAD=b'''<?xml version="1.0" encoding="UTF-8"?>
<collection xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xsi:schemaLocation="http://www.loc.gov/MARC21/slim http://www.loc.gov/standards/marcxml/schema/MARC21slim.xsd"
 xmlns="http://www.loc.gov/MARC21/slim">'''

XML_FOOT=b'</collection>'

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
	resp = make_response(data, code)
	resp.headers.extend(headers or {})
	return resp

@api.representation('application/marcxml')
def output_xml(data, code, headers=None):
	#resp = make_response(XML_HEAD + ''.join(data['xml']) + XML_FOOT, code)
	resp = make_response(XML_HEAD + data + XML_FOOT, code)
	resp.headers.extend(headers or {})
	return resp

@api.representation('application/octet-stream')
def output_raw(data, code, headers=None):
	resp = make_response(data, code)
	resp.headers.extend(headers or {})
	return resp

api.representations['application/json'] = output_json
api.representations['application/marcxml'] = output_xml
api.representations['application/ocetet-stream'] = output_raw

# ROUTING -------------------------

class API(Resource):
	def get(self):
		headers = {'Content-Type': 'text/html'}
		return make_response(render_template('/help.html'), 200, headers)

class GetRecord(Resource):
	def get(self, ident):
		if request.headers['Accept'] == 'application/marcxml':
			data = query_db("SELECT xml FROM record WHERE ident = ?", (ident,), one=True)
			return data['xml']
		elif request.headers['Accept'] == 'application/octet-stream':
			data = query_db("SELECT marc FROM record WHERE ident = ?", (ident,), one=True)
			return data['marc']
		else:
			data = query_db("SELECT json FROM record WHERE ident = ?", (ident,), one=True)
			return data['json']

class ListRecords(Resource):
	def get(self, iso8601_from, iso8601_until):
		if request.headers['Accept'] == 'application/marcxml':
			data = query_db("SELECT xml FROM record WHERE timestamp BETWEEN ? AND ? ORDER BY timestamp;", (iso8601_from, iso8601_until))
			return (row['xml'] for row in data)
		elif request.headers['Accept'] == 'application/octet-stream':
			data = query_db("SELECT marc FROM record WHERE timestamp BETWEEN ? AND ? ORDER BY timestamp;", (iso8601_from, iso8601_until))
			return (row['marc'] for row in data)
		else:
			data = query_db("SELECT json FROM record WHERE timestamp BETWEEN ? AND ? ORDER BY timestamp;", (iso8601_from, iso8601_until))
			return (row['json'] for row in data)

class ListIdentifiers(Resource):
	def get(self, iso8601_from, iso8601_until):
		data = query_db("SELECT ident FROM record WHERE timestamp BETWEEN ? AND ? ORDER BY timestamp;", (iso8601_from, iso8601_until))
		if request.headers['Accept'] == 'application/marcxml':
			return (row['ident'] for row in data)
		else:
			headers = {'Content-Type': 'application/json'}
			data = query_db("SELECT ident FROM record WHERE timestamp BETWEEN ? AND ? ORDER BY timestamp;", (iso8601_from, iso8601_until))
			return (row['ident'] for row in data)

api.add_resource(API, '/api/')
api.add_resource(GetRecord, '/api/GetRecord/<ident>')
api.add_resource(ListRecords, '/api/ListRecords/<iso8601_from>/<iso8601_until>')
api.add_resource(ListIdentifiers, '/api/ListIdentifiers/<iso8601_from>/<iso8601_until>')

# MAIN -------------------------

if __name__ == '__main__':
    app.run(debug=True)

