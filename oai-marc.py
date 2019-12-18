#!/usr/bin/python
# -*- coding: utf-8 -*-
#
# Harvested OAI-PMH 2.0 MARCXML Record validator.
#
# https://aleph.mzk.cz/OAI?verb=GetRecord&identifier=oai:aleph.mzk.cz:MZK01-000152782&metadataPrefix=marc21
#
# TODO:
#
# TEST VALUE RANGE
# TEST SUBFIELD ORDER
# TEST ' ,', ' .' regexp DATA
# IDENT + SIF - Error code.
#

# INCLUDE -------------------

from __future__ import print_function

import argparse,StringIO,urllib,sys,os,re

from datetime import datetime
from oaipmh.client import Client
from oaipmh.metadata import MetadataRegistry
from pymarc import marcxml,MARCWriter,JSONWriter,XMLWriter
from lxml.etree import tostring

# VAR -------------------

#URL='https://aleph.mzk.cz/OAI'
URL='https://aleph.lib.cas.cz/OAI'
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
		raise argparse.ArgumentTypeError('Invalid date format.')

def valid_format(s):
	if s in ('json', 'marc', 'xml'): return s
	else:
		raise argparse.ArgumentTypeError('Invalid export format.')

def valid_display(s):
	if s in ('ident', 'json', 'marc'): return s
	else:
		raise argparse.ArgumentTypeError('Invalid display format.')

def valid_request(s):
	if s in ('record', 'set', 'meta'): return s
	else:
		raise argparse.ArgumentTypeError('Invalid request format.')

def url_response(url):
	try: 
		if urllib.urlopen(url).getcode() == 200: return 1
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
	except: pass
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

		#TEST TAG ------------------

		for TAG in ('001', '003', '005', '008', '040', '080', '245', '520', '655', '910', '964', 'SIF', 'OWN'):
			if not TAG in metadata:
				log.write(header.identifier() + ' Chybí pole ' + TAG + '.\n')
		if not ('KAT' or 'CAT') in metadata:
			log.write(header.identifier() + ' Chybí pole KAT/CAT.\n')
		if not metadata.leader:
			log.write(header.identifier() + ' Chybí pole LDR.\n')
	
		#TEST TAG/SUBFIELD VALUE ------------------

		if '003' in metadata:
			if metadata['003'].value() != 'CZ PrUCL':
				log.write(header.identifier() + ' Chybná hodnota v poli 003.\n')
		if '040' in metadata:
			if 'a' in metadata['040']:
				if metadata['040']['a'] != 'ABB060':
					log.write(header.identifier() + ' Chybná hodnota v podpoli 040a.\n')
			if 'b' in metadata['040']:
				if metadata['040']['b'] != 'cze':
					log.write(header.identifier() + ' Chybná hodnota v podpoli 040b.\n')
			if 'e' in metadata['040']:
				if metadata['040']['e'] != 'rda':
					log.write(header.identifier() + ' Chybná hodnota v podpoli 040e.\n')
		if '072' in metadata:
			if '2' in metadata['072']:
				if metadata['072']['2'] != 'Konspekt':
					log.write(header.identifier() + ' Chybná hodnota v podpoli 072-2.\n')
		if '082' in metadata:
			if '2' in metadata['082']:
				if not metadata['082']['2'] in ('MRF', 'MRF-sel'):
					log.write(header.identifier() + ' Chybná hodnota v podpoli 082-2.\n')
		if '910' in metadata:
			if 'a' in metadata['910']:
				if metadata['910']['a'] != 'ABB060':
					log.write(header.identifier() + ' Chybná hodnota v podpoli 910a.\n')
		if 'OWN' in metadata:
			if metadata['OWN'].value() != 'UCLA':
				log.write(header.identifier() + ' Chybná hodnota v poli OWN.\n')
		if '856' in metadata:
			if '4' in metadata['856']:
				if metadata['856']['4'] != 'N':
					log.write(header.identifier() + ' Chybná hodnota v podpoli 856-4.\n')
			if 'y' in metadata['856']:
				if not metadata['856']['y'] in ('online', 'Webarchiv'):
					log.write(header.identifier() + ' Chybná hodnota v podpoli 856y.\n')

		#TEST SUBFIELD ------------------

		for TAG in ('072', '080', '100', '245', '520', '600', '610', '611', '630', '648', '650', '651', '653', '655', '700', '710'):
			if TAG in metadata:
				if len(metadata[TAG].get_subfields('a')) != 1:
					log.write(header.identifier() + ' Chybí podpole ' + TAG + 'a.\n')
		if '022' in metadata:
			if not len(metadata['022'].get_subfields('a')) >= 1:
				log.write(header.identifier() + ' Chybí podpole 022a.\n')
		if '072' in metadata:
			if len(metadata['072'].get_subfields('x')) != 1:
				log.write(header.identifier() + ' Chybí podpole 072x.\n')
			if len(metadata['072'].get_subfields('2')) != 1:
				log.write(header.identifier() + ' Chybí podpole 072-2.\n')
			if len(metadata['072'].get_subfields('9')) != 1:
				log.write(header.identifier() + ' Chybí podpole 072-9.\n')
		if '080' in metadata:
			if len(metadata['080'].get_subfields('2')) != 1:
				log.write(header.identifier() + ' Chybí podpole 080-2.\n')
		if '700' in metadata:
			if not len(metadata['700'].get_subfields('4')) >= 1:
				log.write(header.identifier() + ' Chybí podpole 700-4.\n')
		if '710' in metadata:
			if not len(metadata['710'].get_subfields('4')) >= 1:
				log.write(header.identifier() + ' Chybí podpole 710-4.\n')
		if '773' in metadata:
			if len(metadata['773'].get_subfields('t')) != 1:
				log.write(header.identifier() + ' Chybí podpole 773t.\n')
			if len(metadata['773'].get_subfields('9')) != 1:
				log.write(header.identifier() + ' Chybí podpole 773-9.\n')
		if '787' in metadata:
			if len(metadata['787'].get_subfields('t')) != 1:
				log.write(header.identifier() + ' Chybí podpole 787t.\n')
			if len(metadata['787'].get_subfields('4')) != 1:
				log.write(header.identifier() + ' Chybí podpole 787-4.\n')
		if '856' in metadata:
			if len(metadata['856'].get_subfields('u')) != 1:
				log.write(header.identifier() + ' Chybí podpole 856u.\n')
			if len(metadata['856'].get_subfields('y')) != 1:
				log.write(header.identifier() + ' Chybí podpole 856y.\n')

		#TEST VALID LINK ------------------

		if '856' in metadata:
			if 'u' in metadata['856']:
				if not url_response(metadata['856']['u']):
					log.write(header.identifier() + ' Nefunkční link v poli 856u.\n')
		
		#TEST INDICATOR ------------------

		if '041' in metadata:
			if not metadata['041'].indicator1 + metadata['041'].indicator2 in ('1 ', '0 '):
				log.write(header.identifier() + ' Chybný indikátor v poli 041.\n')
		if '072' in metadata:
			if metadata['072'].indicator1 + metadata['072'].indicator2 != ' 7':
				log.write(header.identifier() + ' Chybný indikátor v poli 072.\n')
		if '100' in metadata:
			if not metadata['100'].indicator1 + metadata['100'].indicator2 in ('3 ', '1 ', '0 '):
				log.write(header.identifier() + ' Chybný indikátor v poli 100.\n')
		if '110' in metadata:
			if not metadata['110'].indicator1 + metadata['110'].indicator2 in ('1 ', '2 '):
				log.write(header.identifier() + ' Chybný indikátor v poli 110.\n')
		if '245' in metadata:
			if not metadata['245'].indicator1 in ('0', '1'):
				log.write(header.identifier() + ' Chybný indikátor v poli 245.\n')
		if '520' in metadata:
			if metadata['520'].indicator1 + metadata['520'].indicator2 != '2 ':
				log.write(header.identifier() + ' Chybný indikátor v poli 520.\n')
		if '600' in metadata:
			if not metadata['600'].indicator1 + metadata['600'].indicator2 in ('34', '37', '14', '17', '04', '07'):
				log.write(header.identifier() + ' Chybný indikátor v poli 600.\n')
		if '610' in metadata:
			if not metadata['610'].indicator1 + metadata['610'].indicator2 in ('14', '17', '24', '27'):
				log.write(header.identifier() + ' Chybný indikátor v poli 610.\n')
		if '611' in metadata:
			if not metadata['611'].indicator1 + metadata['611'].indicator2 in ('14', '17', '24', '27'):
				log.write(header.identifier() + ' Chybný indikátor v poli 611.\n')
		if '648' in metadata:
			if not metadata['648'].indicator1 + metadata['648'].indicator2 in (' 4', ' 7'):
				log.write(header.identifier() + ' Chybný indikátor v poli 648.\n')
		if '650' in metadata:
			if not metadata['650'].indicator1 + metadata['650'].indicator2 in ('14', '17', '04', '07'):
				log.write(header.identifier() + ' Chybný indikátor v poli 650.\n')
		if '651' in metadata:
			if not metadata['651'].indicator1 + metadata['651'].indicator2 in (' 4', ' 7'):
				log.write(header.identifier() + ' Chybný indikátor v poli 651.\n')
		if '653' in metadata:
			if metadata['653'].indicator1 + metadata['653'].indicator2 != '0 ':
				log.write(header.identifier() + ' Chybný indikátor v poli 653.\n')
		if '655' in metadata:
			if not metadata['655'].indicator1 + metadata['655'].indicator2 in (' 4', ' 7'):
				log.write(header.identifier() + ' Chybný indikátor v poli 655.\n')
		if '700' in metadata:
			if not metadata['700'].indicator1 + metadata['700'].indicator2 in ('3 ', '1 ', '0 '):
				log.write(header.identifier() + ' Chybný indikátor v poli 700.\n')
		if '710' in metadata:
			if not metadata['710'].indicator1 + metadata['710'].indicator2 in ('1 ', '2 '):
				log.write(header.identifier() + ' Chybný indikátor v poli 710.\n')
		if '773' in metadata:
			if metadata['773'].indicator1 + metadata['773'].indicator2 != '0 ':
				log.write(header.identifier() + ' Chybný indikátor v poli 773.\n')
		if '787' in metadata:
			if metadata['787'].indicator1 + metadata['787'].indicator2 != '08':
				log.write(header.identifier() + ' Chybný indikátor v poli 787.\n')

		#TEST DEPENDENCE ------------------

		if metadata.leader[7] == 'm':
			if not ('260' or '264') in metadata:
				log.write(header.identifier() + ' Chybný typ záznamu (pole 260/264).\n')
		for TAG in ('250', '490', '830'):
			if TAG in metadata:
				if metadata.leader[7] != 'm':
					log.write(header.identifier() + ' Chybný typ záznamu (pole ' + TAG + ').\n')
		if metadata.leader[7] in ('a', 'b'):
			if not '773' in metadata:
				log.write(header.identifier() + ' Chybný typ záznamu (pole 773).\n')

		#TEST SUBFIELD RANGE ------------------

		if '040' in metadata:
			for SUB in metadata['040'].subfields[0::2]:
				if SUB not in ('a', 'b', 'e'):
					log.write(header.identifier() + ' Chybný kód podpole v poli 040.\n')
		if '070' in metadata:
			for SUB in metadata['070'].subfields[0::2]:
				if SUB not in ('a', 'x', '2', '9'):
					log.write(header.identifier() + ' Chybný kód podpole v poli 070.\n')
		if '100' in metadata:
			for SUB in metadata['100'].subfields[0::2]:
				if SUB not in ('a', 'b', 'c', 'd', 'g', '4', '7', 'x'):
					log.write(header.identifier() + ' Chybný kód podpole v poli 100.\n')
		if '110' in metadata:
			for SUB in metadata['110'].subfields[0::2]:
				if SUB not in ('a', 'b', 'c', 'd', 'n', '4', '7', 'x'):
					log.write(header.identifier() + ' Chybný kód podpole v poli 110.\n')
		if '111' in metadata:
			for SUB in metadata['111'].subfields[0::2]:
				if SUB not in ('a', 'b', 'c', 'd', 'n', '4', '7', 'x'):
					log.write(header.identifier() + ' Chybný kód podpole v poli 111.\n')
		if '245' in metadata:
			for SUB in metadata['245'].subfields[0::2]:
				if SUB not in ('a', 'b', 'n', 'p', 'c'):
					log.write(header.identifier() + ' Chybný kód podpole v poli 245.\n')
		if '250' in metadata:
			for SUB in metadata['250'].subfields[0::2]:
				if SUB != 'a':
					log.write(header.identifier() + ' Chybný kód podpole v poli 250.\n')
		if '260' in metadata:
			for SUB in metadata['260'].subfields[0::2]:
				if SUB not in ('a', 'b', 'c'):
					log.write(header.identifier() + ' Chybný kód podpole v poli 260.\n')
		if '264' in metadata:
			for SUB in metadata['264'].subfields[0::2]:
				if SUB not in ('a', 'b', 'c'):
					log.write(header.identifier() + ' Chybný kód podpole v poli 264.\n')
		if '300' in metadata:
			for SUB in metadata['300'].subfields[0::2]:
				if SUB not in ('a', 'b', 'e'):
					log.write(header.identifier() + ' Chybný kód podpole v poli 300.\n')
		if '490' in metadata:
			for SUB in metadata['490'].subfields[0::2]:
				if SUB not in ('a', 'v'):
					log.write(header.identifier() + ' Chybný kód podpole v poli 490.\n')
		if '500' in metadata:
			for SUB in metadata['500'].subfields[0::2]:
				if SUB != 'a':
					log.write(header.identifier() + ' Chybný kód podpole v poli 500.\n')
		if '505' in metadata:
			for SUB in metadata['505'].subfields[0::2]:
				if SUB not in ('t', 'r', 'g'):
					log.write(header.identifier() + ' Chybný kód podpole v poli 505.\n')
		if '520' in metadata:
			for SUB in metadata['520'].subfields[0::2]:
				if SUB not in ('a', '2'):
					log.write(header.identifier() + ' Chybný kód podpole v poli 520.\n')
		if '600' in metadata:
			for SUB in metadata['600'].subfields[0::2]:
				if SUB not in ('a', 'b', 'c', 'd', 'q', '7', '2', 'x'):
					log.write(header.identifier() + ' Chybný kód podpole v poli 600.\n')
		if '610' in metadata:
			for SUB in metadata['610'].subfields[0::2]:
				if SUB not in ('a', 'b', 'c', 'd', 'n', '7', '2', 'x'):
					log.write(header.identifier() + ' Chybný kód podpole v poli 610.\n')
		if '611' in metadata:
			for SUB in metadata['611'].subfields[0::2]:
				if SUB not in ('a', 'b', 'c', 'd', 'n', '7', '2', 'x'):
					log.write(header.identifier() + ' Chybný kód podpole v poli 611.\n')
		if '630' in metadata:
			for SUB in metadata['630'].subfields[0::2]:
				if SUB not in ('a', 'l', '7', '2', 'x'):
					log.write(header.identifier() + ' Chybný kód podpole v poli 630.\n')
		if '648' in metadata:
			for SUB in metadata['648'].subfields[0::2]:
				if SUB not in ('a', '7', '2', 'x'):
					log.write(header.identifier() + ' Chybný kód podpole v poli 648.\n')
		if '650' in metadata:
			for SUB in metadata['650'].subfields[0::2]:
				if SUB not in ('a', '7', '2', 'x'):
					log.write(header.identifier() + ' Chybný kód podpole v poli 650.\n')
		if '651' in metadata:
			for SUB in metadata['651'].subfields[0::2]:
				if SUB not in ('a', '7', '2', 'x'):
					log.write(header.identifier() + ' Chybný kód podpole v poli 651.\n')
		if '653' in metadata:
			for SUB in metadata['653'].subfields[0::2]:
				if SUB not in ('a', 'x'):
					log.write(header.identifier() + ' Chybný kód podpole v poli 653.\n')
		if '655' in metadata:
			for SUB in metadata['655'].subfields[0::2]:
				if SUB not in ('a', '7', '2', 'x'):
					log.write(header.identifier() + ' Chybný kód podpole v poli 655.\n')
		if '700' in metadata:
			for SUB in metadata['700'].subfields[0::2]:
				if SUB not in ('a', 'b', 'c', 'd', 'q', '4', '7', 'x'):
					log.write(header.identifier() + ' Chybný kód podpole v poli 700.\n')
		if '710' in metadata:
			for SUB in metadata['710'].subfields[0::2]:
				if SUB not in ('a', 'b', 'c', 'd', 'n', '4', '7', 'x'):
					log.write(header.identifier() + ' Chybný kód podpole v poli 710.\n')
		if '773' in metadata:
			for SUB in metadata['773'].subfields[0::2]:
				if SUB not in ('a', 't', 'x', 'n', 'd', 'b', 'k', 'y', 'g', 'q', '9'):
					log.write(header.identifier() + ' Chybný kód podpole v poli 773.\n')
		if '787' in metadata:
			for SUB in metadata['787'].subfields[0::2]:
				if SUB not in ('i', 'a', 't', 'x', 'n', 'd', 'b', 'k', 'y', '4'):
					log.write(header.identifier() + ' Chybný kód podpole v poli 787.\n')
		if '830' in metadata:
			for SUB in metadata['830'].subfields[0::2]:
				if SUB != 'a':
					log.write(header.identifier() + ' Chybný kód podpole v poli 830.\n')
		if '856' in metadata:
			for SUB in metadata['856'].subfields[0::2]:
				if SUB not in ('u', 'y', '4'):
					log.write(header.identifier() + ' Chybný kód podpole v poli 856.\n')
		if '910' in metadata:
			for SUB in metadata['910'].subfields[0::2]:
				if SUB != 'a':
					log.write(header.identifier() + ' Chybný kód podpole v poli 910.\n')
		if '964' in metadata:
			for SUB in metadata['964'].subfields[0::2]:
				if SUB != 'a':
					log.write(header.identifier() + ' Chybný kód podpole v poli 964.\n')
		
		#TEST SUBFIELD ORDER ------------------

		#TEST SUBFIELD REPEAT ------------------
		
		if '041' in metadata:
			for SUB in metadata['041'].subfields[0::2]:
				if SUB not in ('a', 'b', 'k', 'h'):
					if len(metadata['041'].get_subfields(SUB)) > 1:
						log.write(header.identifier() + ' Neplatné opakování podpolí v poli 041.\n')
		if '044' in metadata:
			for SUB in metadata['041'].subfields[0::2]:
				if SUB != 'a':
					if len(metadata['044'].get_subfields(SUB)) > 1:
						log.write(header.identifier() + ' Neplatné opakování podpolí v poli 044.\n')
		if '245' in metadata:
			for SUB in metadata['245'].subfields[0::2]:
				if SUB not in ('n', 'p'):
					if len(metadata['245'].get_subfields(SUB)) > 1:
						log.write(header.identifier() + ' Neplatné opakování podpolí v poli 245.\n')
		if '260' in metadata:
			for SUB in metadata['260'].subfields[0::2]:
				if SUB not in ('a', 'b'):
					if len(metadata['260'].get_subfields(SUB)) > 1:
						log.write(header.identifier() + ' Neplatné opakování podpolí v poli 260.\n')
		if '264' in metadata:
			for SUB in metadata['264'].subfields[0::2]:
				if SUB not in ('a', 'b'):
					if len(metadata['264'].get_subfields(SUB)) > 1:
						log.write(header.identifier() + ' Neplatné opakování podpolí v poli 264.\n')
		if '787' in metadata:
			for SUB in metadata['787'].subfields[0::2]:
				if SUB != 'k':
					if len(metadata['787'].get_subfields(SUB)) > 1:
						log.write(header.identifier() + ' Neplatné opakování podpolí v poli 787.\n')

		#TEST VALUE RANGE ------------------

		#TEST SUBFIELD DEPENDENCE ------------------

		if '041' in metadata:
			if metadata['041'].indicator1 == '0':
				if 'h' in metadata['041']:
					log.write(header.identifier() + " Chybné podpole 'h' v poli 041.\n")
		if '041' in metadata:
			if metadata['041'].indicator1 == '1':
				if not 'h' in metadata['041']:
					log.write(header.identifier() + " Chybné podpole 'h' v poli 041.\n")
		if '100' in metadata:
			if metadata['100'].indicator1 == '0':
				if 'a' in metadata['100']:
					if re.match('^.*,.+$', metadata['100']['a']):
						log.write(header.identifier() + " Chybný znak v podpoli 'a' v poli 100.\n")
			if metadata['100'].indicator1 == '1':
				if 'c' in metadata['100']:
					if re.match('^\[.*$', metadata['100']['c']):
						log.write(header.identifier() + " Chybný znak v podpoli 'c' v poli 100.\n")
				if 'a' in metadata['100']:
					if re.match('^.*\..*$', metadata['100']['a']):
						log.write(header.identifier() + " Chybný znak v podpoli 'a' v poli 100.\n")
		if '245' in metadata:
			if metadata['245'].indicator1 == '1':
				N=0
				for TAG in ('100', '110', '111', '130'):
					if TAG in metadata: N+=1
				if N != 1:
					log.write(header.identifier() + 'Neplatné pole, chybný indikátor v poli 245.\n')
			if metadata['245'].indicator1 == '0':
				N=0
				for TAG in ('100', '110', '111', '130'):
					if TAG in metadata: N+=1
				if N > 0:
					log.write(header.identifier() + 'Neplatné pole, chybný indikátor v poli 245.\n')
			if metadata['245'].indicator2 == '0':
				if 'a' in metadata['245']:
					for S in ('The ', 'An ', 'Der ', 'Die ', 'Das ', 'Le ', 'La '):
						if re.match('^' + S + '.*', metadata['245']['a']):
							log.write(header.identifier() + ' Neplatný prefix, chybný 2. indikátor v poli 245.\n')
		if '600' in metadata:
			if metadata['600'].indicator1 == '0':
				if 'a' in metadata['600']:
					if re.match('^.*,.+$', metadata['600']['a']):
						log.write(header.identifier() + " Chybný znak v podpoli 'a' v poli 600.\n")
		for TAG in ('600', '610', '611', '630' ,'648', '650', '651', '655'):
			if TAG in metadata:
				if metadata[TAG].indicator2 == '7':
					if not ('2' or '7') in metadata[TAG]: 
						log.write(header.identifier() + ' Chybný 2. indikátor v poli ' + TAG + '.\n')
					elif metadata[TAG]['2'] != 'czenas':
						log.write(header.identifier() + ' Chybný 2. indikátor v poli ' + TAG + '.\n')
				if metadata[TAG].indicator2 == '4':
					if '7' in metadata[TAG]: 
						log.write(header.identifier() + ' Chybný 2. indikátor v poli ' + TAG + '.\n')
					if '2' in metadata[TAG]: 
						if metadata[TAG]['2'] == 'czenas':
							log.write(header.identifier() + ' Chybný 2. indikátor v poli ' + TAG + '.\n')

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

