#!/bin/python3

from flask import Flask

app = Flask(__name__)

@app.route('/exil/')
def hello_world():
    return "<p>Hello, World!</p>"

if __name__ == '__main__':
	#app.config['TEMPLATES_AUTO_RELOAD'] = True
	#app.run(debug=False)
	app.run()
