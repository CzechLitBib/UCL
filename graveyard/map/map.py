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
	return render_template('index.html')

# MAIN

if __name__ == '__main__':
	app.config['TEMPLATES_AUTO_RELOAD'] = True
	app.run(debug=False)
	app.run()
