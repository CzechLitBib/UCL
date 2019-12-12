#!/usr/bin/python
#
# Harvested OAI-PMH 2.0 MARCXML Record validator.
#
# https://aleph.mzk.cz/OAI?verb=GetRecord&identifier=oai:aleph.mzk.cz:MZK01-000152782&metadataPrefix=marc21
#

# INCLUDE -------------------

from __future__ import print_function

import httplib,argparse,StringIO,sys,os,re

from datetime import datetime
from oaipmh.client import Client
from oaipmh.metadata import MetadataRegistry
from pymarc import marcxml,MARCWriter,JSONWriter,XMLWriter
from lxml.etree import tostring

# VAR -------------------

URL='https://aleph.mzk.cz/OAI'
LOG='oai-marc.log'

# DEF -------------------

def MarcXML(xml):
	handler = marcxml.XmlHandler()
	marcxml.parse_xml(StringIO.StringIO(tostring(xml, encoding='utf-8')), handler)
	return handler.records[0]

def valid_date(s):
	try:
		return datetime.strptime(s, '%Y-%m-%d %H:%M:%S')
	except:
		msg = 'Invalid date format.'
		raise argparse.ArgumentTypeError(msg)

def valid_format(s):
	if s in ('json', 'marc', 'xml'): return s
	else:
		msg = 'Invalid export format.'
		raise argparse.ArgumentTypeError(msg)

def valid_display(s):
	if s in ('ident', 'json', 'marc'): return s
	else:
		msg = 'Invalid display format.'
		raise argparse.ArgumentTypeError(msg)

def valid_request(s):
	if s in ('record', 'set', 'meta'): return s
	else:
		msg = 'Invalid request format.'
		raise argparse.ArgumentTypeError(msg)

def url_response(url):
	try:
		c = httplib.HTTPConnection(url)
		c. request('GET','/')
		r = c.getresponse()
		if r.status == 200: return 1
		c = httplib.HTTPSConnection(url)
		c. request('GET','/')
		r = c.getresponse()
		if r.status == 200: return 1
	except: pass
	return 0

# ARG -------------------

parser = argparse.ArgumentParser(description="OAI PMH 2.0 MARCXML Validator.")
listing = parser.add_argument_group('info')
listing.add_argument('--get', help='Request type. [record] [set] [meta]', type=valid_request, default='record')
required = parser.add_argument_group('validation')
required.add_argument('--set', help='Records set.')
required.add_argument('--from', help='Records from. [YYYY-mm-dd HH:MM:SS]', type=valid_date, dest='from_date')
required.add_argument('--until', help='Records until. [YYYY-mm-dd HH:MM:SS]', type=valid_date, dest='until_date')
optional = parser.add_argument_group('output')
optional.add_argument('--check', help='Validation control.', action='store_true')
optional.add_argument('--export', help='Export data format. [json] [marc] [xml]', type=valid_format)
optional.add_argument('--display', help='Display output format. [ident] [json] [marc]', nargs='?', type=valid_display, const='ident')
args = parser.parse_args()

if args.get == 'record':
	if not args.set:
		parser.error('argument --set is required.')
	if not args.from_date:
		parser.error('argument --from is required.')
	if not args.until_date:
		parser.error('argument --until is required.')

# INIT -------------------

try:
	log = open(LOG, 'w', 0)
except:
	print('Read only FS exiting..')
	exit(1)

registry = MetadataRegistry()
registry.registerReader('marc21', MarcXML)

oai = Client(URL, registry)

try:
	if args.get == 'record':
		records = oai.listRecords(metadataPrefix='marc21', set=args.set, from_=args.from_date, until=args.until_date)
	if args.get == 'set':
		records = oai.listSets()
	if args.get == 'meta':
		records = oai.listMetadataFormats()
except:
	print('No records.')
	sys.exit(1)

if args.export:
	try:
		os.mkdir('export')
	except :pass
	try:
		os.mkdir('export/' + args.export)
	except: pass

if args.check: print('Validating..')
if args.display or args.get != 'record': print('------------------')

# MAIN -------------------

counter = 0

for record in records:

	if args.get == 'set' or args.get == 'meta':
		print(record[0])
		counter+=1
		continue

	header = record[0]
	metadata = record[1]
	
	# skip deleted records
	if header.isDeleted(): continue

	# retry missing metadata(?)
	if not metadata:
		print(header.identifier() + ' Missing matadata. Retrying..')
		retry = oai.getRecord(metadataPrefix='marc21', identifier=header.identifier())
		if not retry[1]:
			print(header.identifier() + ' Missing retry metadata.')
			continue
		else:
			header = retry[0]
			metadata = retry[1]

	# DISPLAY ------------------

	if args.display:
		if args.display == 'ident':
			print(header.identifier())
		if args.display == 'json':
			print(metadata.as_json(indent=4, sort_keys=True))
		if args.display == 'marc':
			print(metadata)

	# VALIDATION ------------------

	if args.check:
		#TEST TAG
		for TAG in ('001', '003', '005', '008', '040', '080', '245', '520', '655', '910', '964', 'SIF', 'OWN'):
			if not TAG in metadata:
				log.write(header.identifier() + ' Missing ' + TAG + ' tag.\n')
		if not 'KAT' or 'CAT' in metadata:
			log.write(header.identifier() + ' Missing KAT/CAT tag.\n')
		if not metadata.leader:
			log.write(header.identifier() + ' Missing LDR tag.\n')
	
		#TEST TAG/SUBFIELD VALUE
		if '003' in metadata:
			if metadata['003'].value() != 'Cz-PrUCL':
				log.write(header.identifier() + ' Invalid value 003 tag.\n')
		if '040' in metadata:
			if 'a' in metadata['040']:
				if metadata['040']['a'] != 'ABB060':
					log.write(header.identifier() + ' Invalid value 040a subfield.\n')
			if 'b' in metadata['040']:
				if metadata['040']['b'] != 'cze':
					log.write(header.identifier() + ' Invalid value 040b subfield.\n')
			if 'e' in metadata['040']:
				if metadata['040']['e'] != 'rda':
					log.write(header.identifier() + ' Invalid value 040e subfield.\n')
		if '072' in metadata:
			if '2' in metadata['072']:
				if metadata['072']['2'] != 'Konspekt':
					log.write(header.identifier() + ' Invalid value 072-2 subfield.\n')
		if '082' in metadata:
			if '2' in metadata['082']:
				if not metadata['082']['2'] in ('MRF', 'MRF-sel'):
					log.write(header.identifier() + ' Invalid value 082-2 subfield.\n')
		if '910' in metadata:
			if 'a' in metadata['910']:
				if metadata['910']['2'] != 'ABB060':
					log.write(header.identifier() + ' Invalid value 910a subfield.\n')
		if 'OWN' in metadata:
			if metadata['OWN'].value() != 'UCLA':
				log.write(header.identifier() + ' Invalid value OWN tag.\n')
		if '856' in metadata:
			if '4' in metadata['856']:
				if metadata['856']['4'] != '4':
					log.write(header.identifier() + ' Invalid value 856-4 subfield.\n')
			if 'y' in metadata['856']:
				if not metadata['856']['y'] in ('online', 'Webarchiv'):
					log.write(header.identifier() + ' Invalid value 856y subfield.\n')

		#TEST SUBFIELD
		for TAG in ('072', '080', '100', '245', '520', '600', '610', '611', '630', '648', '650', '651', '653', '655', '700', '710'):
			if TAG in metadata:
				if not len(metadata[TAG].get_subfields('a')) != 1:
					log.write(header.identifier() + ' Invalid ' + TAG + 'a subfield.\n')
		if '022' in metadata:
			if len(metadata['022'].get_subfields('a')) > 1:
				log.write(header.identifier() + ' Invalid 022a subfield.\n')
		if '072' in metadata:
			if len(metadata['072'].get_subfields('x')) != 1:
				log.write(header.identifier() + ' Invalid 072x subfield.\n')
			if len(metadata['072'].get_subfields('2')) != 1:
				log.write(header.identifier() + ' Invalid 072-2 subfield.\n')
			if len(metadata['072'].get_subfields('9')) != 1:
				log.write(header.identifier() + ' Invalid 072-9 subfield.\n')
		if '080' in metadata:
			if len(metadata['080'].get_subfields('2')) != 1:
				log.write(header.identifier() + ' Invalid 080-2 subfield.\n')
		if '700' in metadata:
			if len(metadata['700'].get_subfields('4')) > 1:
				log.write(header.identifier() + ' Invalid 700-4 subfield.\n')
		if '710' in metadata:
			if len(metadata['710'].get_subfields('4')) > 1:
				log.write(header.identifier() + ' Invalid 710-4 subfield.\n')
		if '773' in metadata:
			if len(metadata['773'].get_subfields('t')) != 1:
				log.write(header.identifier() + ' Invalid 773t subfield.\n')
			if len(metadata['773'].get_subfields('9')) != 1:
				log.write(header.identifier() + ' Invalid 773-9 subfield.\n')
		if '787' in metadata:
			if len(metadata['787'].get_subfields('t')) != 1:
				log.write(header.identifier() + ' Invalid 787t subfield.\n')
			if len(metadata['787'].get_subfields('4')) != 1:
				log.write(header.identifier() + ' Invalid 787-4 subfield.\n')
		if '856' in metadata:
			if len(metadata['856'].get_subfields('u')) != 1:
				log.write(header.identifier() + ' Invalid 856u subfield.\n')
			if len(metadata['856'].get_subfields('y')) != 1:
				log.write(header.identifier() + ' Invalid 856y subfield.\n')

		#TEST VALID LINK
		if '856' in metadata:
			if 'u' in metadata['856']:
				if not url_response(metadata['856']['u']):
					log.write(header.identifier() + ' Invalid 856u link.\n')

		#TEST: TAG + TAG SUBFIELD EXISTS
		#if '072' in metadata:
		#	if 'x' in metadata['072']:
		#		if not '245' in metadata:
		#			log.write(header.identifier() + ' Missing 245 tag when x subfield in 072 tag.\n')
		#TEST: EQUAL VALUE
		#if '100' and '260' in metadata:
		#	if metadata['100'].value() != metadata['260'].value():
		#		log.write(header.identifier() + ' Value of 100 and 260 not equal.\n')
		#TEST: VALUE IN LIST
		#LIST = ('auto','kolo','vlak')
		#if '260' in metadata:
		#	if not metadata['260'].value() in LIST:
		#		log.write(header.identifier() + ' Value of 260 not in list.\n')
		#TEST: PRINT VALUE FROM LIST
		#for TAG in ('001','005','007'):
		#	if TAG in metadata:
		#		log.write(header.identifier() + ' Tag ' + TAG + ' value: ' + metadata[TAG].value() + '\n')
		#TEST: VALUE DATE FORMAT
		#if '001' in metadata:
		#	if not re.match('\d+', metadata['001'].value()):
		#		log.write(header.identifier() + ' Tag 001 invalid data format.\n')

	# EXPORT -------------------

	if args.export:
		if args.export == 'marc':# MARC 21
			writer = MARCWriter(open('export/marc/' + header.identifier() + '.dat', 'wb'))
			writer.write(metadata)
			writer.close()
		if args.export == 'json':# JSON
			writer = JSONWriter(open('export/json/'+ header.identifier() + '.json', 'wt'))
			writer.write(metadata)
			writer.close()
		if args.export == 'xml':# MARCXML
			writer = XMLWriter(open('export/xml/' + header.identifier() + '.xml', 'wb'))
			writer.write(metadata)
			writer.close()

	counter+=1

# EXIT -------------------
log.write('TOTAL: ' + str(counter) + '\n')
log.close()
if args.display or args.get != 'record': print('------------------')
print('Done.')
print('Total: ' + str(counter))

