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

from flask import Flask,g
from flask_restful import Resource, Api

DB='vufind.db'# ident | timestamp |  metadata

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

@api.representations('application/xml')
def output_xml(data, code, headers=None):
    res = make_response('<?xml version="1.0" encoding="UTF-8" standalone="no" ?><data>' + data + '</data>', code)
    res.headers.extend(headers or {})
    return res

class GetRecord(Resource):
	def get(self, ident):
		data = query_db("SELECT metadata FROM record WHERE ident = ?", (ident,), one=True)
		return json.loads(data['metadata'])

class ListRecords(Resource):
	def get(self, iso8601_from, iso8601_until):
		data = query_db("SELECT metadata FROM record WHERE timestamp BETWEEN ? AND ? ORDER BY timestamp;", (iso8601_from, iso8601_until))
		return json.loads([row['metadata'] for row in data][1])

api.add_resource(GetRecord, '/api/GetRecord/<ident>')
api.add_resource(ListRecords, '/api/ListRecords/<iso8601_from>/<iso8601_until>')

if __name__ == '__main__':
    app.run(debug=True)

