#!/bin/python3

import json

from flask import Flask,render_template

#from wtforms import Form, StringField, SubmitField
#from wtforms.validators import DataRequired

# INIT

app = Flask(__name__)

# DEF / ROUTE

@app.route('/map')
def index():
	return render_template('index.html')

@app.route('/map/json')
def db():
	data = []
	return json.dumps(data)

# MAIN
if __name__ == '__main__':
	app.config['TEMPLATES_AUTO_RELOAD'] = True
	#app.run(debug=False)
	app.run()
