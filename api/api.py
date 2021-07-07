#!/usr/bin/python3
#
# MARCXML REST API
#

import sqlite3

from flask import Flask
from flask_restful import Resource, Api

# SQLITE3

DB='vufind.db'

con = sqlite3.connect(DB)
cur = con.cursor()

#API

app = Flask(__name__)
api = Api(app)

class Record(Resource):
    def get(self):
	cur.execute("SELECT metadata FROM record WHERE ident = ?", (ident,))
        return cur.fetchone()

class ListRecords(Resource):
    def get(self)
	date_min,date_max = '-'.split(interval)
	cur.execute("SELECT metadata FROM record WHERE timestamp BETWEEN ? AND ? ORDER BY timestamp;", (date_min, date_max))
	return cur.fetchall()

api.add_resource(Record, '/api/record/<ident>')
api.add_resource(ListRecords, '/api/records/<interval>')

if __name__ == '__main__':
    app.run(debug=True)

