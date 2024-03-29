#!/usr/bin/python3
#
# KOHA CLO REST API
#

import sqlite3,json,re

from datetime import datetime
from flask import Flask,make_response,g,render_template,request,abort
from flask_restful import Resource,Api

# VAR -------------------------

DB='/usr/local/bin/koha-clo.db'# record[ident|timestamp|json|xml|marc]

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
	resp = make_response(data, code)
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
	def get(self):
		prefix = 'json'
		ident = request.args.get('identifier')
		if not ident: return abort(400, "Missing 'identifier' argument.")
		if request.headers.get('Accept') == 'application/marcxml': prefix='xml'
		if request.headers.get('Accept') == 'application/octet-stream': prefix='marc'
		if not not re.match('^\d{9}$', ident):
			return abort(400, "Invalid identifier: <000000000-999999999>.")
		data = query_db("SELECT {} FROM record WHERE ident = ?".format(prefix), (ident,), one=True)
		if data:
			if prefix == 'xml':
				return XML_HEAD + data[prefix] + XML_FOOT
			return data[prefix]
		else:
			return '', 204
			
class ListRecords(Resource):
	def get(self):
		prefix = 'json'
		if request.headers.get('Accept') == 'application/marcxml': prefix='xml'
		if request.headers.get('Accept') == 'application/octet-stream': prefix='marc'
		iso8601_from = request.args.get('from')
		iso8601_until = request.args.get('until')
		if not iso8601_from: return abort(400, "Missing 'from' argument.")
		if not iso8601_until: return abort(400, "Missing 'until' argument.")
		try: _from = int(datetime.fromisoformat(iso8601_from).timestamp())
		except:
			return abort(400, "Invalid 'from' argument: <iso8601_datetime>.")
		try: _until = int(datetime.fromisoformat(iso8601_until).timestamp())
		except:
			return abort(400, "Invalid 'until' argument: <iso8601_datetime>.")
		data = query_db("SELECT {} FROM record WHERE timestamp BETWEEN ? AND ? ORDER BY timestamp;".format(prefix), (_from,_until))
		if data:
			if prefix in ['xml','marc']:
				return b''.join([row[prefix] for row in data])
			return '[' + ','.join([row[prefix] for row in data]) + ']'
		else:
			return '', 204

class ListIdentifiers(Resource):
	def get(self):
		iso8601_from = request.args.get('from')
		iso8601_until = request.args.get('until')
		if not iso8601_from: return abort(400, "Missing 'from' argument.")
		if not iso8601_until: return abort(400, "Missing 'until' argument.")
		try: _from = int(datetime.fromisoformat(iso8601_from).timestamp())
		except:
			return abort(400, "Invalid 'from' argument: <iso8601_datetime>.")
		try: _until = int(datetime.fromisoformat(iso8601_until).timestamp())
		except:
			return abort(400, "Invalid 'until' argument: <iso8601_datetime>.")
		data = query_db("SELECT ident FROM record WHERE timestamp BETWEEN ? AND ? ORDER BY timestamp;", (_from,_until))
		if data:
			return json.dumps([row['ident'] for row in data])
		else:
			return '', 204

api.add_resource(API, '/')
api.add_resource(GetRecord, '/GetRecord')
api.add_resource(ListRecords, '/ListRecords')
api.add_resource(ListIdentifiers, '/ListIdentifiers')

# MAIN -------------------------

if __name__ == '__main__':
    app.run(debug=True)

