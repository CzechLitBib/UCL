#!/usr/bin/python3
#
# MARCXML REST API
#
#
#  PKEY ID INT - record ('ident' [INT],'date' [INT],'metadata' [TEXT])
#  INDEX ident ( indent);
#  INDEX epoch ( date);
#
# row.fetch_one('ident');
# yield list(iso_range); -> SELECT BETWEEN;
#

from pymarc import MARCWriter

from flask import Flask
from flask_restful import Resource, Api

app = Flask(__name__)
api = Api(app)

class HelloWorld(Resource):
    def get(self):
        return {'hello': 'world'}

#class Record(Resource):
#    def get(sef):
#        return Record(ident);

#class ListRecords(Resource):
#    def get(self)
#        return Record();

api.add_resource(HelloWorld, '/api')

if __name__ == '__main__':
    app.run(debug=True)

