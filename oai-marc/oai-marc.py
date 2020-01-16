#!/usr/bin/python
# -*- coding: utf-8 -*-
#
# OAI-PMH 2.0 MARCXML Record validator.
#
# TODO:
#
# FIX DISPLAY/EXPORT
#

# INCLUDE -------------------

from __future__ import print_function

import argparse,StringIO,urllib2,smtplib,sys,os,re

from email.mime.text import MIMEText
from datetime import datetime
from oaipmh.client import Client
from oaipmh.metadata import MetadataRegistry
from pymarc import marcxml,MARCWriter,JSONWriter,XMLWriter
from lxml.etree import tostring

# VAR -------------------

URL='https://aleph.lib.cas.cz/OAI'
LOG='oai-marc.html'

COUNTRY_CODE='country_code.txt'
LANG_CODE='lang_code.txt'
ROLE_CODE='role_code.txt'
SIF_CODE='sif_code.txt'

ERROR={}

MAIL_SENDER='webmaster@pokuston.ucl.cas.cz'
MAIL_SERVER='listonos.ucl.cas.cz'

HTML_HEADER='''
<html>
<head><meta charset="utf-8"></head>
<body style="background-color:black;color:#6DAE42;">
<a style="color:white;" href="/">[Zpět]</a>
'''

HTML_FOOTER='''
<a style="color:white;" href="/">[Zpět]</a>
</body>
</html>
'''

COUNTER=0
MATCH=0

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
		if urllib2.urlopen(url, timeout=10).getcode() == 200: return 1
	except: pass
	return 0

def notify():
	html_header = 'Dobrý den,<br><br>Ve vašich záznamech byly při kontrolách nalezeny následující chyby:<br><br>'
	html_footer =('<br>Prosíme o opravu.<br>---------------------------<br><br>' +
		'TATO ZPRÁVA BYLA VYGENEROVÁNA AUTOMATICKY,<br>NEODPOVÍDEJTE NA NI.<br>')
	for sif in ERROR:
		if sif in sif_code:# match email
			for aleph in ERROR[sif]:
				for error in ERROR[sif][aleph]:
					html_body+=('<a target="_blank" href="https://aleph22.lib.cas.cz/F/?func=direct&doc_number=' +
						aleph + '&local_base=AV">' + aleph + '</a> ' + ' [' + error[0] + '] ' + error[1] + '<br>')
			try:
				msg = MIMEText((html_header + html_body + html_footer).decode('utf-8'), 'html', 'utf-8')
				msg['Subject'] = 'Kontrolní zpráva'
				msg['From'] = 'Kontrola MARC <' + MAIL_SENDER + '>'
				msg['To'] = ADDR
				s = smtplib.SMTP(MAIL_SERVER)
				s.sendmail(MAIL_SENDER, ADDR, msg.as_string())
				s.quit()
			except: pass

def write_error(id,tag,sif,code,code_text):
	aleph = re.sub('^.*-(\d+)$', '\\1', id)
	SIF = sif.lower()
	# error counter
	global MATCH
	MATCH+=1
	# update errors
	if SIF not in ERROR: ERROR[SIF] = {}
	if aleph not in ERROR[SIF]: ERROR[SIF][aleph] = []
	ERROR[SIF][aleph].append((code,code_text))
	# write daily HTML
	log.write(
		'<p><a style="color:#6DAE42;" target="_blank" href="https://aleph22.lib.cas.cz/F/?func=direct&doc_number=' +
		aleph + '&local_base=AV">' + aleph + '</a>' + ' [<font color="gold">' + sif + '</font>] ' +
		' [<font color="gold">' + code + '</font>] <font color="white">' + code_text + '</font></p>\n'
	)
	# write global CSV <- will be removed. RUN ONCE
	return

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
optional.add_argument('--notify', help='Enable notification.', action='store_true')
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

try:
	with open(COUNTRY_CODE, 'r') as f: country_code = f.read().splitlines()
except: country_code = ''

try:
	with open(LANG_CODE, 'r') as f:	lang_code = f.read().splitlines()
except: lang_code = ''

try:
	with open(ROLE_CODE, 'r') as f:	role_code = f.read().splitlines()
except: role_code = ''

try:
	sif_code = {}
	with open(SIF_CODE, 'r') as f:
		for line in f:
			acct_code, acct_addr = line.decode('utf-8').split(':')
			sif_code[acct_code] = acct_addr.strip()
except: sif_code = ''

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
		os.mkdir('export/' + args.export)
	except: pass

if args.check: log.write(HTML_HEADER)

# MAIN -------------------

for record in records:

	if args.get == 'set' or args.get == 'meta':
		print(record[0])
		COUNTER+=1
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

		# TEST TAG ------------------

		if 'SIF' in metadata:
			if 'a' in metadata['SIF']: SIF = metadata['SIF']['a'].encode('utf-8')
		else:
			SIF = ''
			write_error(header.identifier(), 'SIF', SIF, '000', 'Chybí pole SIF.')
		for TAG in ('001', '003', '005', '008', '040', '080', '245', '520', '655', '910', '964', 'OWN'):
			if TAG not in metadata:
				write_error(header.identifier(), TAG, SIF, '001', 'Chybí pole ' + TAG + '.')
		if 'KAT' not in metadata and 'CAT' not in metadata:
			write_error(header.identifier(), 'CAT/KAT', SIF, '002', 'Chybí pole KAT/CAT.')
		if not metadata.leader:
			write_error(header.identifier(), 'LDR', SIF, '003', 'Chybí pole LDR.')
	
		# TEST TAG/SUBFIELD VALUE ------------------

		if '003' in metadata:
			if metadata['003'].value() != 'CZ PrUCL':
				write_error(header.identifier(), '003', SIF, '004', 'Chybná hodnota v poli 003.')
		if '040' in metadata:
			if 'a' in metadata['040']:
				if metadata['040']['a'] != 'ABB060':
					write_error(header.identifier(), '040', SIF, '005', 'Chybná hodnota v podpoli 040a.')
			if 'b' in metadata['040']:
				if metadata['040']['b'] != 'cze':
					write_error(header.identifier(), '040', SIF, '006', 'Chybná hodnota v podpoli 040b.')
			if 'e' in metadata['040']:
				if metadata['040']['e'] != 'rda':
					write_error(header.identifier(), '040', SIF, '007', 'Chybná hodnota v podpoli 040e.')
		if '072' in metadata:
			if '2' in metadata['072']:
				if metadata['072']['2'] != 'Konspekt':
					write_error(header.identifier(), '072', SIF, '008', 'Chybná hodnota v podpoli 072-2.')
		if '082' in metadata:
			if '2' in metadata['082']:
				if metadata['082']['2'] not in ('MRF', 'MRF-sel'):
					write_error(header.identifier(), '082', SIF, '009', 'Chybná hodnota v podpoli 082-2.')
		if '910' in metadata:
			if 'a' in metadata['910']:
				if metadata['910']['a'] != 'ABB060':
					write_error(header.identifier(), '910', SIF, '010', 'Chybná hodnota v podpoli 910a.')
		if 'OWN' in metadata:
			if metadata['OWN'].value() != 'UCLA':
				write_error(header.identifier(), 'OWN', SIF, '011', 'Chybná hodnota v poli OWN.')
		for F in metadata.get_fields('856'):
			if '4' in F:
				if F['4'] != 'N':
					write_error(header.identifier(), '856', SIF, '012', 'Chybná hodnota v podpoli 856-4.')
			if 'y' in F:
				if F['y'] not in ('online', 'Webarchiv', 'Obsah knihy'):
					write_error(header.identifier(), '856', SIF, '013', 'Chybná hodnota v podpoli 856y.')

		# TEST SUBFIELD ------------------

		for TAG in ('072', '100', '245', '520'):
			if TAG in metadata:
				if len(metadata[TAG].get_subfields('a')) != 1:
					write_error(header.identifier(), TAG, SIF, '014', 'Chybí podpole ' + TAG + 'a.')
		for TAG in ('080', '600', '610', '611', '630', '648', '650', '651', '653', '655', '700', '710'):
			for F in metadata.get_fields(TAG):
				if len(F.get_subfields('a')) != 1:
					write_error(header.identifier(), TAG, SIF, '015', 'Chybí podpole ' + TAG + 'a.')
		for F in metadata.get_fields('022'):
			if len(F.get_subfields('a')) == 0:
				write_error(header.identifier(), '022', SIF, '016', 'Chybí podpole 022a.')
		if '072' in metadata:
			if len(metadata['072'].get_subfields('x')) != 1:
				write_error(header.identifier(), '072', SIF, '017', 'Chybí podpole 072x.')
			if len(metadata['072'].get_subfields('2')) != 1:
				write_error(header.identifier(), '072', SIF, '018', 'Chybí podpole 072-2.')
			if len(metadata['072'].get_subfields('9')) != 1:
				write_error(header.identifier(), '072', SIF, '019', 'Chybí podpole 072-9.')
		for F in metadata.get_fields('080'):
			if len(F.get_subfields('2')) != 1:
				write_error(header.identifier(), '080', SIF, '020', 'Chybí podpole 080-2.')
		for F in metadata.get_fields('700'):
			if len(F.get_subfields('4')) == 0:
				write_error(header.identifier(), '700', SIF, '021', 'Chybí podpole 700-4.')
		for F in metadata.get_fields('710'):
			if len(F.get_subfields('4')) == 0:
				write_error(header.identifier(), '710', SIF, '022', 'Chybí podpole 710-4.')
		for F in metadata.get_fields('773'):
			if len(F.get_subfields('t')) != 1:
				write_error(header.identifier(), '773', SIF, '023', 'Chybí podpole 773t.')
			if len(F.get_subfields('9')) != 1:
				write_error(header.identifier(), '773', SIF, '024', 'Chybí podpole 773-9.')
		for F in metadata.get_fields('787'):
			if len(F.get_subfields('t')) != 1:
				write_error(header.identifier(), '787', SIF, '025', 'Chybí podpole 787t.')
			if len(F.get_subfields('4')) != 1:
				write_error(header.identifier(), '787', SIF, '026', 'Chybí podpole 787-4.')
		for F in metadata.get_fields('856'):
			if len(F.get_subfields('u')) != 1:
				write_error(header.identifier(), '856', SIF, '027', 'Chybí podpole 856u.')
			if len(F.get_subfields('y')) != 1:
				write_error(header.identifier(), '856', SIF, '028', 'Chybí podpole 856y.')

		# TEST VALID LINK ------------------

		for F in metadata.get_fields('856'):
			if 'u' in F:
				if not url_response(F['u']):
					write_error(header.identifier(), '856', SIF, '029', 'Nefunkční odkaz v poli 856u.')
		
		# TEST INDICATOR ------------------

		if '041' in metadata:
			if metadata['041'].indicator1 + metadata['041'].indicator2 not in ('1 ', '0 '):
				write_error(header.identifier(), '041', SIF, '030', 'Chybný indikátor v poli 041.')
		if '072' in metadata:
			if metadata['072'].indicator1 + metadata['072'].indicator2 != ' 7':
				write_error(header.identifier(), '072', SIF, '031', 'Chybný indikátor v poli 072.')
		if '100' in metadata:
			if metadata['100'].indicator1 + metadata['100'].indicator2 not in ('3 ', '1 ', '0 '):
				write_error(header.identifier(), '100', SIF, '032', 'Chybný indikátor v poli 100.')
		if '110' in metadata:
			if metadata['110'].indicator1 + metadata['110'].indicator2 not in ('1 ', '2 '):
				write_error(header.identifier(), '110', SIF, '033', 'Chybný indikátor v poli 110.')
		if '245' in metadata:
			if metadata['245'].indicator1 not in ('0', '1'):
				write_error(header.identifier(), '245', SIF, '034', 'Chybný indikátor v poli 245.')
		if '520' in metadata:
			if metadata['520'].indicator1 + metadata['520'].indicator2 != '2 ':
				write_error(header.identifier(), '520', SIF, '035', 'Chybný indikátor v poli 520.')
		for F in metadata.get_fields('600'):
			if F.indicator1 + F.indicator2 not in ('34', '37', '14', '17', '04', '07'):
				write_error(header.identifier(), '600', SIF, '036', 'Chybný indikátor v poli 600.')
		for F in metadata.get_fields('610'):
			if F.indicator1 + F.indicator2 not in ('14', '17', '24', '27'):
				write_error(header.identifier(), '610', SIF, '037', 'Chybný indikátor v poli 610.')
		for F in metadata.get_fields('611'):
			if F.indicator1 + F.indicator2 not in ('14', '17', '24', '27'):
				write_error(header.identifier(), '611', SIF, '038', 'Chybný indikátor v poli 611.')
		for F in metadata.get_fields('648'):
			if F.indicator1 + F.indicator2 not in (' 4', ' 7'):
				write_error(header.identifier(), '648', SIF, '039', 'Chybný indikátor v poli 648.')
		for F in metadata.get_fields('650'):
			if F.indicator1 + F.indicator2 not in ('14', '17', '04', '07'):
				write_error(header.identifier(), '650', SIF, '040', 'Chybný indikátor v poli 650.')
		for F in metadata.get_fields('651'):
			if F.indicator1 + F.indicator2 not in (' 4', ' 7'):
				write_error(header.identifier(), '651', SIF, '041', 'Chybný indikátor v poli 651.')
		for F in metadata.get_fields('653'):
			if F.indicator1 + F.indicator2 != '0 ':
				write_error(header.identifier(), '653', SIF, '042', 'Chybný indikátor v poli 653.')
		for F in metadata.get_fields('655'):
			if F.indicator1 + F.indicator2 not in (' 4', ' 7'):
				write_error(header.identifier(), '655', SIF, '043', 'Chybný indikátor v poli 655.')
		for F in metadata.get_fields('700'):
			if F.indicator1 + F.indicator2 not in ('3 ', '1 ', '0 '):
				write_error(header.identifier(), '700', SIF, '044', 'Chybný indikátor v poli 700.')
		for F in metadata.get_fields('710'):
			if F.indicator1 + F.indicator2 not in ('1 ', '2 '):
				write_error(header.identifier(), '710', SIF, '045', 'Chybný indikátor v poli 710.')
		for F in metadata.get_fields('773'):
			if F.indicator1 + F.indicator2 != '0 ':
				write_error(header.identifier(), '773', SIF, '046', 'Chybný indikátor v poli 773.')
		for F in metadata.get_fields('787'):
			if F.indicator1 + F.indicator2 != '08':
				write_error(header.identifier(), '787', SIF, '047', 'Chybný indikátor v poli 787.')

		# TEST DEPENDENCE ------------------

		if metadata.leader[7] == 'm':
			if '260' not in metadata and '264' not in metadata:
				write_error(header.identifier(), '260/264', SIF, '048', 'Chybný typ záznamu (pole 260/264).')
		for TAG in ('250', '490', '830'):
			if TAG in metadata:
				if metadata.leader[7] != 'm':
					write_error(header.identifier(), TAG, SIF, '098', 'Chybný typ záznamu (pole ' + TAG + ').')
		if metadata.leader[7] in ('a', 'b'):
			if '773' not in metadata:
				write_error(header.identifier(), '773', SIF, '050', 'Chybný typ záznamu (pole 773).')

		# TEST SUBFIELD RANGE ------------------

		if '040' in metadata:
			for SUB in metadata['040'].subfields[0::2]:
				if SUB not in ('a', 'b', 'e'):
					write_error(header.identifier(), '040', SIF, '051', 'Chybný kód podpole v poli 040.')
		if '072' in metadata:
			for SUB in metadata['072'].subfields[0::2]:
				if SUB not in ('a', 'x', '2', '9'):
					write_error(header.identifier(), '072', SIF, '052', 'Chybný kód podpole v poli 072.')
		if '100' in metadata:
			for SUB in metadata['100'].subfields[0::2]:
				if SUB not in ('a', 'b', 'c', 'd', 'g', '4', '7', 'x', 'q', 'j'):
					write_error(header.identifier(), '100', SIF, '053', 'Chybný kód podpole v poli 100.')
		if '110' in metadata:
			for SUB in metadata['110'].subfields[0::2]:
				if SUB not in ('a', 'b', 'c', 'd', 'n', '4', '7', 'x'):
					write_error(header.identifier(), '110', SIF, '054', 'Chybný kód podpole v poli 110.')
		if '111' in metadata:
			for SUB in metadata['111'].subfields[0::2]:
				if SUB not in ('a', 'b', 'c', 'd', 'n', '4', '7', 'x'):
					write_error(header.identifier(), '111', SIF, '055', 'Chybný kód podpole v poli 111.')
		if '245' in metadata:
			for SUB in metadata['245'].subfields[0::2]:
				if SUB not in ('a', 'b', 'n', 'p', 'c'):
					write_error(header.identifier(), '245', SIF, '056', 'Chybný kód podpole v poli 245.')
		for F in metadata.get_fields('250'):
			for SUB in F.subfields[0::2]:
				if SUB != 'a':
					write_error(header.identifier(), '250', SIF, '057', 'Chybný kód podpole v poli 250.')
		if '260' in metadata:
			for SUB in metadata['260'].subfields[0::2]:
				if SUB not in ('a', 'b', 'c'):
					write_error(header.identifier(), '260', SIF, '058', 'Chybný kód podpole v poli 260.')
		for F in metadata.get_fields('264'):
			for SUB in F.subfields[0::2]:
				if SUB not in ('a', 'b', 'c'):
					write_error(header.identifier(), '264', SIF, '059', 'Chybný kód podpole v poli 264.')
		if '300' in metadata:
			for SUB in metadata['300'].subfields[0::2]:
				if SUB not in ('a', 'b', 'e'):
					write_error(header.identifier(), '300', SIF, '060', 'Chybný kód podpole v poli 300.')
		for F in metadata.get_fields('490'):
			for SUB in F.subfields[0::2]:
				if SUB not in ('a', 'v'):
					write_error(header.identifier(), '490', SIF, '061', 'Chybný kód podpole v poli 490.')
		for F in metadata.get_fields('500'):
			for SUB in F.subfields[0::2]:
				if SUB != 'a':
					write_error(header.identifier(), '500', SIF, '062', 'Chybný kód podpole v poli 500.')
		for F in metadata.get_fields('505'):
			for SUB in F.subfields[0::2]:
				if SUB not in ('t', 'r', 'g'):
					write_error(header.identifier(), '505', SIF, '063', 'Chybný kód podpole v poli 505.')
		if '520' in metadata:
			for SUB in metadata['520'].subfields[0::2]:
				if SUB not in ('a', '2'):
					write_error(header.identifier(), '520', SIF, '064', 'Chybný kód podpole v poli 520.')
		for F in metadata.get_fields('600'):
			for SUB in F.subfields[0::2]:
				if SUB not in ('a', 'b', 'c', 'd', 'q', '7', '2', 'x'):
					write_error(header.identifier(), '600', SIF, '065', 'Chybný kód podpole v poli 600.')
		for F in metadata.get_fields('610'):
			for SUB in F.subfields[0::2]:
				if SUB not in ('a', 'b', 'c', 'd', 'n', '7', '2', 'x'):
					write_error(header.identifier(), '610', SIF, '066', 'Chybný kód podpole v poli 610.')
		for F in metadata.get_fields('611'):
			for SUB in F.subfields[0::2]:
				if SUB not in ('a', 'b', 'c', 'd', 'n', '7', '2', 'x'):
					write_error(header.identifier(), '611', SIF, '067', 'Chybný kód podpole v poli 611.')
		for F in metadata.get_fields('630'):
			for SUB in F.subfields[0::2]:
				if SUB not in ('a', 'l', '7', '2', 'x', 'p'):
					write_error(header.identifier(), '630', SIF, '068', 'Chybný kód podpole v poli 630.')
		for F in metadata.get_fields('648'):
			for SUB in F.subfields[0::2]:
				if SUB not in ('a', '7', '2', 'x'):
					write_error(header.identifier(), '648', SIF, '069', 'Chybný kód podpole v poli 648.')
		for F in metadata.get_fields('650'):
			for SUB in F.subfields[0::2]:
				if SUB not in ('a', '7', '2', 'x'):
					write_error(header.identifier(), '650', SIF, '070', 'Chybný kód podpole v poli 650.')
		for F in metadata.get_fields('651'):
			for SUB in F.subfields[0::2]:
				if SUB not in ('a', '7', '2', 'x'):
					write_error(header.identifier(), '651', SIF, '071', 'Chybný kód podpole v poli 651.')
		for F in metadata.get_fields('653'):
			for SUB in F.subfields[0::2]:
				if SUB not in ('a', 'x'):
					write_error(header.identifier(), '653', SIF, '072', 'Chybný kód podpole v poli 653.')
		for F in metadata.get_fields('655'):
			for SUB in F.subfields[0::2]:
				if SUB not in ('a', '7', '2', 'x'):
					write_error(header.identifier(), '655', SIF, '073', 'Chybný kód podpole v poli 655.')
		for F in metadata.get_fields('700'):
			for SUB in F.subfields[0::2]:
				if SUB not in ('a', 'b', 'c', 'd', 'q', '4', '7', 'x', 'j'):
					write_error(header.identifier(), '700', SIF, '074', 'Chybný kód podpole v poli 700.')
		for F in metadata.get_fields('710'):
			for SUB in F.subfields[0::2]:
				if SUB not in ('a', 'b', 'c', 'd', 'n', '4', '7', 'x'):
					write_error(header.identifier(), '710', SIF, '075', 'Chybný kód podpole v poli 710.')
		for F in metadata.get_fields('773'):
			for SUB in F.subfields[0::2]:
				if SUB not in ('a', 't', 'x', 'n', 'd', 'b', 'k', 'y', 'g', 'q', '9', 'z'):
					write_error(header.identifier(), '773', SIF, '076', 'Chybný kód podpole v poli 773.')
		for F in metadata.get_fields('787'):
			for SUB in F.subfields[0::2]:
				if SUB not in ('i', 'a', 't', 'n', 'd', 'b', 'k', 'h', 'z', 'y', '4'):
					write_error(header.identifier(), '787', SIF, '077', 'Chybný kód podpole v poli 787.')
		for F in metadata.get_fields('830'):
			for SUB in F.subfields[0::2]:
				if SUB != 'a':
					write_error(header.identifier(), '830', SIF, '078', 'Chybný kód podpole v poli 830.')
		for F in metadata.get_fields('856'):
			for SUB in F.subfields[0::2]:
				if SUB not in ('u', 'y', '4'):
					write_error(header.identifier(), '856', SIF, '079', 'Chybný kód podpole v poli 856.')
		if '910' in metadata:
			for SUB in metadata['910'].subfields[0::2]:
				if SUB != 'a':
					write_error(header.identifier(), '910', SIF, '080', 'Chybný kód podpole v poli 910.')
		for F in metadata.get_fields('964'):
			for SUB in F.subfields[0::2]:
				if SUB != 'a':
					write_error(header.identifier(), '964', SIF, '081', 'Chybný kód podpole v poli 964.')
		
		# TEST SUBFIELD ORDER ------------------

		# TEST SUBFIELD REPEAT ------------------
		
		if '041' in metadata:
			for SUB in metadata['041'].subfields[0::2]:
				if SUB not in ('a', 'b', 'k', 'h'):
					if len(metadata['041'].get_subfields(SUB)) > 1:
						write_error(header.identifier(), '041', SIF, '082', 'Neplatné opakování podpolí v poli 041.')
		if '044' in metadata:
			for SUB in metadata['044'].subfields[0::2]:
				if SUB != 'a':
					if len(metadata['044'].get_subfields(SUB)) > 1:
						write_error(header.identifier(), '044', SIF, '083', 'Neplatné opakování podpolí v poli 044.')
		if '245' in metadata:
			for SUB in metadata['245'].subfields[0::2]:
				if SUB not in ('n', 'p'):
					if len(metadata['245'].get_subfields(SUB)) > 1:
						write_error(header.identifier(), '245', SIF, '084', 'Neplatné opakování podpolí v poli 245.')
		if '260' in metadata:
			for SUB in metadata['260'].subfields[0::2]:
				if SUB not in ('a', 'b'):
					if len(metadata['260'].get_subfields(SUB)) > 1:
						write_error(header.identifier(), '260', SIF, '085', 'Neplatné opakování podpolí v poli 260.')
		for F in metadata.get_fields('264'):
			for SUB in F.subfields[0::2]:
				if SUB not in ('a', 'b'):
					if len(F.get_subfields(SUB)) > 1:
						write_error(header.identifier(), '264', SIF, '086', 'Neplatné opakování podpolí v poli 264.')
		for F in metadata.get_fields('773'):
			for SUB in F.subfields[0::2]:
				if SUB  != 'z':
					if len(F.get_subfields(SUB)) > 1:
						write_error(header.identifier(), '773', SIF, '087', 'Neplatné opakování podpolí v poli 773.')
		for F in metadata.get_fields('787'):
			for SUB in F.subfields[0::2]:
				if SUB not in ('k', 'z'):
					if len(F.get_subfields(SUB)) > 1:
						write_error(header.identifier(), '787', SIF, '088', 'Neplatné opakování podpolí v poli 787.')

		# TEST VALUE RANGE ------------------

		# TEST SUBFIELD DEPENDENCE ------------------

		if '041' in metadata:
			if metadata['041'].indicator1 == '0':
				if 'h' in metadata['041']:
					write_error(header.identifier(), '041', SIF, '089', "Chybné podpole 'h' v poli 041.")
		if '041' in metadata:
			if metadata['041'].indicator1 == '1':
				if 'h' not in metadata['041']:
					write_error(header.identifier(), '041', SIF, '090', "Chybné podpole 'h' v poli 041.")
		if '100' in metadata:
			if metadata['100'].indicator1 == '0':
				if 'a' in metadata['100']:
					if re.match('^.+\..+,.+$', metadata['100']['a']):
						write_error(header.identifier(), '100', SIF, '091', "Chybný znak v podpoli 'a' v poli 100.")
			if metadata['100'].indicator1 == '1':
				if 'c' in metadata['100']:
					if re.match('^\[.*$', metadata['100']['c']):
						write_error(header.identifier(), '100', SIF, '092', "Chybný znak v podpoli 'c' v poli 100.")
				#if 'a' in metadata['100']:
				#	if re.match('^.*\..*$', metadata['100']['a']):
				#		write_error(header.identifier(), '100', SIF, '093', "Chybný znak v podpoli 'a' v poli 100.")
		if '245' in metadata:
			if metadata['245'].indicator1 == '1':
				N=0
				for TAG in ('100', '110', '111', '130'):
					if TAG in metadata: N+=1
				if N != 1:
					write_error(header.identifier(), '245', SIF, '094', 'Neplatné pole, chybný indikátor v poli 245.')
			if metadata['245'].indicator1 == '0':
				N=0
				for TAG in ('100', '110', '111', '130'):
					if TAG in metadata: N+=1
				if N != 0:
					write_error(header.identifier(), '245', SIF, '095', 'Neplatné pole, chybný indikátor v poli 245.')
			if metadata['245'].indicator2 == '0':
				if 'a' in metadata['245']:
					for S in ('The ', 'An ', 'Der ', 'Die ', 'Das ', 'Le ', 'La '):
						if re.match('^' + S + '.*', metadata['245']['a']):
							write_error(header.identifier(), '245', SIF, '096', 'Neplatný prefix, chybný 2.indikátor v poli 245.')
		for F in metadata.get_fields('600'):
			if F.indicator1 == '0':
				if 'a' in F:
					if re.match('^.*,.+$', F['a']):
						write_error(header.identifier(), '600', SIF, '097', "Chybný znak v podpoli 'a' v poli 600.")
		for TAG in ('600', '610', '611', '630' ,'648', '650', '651', '655'):
			for F in metadata.get_fields(TAG):
				if F.indicator2 == '7':
					if '2' not in F or '7' not in F:
						write_error(header.identifier(), TAG, SIF, '098', 'Chybný 2.indikátor v poli ' + TAG + '.')
					if '2' in F and '7' in F:
						if F['2'] != 'czenas':
							write_error(header.identifier(), TAG, SIF, '099', 'Chybný 2.indikátor v poli ' + TAG + '.')
				if F.indicator2 == '4':
					if '7' in F:
						write_error(header.identifier(), TAG, SIF, '100', 'Chybný 2.indikátor v poli ' + TAG + '.')
					if '2' in F:
						if F['2'] == 'czenas':
							write_error(header.identifier(), TAG, SIF, '101', 'Chybný 2.indikátor v poli ' + TAG + '.')
	
		# TEST SPACE DOT / SPACE COMA TAG 2xx/5xx ------------------

		# TEST DATE / COUNTRY / LANG ------------------
		
		if '008' in metadata:
			DATE = metadata['008'].value()[7:15].strip()
			if metadata['008'].value()[6] not in ('s', 'e', 'm', 'q'):
				write_error(header.identifier(), '008', SIF, '102', 'Chybný kód data v poli 008.')
			if not re.match('^\d+$', DATE) or len(DATE) not in (4, 6, 8):
					write_error(header.identifier(), '008', SIF, '103', 'Neplatné datum v poli 008.')
			if metadata['008'].value()[6] in ('s', 'q'):
				if len(DATE) != 4:
					write_error(header.identifier(), '008', SIF, '104', 'Nesoulad mezi kódem data a datem (má být RRRR).')
			if metadata['008'].value()[6] == 'e':
				if len(DATE) not in (6, 8):
					write_error(header.identifier(), '008', SIF, '105', 'Nesoulad mezi kódem data a datem (má být RRRRMM/RRRRMMDD).')
			if metadata['008'].value()[6] == 'm':
				if len(DATE) != 8:
					write_error(header.identifier(), '008', SIF, '106', 'Nesoulad mezi kódem data a datem (není interval roků).')
		if metadata.leader[7] == 'm':
			if '008' in metadata:
				DATA = metadata['008'].value()[7:11]
				if '260' in metadata:
					if 'c' in metadata['260']:
						if DATA != metadata['260']['c'].strip('.'):
							write_error(header.identifier(), '008', SIF, '107', 'Nesoulad mezi daty v poli 008 a 260/264.')
				for F in metadata.get_fields('264'):
					if 'c' in F:
						if DATA != F['c'].strip('.'):
							write_error(header.identifier(), '008', SIF, '108', 'Nesoulad mezi daty v poli 008 a 260/264.')
		if metadata.leader[7] == 'b':
			if '008' in metadata:
				DATA1 = metadata['008'].value()[7:15].strip()
				for F in metadata.get_fields('773'):
					if '9' in F:
						DATA2 = re.sub('^(\d+).*$','\\1', F['9'])
						if DATA1[0:4] != DATA2 and DATA1[4:8] != DATA2 and DATA1 != DATA2:
							write_error(header.identifier(), '008', SIF, '109', 'Nesoulad mezi daty v poli 008 a 773-9.')
		if '008' in metadata:
			DATA = metadata['008'].value()[15:18].strip()
			if country_code:
				if DATA not in country_code:
					write_error(header.identifier(), '008', SIF, '110', 'Chybný kód země v poli 008.')
				if '044' in metadata:
					if DATA not in metadata['044'].subfields[1::2]:
						write_error(header.identifier(), '008', SIF, '111', 'Nesoulad mezi kódy zemí v poli 008 a 044.')
		if '008' in metadata:
			DATA = metadata['008'].value()[35:38].strip()
			if lang_code:
				if DATA not in lang_code:
					write_error(header.identifier(), '008', SIF, '112', 'Chybný kód jazyka v poli 008.')
				if '041' in metadata:
					if DATA not in metadata['041'].subfields[1::2]:
						write_error(header.identifier(), '008', SIF, '113', 'Nesoulad mezi kódy jazyků v poli 008 a 041.')
		if '041' in metadata:
			if lang_code:
				for DATA in metadata['041'].subfields[1::2]:
					if DATA not in lang_code:
						write_error(header.identifier(), '041', SIF, '114', 'Chybný kód jazyka v poli 041.')
			if len(metadata['041'].subfields) != 4:
					write_error(header.identifier(), '041', SIF, '115', 'Pole 041 musí obsahovat 2 a více podpolí.')
		if '044' in metadata:
			if country_code:
				for DATA in metadata['044'].subfields[1::2]:
					if DATA not in country_code:
						write_error(header.identifier(), '044', SIF, '116', 'Chybný kód země v poli 044.')
			if len(metadata['044'].subfields) != 4:
					write_error(header.identifier(), '044', SIF, '117', 'Pole 044 musí obsahovat 2 a více podpolí.')
		if '100' in metadata:
			if role_code:
				if '4' in metadata['100']:
					for DATA in metadata['100'].get_subfields('4'):
						if DATA not in role_code:
							write_error(header.identifier(), '100', SIF, '118', 'Chybný kód role v poli 100.')
			if 'j' in metadata['100']:
				if metadata['100']['j'] not in ('bbg', 'rej'):
					write_error(header.identifier(), '100', SIF, '119', 'Chybný kód role v podpoli 100j.')
				if '4' not in metadata['100'] or 'oth' not in metadata['100'].get_subfields('4'):
						write_error(header.identifier(), '100', SIF, '120', 'V poli 100 chybí podpole 4 s hodnotou "oth".')
		for F in metadata.get_fields('700'):
			if role_code:
				if '4' in F:
					for DATA in F.get_subfields('4'):
						if DATA not in role_code:
							write_error(header.identifier(), '700', SIF, '121', 'Chybný kód role v poli 700.')
			if 'j' in F:
				if F['j'] not in ('bbg', 'rej'):
					write_error(header.identifier(), '700', SIF, '122', 'Chybný kód role v podpoli 700j.')
				if '4' not in F or 'oth' not in F.get_subfields('4'):
						write_error(header.identifier(), '700', SIF, '123', 'V poli 700 chybí podpole 4 s hodnotou "oth".')
		if '100' in metadata:
			if '4' in metadata['100']:
				DATA = metadata['100'].get_subfields('4')
				if 'aut' not in DATA and 'ive' not in DATA:
					write_error(header.identifier(), '100', SIF, '124', 'V poli 100 chybí kód role "aut" nebo "ive".')

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

	COUNTER+=1

# EXIT -------------------

if args.check:
	log.write(HTML_FOOTER)
if args.notify:
	notify()

print('TOTAL ' + str(COUNTER))
print('MATCH ' + str(MATCH))

log.close()

