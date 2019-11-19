#!/usr/bin/python
#
# Harvested OAI-PMH 2.0 MARCXML Record validator.
#
# https://aleph.mzk.cz/OAI?verb=GetRecord&identifier=oai:aleph.mzk.cz:MZK01-000152782&metadataPrefix=marc21
#

# INCLUDE -------------------

from __future__ import print_function

import StringIO,json,sys,os,re

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

# INIT -------------------

try:
	log = open(LOG,'a',0)
except:
	print("Read only FS exiting..")
	exit(1)

try:
	os.mkdir('export')
	os.mkdir('export/marc')
	os.mkdir('export/json')
	os.mkdir('export/xml')
except: pass

registry = MetadataRegistry()
registry.registerReader('marc21', MarcXML)

oai = Client(URL, registry)

#records = oai.listSets()
#records = oai.listMetadataFormats()
#records = oai.listIdentifiers(metadataPrefix='marc21', set='STMUS')

try:
	print('Harversting..')
	records = oai.listRecords(metadataPrefix='marc21', set='STMUS', from_=datetime(2019,1,1), until=datetime(2019,2,1))# Ymd
except:
	print('Harversting failed.')
	sys.exit(1)

# MAIN -------------------

counter = 0

for record in records:

	header = record[0]
	#print tostring(record_header.element())
	#print header.identifier()
	#print header.datestamp()
	#print header.setSpec()

	metadata = record[1]
	#print metadata
	#print metadata.leader
	#print metadata.title()
	#print metadata.as_marc()
	#print metadata.as_dict()
	
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
	
	# VALIDATION ------------------
	
	#TEST: TAG EXISTS
	if not '002' in metadata: log.write(header.identifier() + ' Missing 002 tag.\n')
	#TEST: TAG SUBFIELD EXISTS
	if '100' in metadata:
		if not('a' and 'd' and '7') in metadata['100']:
			log.write(header.identifier() + ' Missing a,d,7 subfield group in 100 tag.\n')
	#TEST: TAG + TAG SUBFIELD EXISTS
	if '072' in metadata:
		if 'x' in metadata['072']:
			if not '245' in metadata:
				log.write(header.identifier() + ' Missing 245 tag when x subfield in 072 tag.\n')
	#TEST: EQUAL VALUE
	if '100' in metadata:
		if '260' in metadata:
			if metadata['100'].value() != metadata['260'].value():
				log.write(header.identifier() + ' Value of 100 and 260 not equal.\n')
	#TEST: VALUE IN LIST
	LIST = ('auto','kolo','vlak')
	if '260' in metadata:
		if not metadata['260'].value() in LIST:
				log.write(header.identifier() + ' Value of 260 not in list.\n')
	#TEST: PRINT VALUE FROM LIST
	for TAG in ('001','005','007'):
		if TAG in metadata:
			log.write(header.identifier() + ' Tag ' + TAG + ' value: ' + metadata[TAG].value() + '\n')
	#TEST: VALUE DATE FORMAT
	if '001' in metadata:
		if not re.match('\d+', metadata['001'].value()):
			log.write(header.identifier() + ' Tag 001 invalid data format.\n')

	# EXPORT -------------------

	# MARC21
	writer = MARCWriter(open('export/marc/' + record[0].identifier() + '.dat', 'wb'))
	writer.write(record[1])
	writer.close()
	# JSON
	writer = JSONWriter(open('export/json/'+ record[0].identifier() + '.json', 'wt'))
	writer.write(record[1])
	writer.close()
	# MARCXML
	writer = XMLWriter(open('export/xml/' + record[0].identifier() + '.xml', 'wb'))
	writer.write(record[1])
	writer.close()

	counter+=1

log.write("\nTOTAL: " + str(counter))
log.close()

