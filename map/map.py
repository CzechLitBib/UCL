#!/bin/python3
#
# VuFind Map module app
#
import json

from flask import Flask,render_template,make_response

# INIT

app = Flask(__name__)

@app.route('/map')# ROOT
def index():
	return render_template('index.html')

@app.route('/map/q')# QUERY
def query():
	# attrib parsing..
	# JOSN | SQLITE | SOLR query
	# return JSON location + markers coords..
	data = []
	return render_template('index.html')

@app.route('/map/data')# DATA
def data():
	data = {'country': ('lat','lon')}
	headers = {'Content-Type': 'application/json'}
	# return JSON location + markers coords..
	return make_response(json.dumps(data), 200, headers)

# MAIN

if __name__ == '__main__':
	app.config['TEMPLATES_AUTO_RELOAD'] = True
	#app.run(debug=False)
	app.run()
