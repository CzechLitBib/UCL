#!/usr/bin/python3
#
# UPDATE SQLITE3 DB FROM OAI-PMH 2.0
#

import argparse,io

from pymarc import marcxml
from datetime import datetime
from lxml.etree import tostring
from oaipmh.client import Client
from oaipmh.metadata import MetadataRegistry

URL='https://aleph.lib.cas.cz/OAI'
SET='UCLA'

# DEF -------------------

def valid_date(date):
	try:
		return datetime.strptime(date, '%Y-%m-%d %H:%M:%S')
	except:
		raise argparse.ArgumentTypeError('Invalid date format.')

def MarcXML(xml):
	handler = marcxml.XmlHandler()
	marcxml.parse_xml(io.StringIO(tostring(xml).decode('utf-8')), handler)
	return handler.records[0]

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
registry.registerReader('marc21', MarcXML)

oai = Client(URL, registry)

records = oai.listRecords(metadataPrefix='marc21', set=SET, from_=args.from_date, until=args.until_date)

# MAIN -------------------

for record in records:
	metadata = record[1]
	metadata.remove_fields('LDR')# LDR DUP
	metadata.remove_fields('SYS')# INVALID
	print(marcxml.record_to_xml(metadata))
	print(metadata.as_json())
	print(metadata.as_marc())

