#!/usr/bin/python
#
# Harvested OAI-PMH 2.0 MARCXML Record validator.
#
#https://aleph.mzk.cz/OAI?verb=GetRecord&identifier=oai:aleph.mzk.cz:MZK01-000152782&metadataPrefix=marc21
#

# VAR -------------------

from __future__ import print_function

import StringIO,sys

from oaipmh.client import Client
from oaipmh.metadata import MetadataRegistry
from pymarc import marcxml
from lxml.etree import tostring

# VAR -------------------

URL='https://aleph.mzk.cz/OAI'

# DEF -------------------

def MarcXML(xml):
	handler = marcxml.XmlHandler()
	marcxml.parse_xml(StringIO.StringIO(tostring(xml, encoding='utf-8')), handler)
	return handler.records[0]

# INSTANCE -------------------

registry = MetadataRegistry()
registry.registerReader('marc21', MarcXML)

oai = Client(URL, registry)

#records = oai.listSets()
#records = oai.listMetadataFormats()
#records = oai.listIdentifiers(metadataPrefix='marc21', set='STMUS')

try:
	print("Harversting..")
	records = oai.listRecords(metadataPrefix='marc21', set='STMUS')
except:
	print("Harverst failed.")
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
	#print metadata.title()
	#print metadata.as_marc()
	#print metadata.as_dict()
	
	# skip deleted records
	if header.isDeleted(): continue

	# retry missing metadata(?)
	if not metadata:
		print(header.identifier() + " Missing matadata. Retrying..")
		retry = oai.getRecord(metadataPrefix='marc21', identifier=header.identifier())
		if not retry[1]:
			print(header.identifier() + " Missing retry metadata.")
			continue
		else:
			header = retry[0]
			metadata = retry[1]
	
	#print metadata
	#print metadata.leader
	#if '005' in metadata: print metadata['005'].value()

	if '100' in metadata:
		if 'a' in metadata['100']:
			if metadata['100']['a'] == '': print(header.identifier() + " Empty author!")
	if '245' in metadata:
		if 'a' in metadata['245']:
			 if metadata['245']['a'] == '': print(header.identifier() + " Empty description!")
	
	#js = json.loads(meta.as_json(encoding='utf-8'))
	#print json.dumps(js, indent=2, sort_keys=True)

	counter+=1
	#if counter == 5: break

print("\nTOTAL: " + str(counter))

