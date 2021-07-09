#!/usr/bin/python3
#
# UPDATE SQLITE3 DB FROM OAI-PMH 2.0
#

import argparse,io

from datetime import datetime

from pymarc import marcxml,MARCWriter,XMLWriter

from datetime import datetime
from lxml.etree import tostring, parse
from oaipmh.client import Client
from oaipmh.metadata import MetadataRegistry

URL='https://aleph.lib.cas.cz/OAI'
SET='UCLA'

# DEF -------------------

def valid_date(s):
	try:
		return datetime.strptime(s, '%Y-%m-%d %H:%M:%S')
	except:
		raise argparse.ArgumentTypeError('Invalid date format.')

def XMLParse(xml):
	handler = marcxml.XmlHandler()
	marcxml.parse_xml(io.StringIO(tostring(xml, encoding='utf-8').decode('utf-8')), handler)
	return handler.records
	#return xml.findall('.//record')
	#return parse(io.StringIO(tostring(xml, encoding='utf-8').decode('utf-8')))
#	data = parse(tostring(xml, encoding='utf-8'))
#	data = parse(xml)
#	print(tostring(xml, encoding='utf-8'))
#	data =type() xml.find('record')
#	print(tostring(data))
#	#data = parse(xml)
#	buff = parse(xml).xpath('//record')[0]
#	return tostring(data)

#def MarcXML(xml):
	#handler = marcxml.XmlHandler()
	#marcxml.parse_xml(io.StringIO(tostring(xml, encoding='utf-8'))), handler)
	#return handler.records[0]

# ARGS -------------------

parser = argparse.ArgumentParser(description="OAI PMH 2.0 MARCXML Validator.")
required = parser.add_argument_group('validation')
required.add_argument('--set', help='Records set.')
required.add_argument('--from', help='Records from. [YYYY-mm-dd HH:MM:SS]', type=valid_date, dest='from_date')
required.add_argument('--until', help='Records until. [YYYY-mm-dd HH:MM:SS]', type=valid_date, dest='until_date')
args = parser.parse_args()

if not args.from_date: parser.error('argument -from is required.')
if not args.until_date: parser.error('argument --until is required.')

# OAI -------------------

registry = MetadataRegistry()
registry.registerReader('marc21', XMLParse)

oai = Client(URL, registry)

records = oai.listRecords(metadataPrefix='marc21', set=SET, from_=args.from_date, until=args.until_date)

# MAIN -------------------

for record in records:
	print(record[1])
