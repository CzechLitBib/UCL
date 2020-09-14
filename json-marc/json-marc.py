#!/usr/bin/python
# -*- coding: utf-8 -*-
#
# JSON to MARC convertor.
# 

# INCLUDE -----------------

from __future__ import print_function

import argparse,json,sys,os,re

from pymarc import Record
from pymarc.field import Field

# fast JSON streaming library
#from yajl import YajlParser,YajlContentHandler

# VAR -----------------

#IN='retrobi.json'
IN='demo.json'
#BUFFER={}

# INIT -----------------

record = Record()

record.leader = '     nab a22     4a 4500'
#record.add_ordered_field(Field(tag='FMT', data='RS'))
record.add_ordered_field(Field(tag='003', data='CZ PrUCL'))
record.add_ordered_field(Field(tag='005', data='20201231'))
record.add_ordered_field(Field(tag='040', indicators=[' ',' '], subfields=['a', 'ABB060','b', 'cze']))
record.add_ordered_field(Field(tag='041', indicators=['0',' '], subfields=['a', 'cze']))
record.add_ordered_field(Field(tag='336', indicators=[' ',' '], subfields=['a', 'text', 'b', 'txt', '2', 'rdacontent']))
record.add_ordered_field(Field(tag='337', indicators=[' ',' '], subfields=['a', u'bez média', 'b', 'n', '2', 'rdamedia']))
record.add_ordered_field(Field(tag='338', indicators=[' ',' '], subfields=['a', u'jiný', 'b', 'nz', '2', 'rdacarrier']))
record.add_ordered_field(Field(tag='500', indicators=[' ',' '], subfields=['a', u'Strojově převedený záznam z RETROBI bez redakční kontroly.']))
record.add_ordered_field(Field(tag='910', indicators=[' ',' '], subfields=['a', 'ABB060']))
record.add_ordered_field(Field(tag='964', indicators=[' ',' '], subfields=['a', 'RETROBI']))
record.add_ordered_field(Field(tag='OWN', indicators=[' ',' '], subfields=['a', 'UCLA']))
#record.add_ordered_field(Field(tag='SIF', data='RET'))

# DEF -----------------

#class ContentHandler(YajlContentHandler):
#	def __init__(self): pass
#	def yajl_null(self, ctx): pass
#	def yajl_start_map(self, ctx): pass
#	def yajl_end_map(self, ctx): pass
#	def yajl_start_array(self, ctx): pass
#	def yajl_end_array(self, ctx): pass
#
#	def yajl_boolean(self, ctx, boolVal):
#		field_complete(['bool', boolVal])
#	def yajl_integer(self, ctx, integerVal):
#		field_complete(['integer', integerVal])
#	def yajl_double(self, ctx, doubleVal):
#		field_complete(['double', doubleVal])
#	def yajl_number(self, ctx, stringNum):
#		field_complete(['number', stringNum])
#	def yajl_string(self, ctx, stringVal):
#		field_complete(['string', stringVal])
#	def yajl_map_key(self, ctx, stringVal):
#		field_complete(['key', stringVal])
#
#def field_complete(tpl):
#	print(tpl[0] + ': ' + str(tpl[1]))

# MAIN -----------------

#parser = YajlParser(ContentHandler())
#parser.dont_validate_strings=True
#parser.allow_multiple_values=True

with open(IN, 'rb') as f:
	for LINE in f:
		j = json.loads(re.sub('(.*),$','\\1',LINE.strip()), strict=False)
		print(json.dumps(j, indent=2))
		# ID
		#record.add_ordered_field(Field(tag='001', data='RET-' +  j['id']))
		# 

#parser.parse(f=j)
#j.close()

#print(record.as_json())

# EXPORT -----------------

#try:
#	os.mkdir('export')
#	os.mkdir('export/' + args.format)
#except: pass

#for record in reader:
#	if args.format == 'json':# JSON
#		writer = JSONWriter(open('export/json/' + re.sub('(.*)\.json', '\\1', f) + '.json', 'wt'))
#		writer.write(record)
#		writer.close()
#	if args.format == 'marc':# MARC21
#		writer = MARCWriter(open('export/marc/' + re.sub('(.*)\.json', '\\1', f)  + '.dat', 'wb'))
#		writer.write(record)
#		writer.close()
#	if args.format == 'xml':# MARCXML
#		writer = XMLWriter(open('export/xml/' + re.sub('(.*)\.json', '\\1', f) + '.xml', 'wb'))
#		writer.write(record)
#		writer.close()

# EXIT -------------------

#log.close()
#print('Done.')

