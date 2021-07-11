#!/usr/bin/python3
#
# Update database from OAI-PMH 2.0
#
#from datetime import datetime,date,timedelta

import argparse,sqlite3,time,sys,io,re

from pymarc import marcxml,Field
from datetime import datetime
from lxml.etree import tostring
from oaipmh.client import Client
from oaipmh.metadata import MetadataRegistry

OAI_URL='https://aleph.lib.cas.cz/OAI'
OAI_SET='UCLA'

DB='vufind.db'

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

def name_to_upper(s,ID):
	if len(s.split(',')) == 2:
		return s.split(',')[0].strip().upper() + ', ' + s.split(',')[1].strip() 
	else:
		if '[=' in s: print(ID, '#Multiple comma.')
	return s

def word_to_upper(s):
	word,buff = '',''
	for char in s:
		if not word and re.match('\w', char, re.UNICODE):
			char = char.upper()
			word=True
		buff += char 
	return buff

def word_to_lower(s):
	word,buff = '',''
	for char in s:
		if not word and re.match('\w', char, re.UNICODE):
			char = char.lower()
			word=True
		buff += char 
	return buff

# ARGS -------------------

parser = argparse.ArgumentParser(description="OAI PMH 2.0 MARCXML Validator.")
parser.add_argument('--from', help='Records from. [YYYY-mm-dd HH:MM:SS]', type=valid_date, dest='from_date')
parser.add_argument('--until', help='Records until. [YYYY-mm-dd HH:MM:SS]', type=valid_date, dest='until_date')
args = parser.parse_args()

if not args.from_date: parser.error('argument -from is required.')
if not args.until_date: parser.error('argument --until is required.')

# OAI -------------------

registry = MetadataRegistry()
registry.registerReader('marc21', MarcXML)

oai = Client(OAI_URL, registry)

records = oai.listRecords(metadataPrefix='marc21', set=OAI_SET, from_=args.from_date, until=args.until_date)

# MAIN -------------------

BATCH=[]

for record in records:

	header = record[0]
	metadata = record[1]

	# ident
	ident = metadata['001'].value()

	# skip deleted records
	if header.isDeleted(): continue

	# LDR dup
	metadata.remove_fields('LDR')# Leader duplicate (Aleph)

	# Drop SYS
	metadata.remove_fields('SYS')# Invalid control field (Aleph)

	# Webarchiv
	for F in metadata.get_fields('856'):
		if 'y' in F:
			if F['y'] == 'Weabrchiv': F['y'] = 'Webarchiv'
			if F['y'] == 'WebArchiv': F['y'] = 'Webarchiv'
			if F['y'] == 'Webarhciv': F['y'] = 'Webarchiv'
			if F['y'] == 'Wenarchiv': F['y'] = 'Webarchiv'

	# Drop X
	X=False
	for F in metadata.get_fields('773'):
		if X and 'x' in F: F.delete_subfield('x')# drop more than one
		if 'x' in F: X = True# catch the first one

	# MBK
	MBK=False
	if '044' in metadata:
		if 'a' in metadata['044']:
			if metadata['044']['a'] != 'xr': MBK=True
	if '008' in metadata:
		if metadata['008'].value()[15:17] != 'xr': MBK=True
	for F in metadata.get_fields('964'):
		if 'CLE' in F.value(): MBK=False
	if MBK:
		metadata.add_ordered_field(Field(tag='964', indicators=[' ',' '], subfields=['a', 'MBK']))

	# 953 remove << >>
	for F in metadata.get_fields('100', '600', '700'):
		LHT = False
		SUB = F.subfields
		for i in range(0, len(SUB)):
			if re.findall('<<[^<]*>>', SUB[i]):
				SUB[i] = re.sub('<<([^<]*)>>', '\\1', SUB[i])
				LHT = True
		if LHT:
			F.subfields = SUB

	if '245' in metadata and 'c' in metadata['245']:
		LHT = re.findall('<<[^<]*>>', metadata['245']['c'])
		if LHT:
			metadata['245']['c'] = re.sub('<<([^<]*)>>', '\\1', metadata['245']['c'])	
		for LH in LHT:
			SUB = re.findall('<<([^>[]+)', LH)
			if SUB:
				metadata.add_ordered_field(Field(tag='593', indicators=[' ',' '],
					subfields=['a', SUB[0].strip()]
				))

	# 592
	if '630' in metadata and 'a' in metadata['630']:
		metadata.add_ordered_field(Field(tag='592', indicators=[' ',' '],
			subfields=['a', metadata['630']['a']]
		))

	for F in metadata.get_fields('787'):
		if 't' in F and '4' in F:
			if 'a' in F:
				metadata.add_ordered_field(Field(tag='592', indicators=[' ',' '],
					subfields=['a', F['a'] + ': ' + F['t'], 'b', F['4']]
				))
			else:
				metadata.add_ordered_field(Field(tag='592', indicators=[' ',' '],
					subfields=['a', F['t'], 'b', F['4']]
				))
	# Citace

	BROKEN=False
	
	#BASE
	CNT=0
	NAME,DATA,CTEXT='','',''
	if '100' in metadata and 'a' in metadata['100']:
		if len(metadata['100']['a'].rstrip(',').split(',')) == 2:
			NAME = name_to_upper(metadata['100']['a'].rstrip(','), ident)
		else:
			NAME = metadata['100']['a']
		CNT+=1
	for F in metadata.get_fields('700'):
		if '4' in F and F['4'] == 'aut':
			if not NAME:
				NAME = name_to_upper(F['a'].rstrip(','), ident)
			else:
				NAME += u' – ' + name_to_upper(F['a'].rstrip(','), ident)
			CNT+=1
			if CNT == 3:
				NAME += ' et al.'
				break
		else:
			continue
	# NAME 100/700
	#if len(metadata.get_fields('100')) == 1 and '700' not in metadata and '710' not in metadata:
	#	if '245' in metadata and 'c' in metadata['245']:
	#		if '[=' in metadata['245']['c']:
	#			if re.findall('\[=(.+?)\]', metadata['245']['c']):
	#				LOW = re.findall('\[=(.+?)\]', metadata['245']['c'].strip())[0]
	#				NAME =  metadata['245']['c'].replace(LOW, name_to_upper(LOW, ident)).strip('. ')
	# 245
	if '245' in metadata:
		A = [sub for sub in metadata['245'].get_subfields('a')]
		B = [sub for sub in metadata['245'].get_subfields('b') if not re.match('^\[.*\]$', sub.strip('./ '))]
		NP = [sub for sub in metadata['245'].get_subfields('n','p')]

		DATA = re.sub('(?<![.])([.,:;/])(?![ .])' ,'\\1 ', ''.join(A + B + NP).strip('.:/ ')) + '. '
		
		if NAME: DATA = ': ' + DATA
		# 245c
		if 'c' in metadata['245']:
			if ';' in metadata['245']['c']:
				CTEXT =	re.sub('.*;(.*)', '\\1', metadata['245']['c']).strip('. ') + '. '
				# capitalize
				CTEXT = word_to_upper(CTEXT)
				# remove 245c page ref.
				CTEXT = re.sub(' \(s\.[^)]+\)', '', CTEXT)

	DATA = DATA.replace(' - ', u' – ')
	CTEXT = CTEXT.replace(' - ', u' – ')

	# 100a - 700a: 245abnp. 245c. 773t. 773g.
	TTEXT,GTEXT,URL = '','',''
	if metadata.leader[7] == 'b':
		# 773
		if '773' in metadata:
			# 773t (first..)
			if 't' in metadata['773']:
				TTEXT = metadata['773']['t'].strip() + '. '
				# URL
				if '[online]' in metadata['773']['t']:
					for F in metadata.get_fields('856'):
						if 'u' in F and 'y' in F and 'online' in F['y']:
							URL = ' URL: ' + F['u']
			# 773g (first..)
			if 'g' in metadata['773']:
				GTEXT = metadata['773']['g'].rstrip('.').strip() + '.'

		TTEXT = TTEXT.replace(' - ', u' – ')
		GTEXT = GTEXT.replace('-', u'–')

		BUFF = NAME + DATA + CTEXT + TTEXT + GTEXT + URL
		metadata.add_ordered_field(Field(tag='524', indicators=[' ',' '], subfields=['a', BUFF.replace('....', '...')]))

	# 100a - 700a: 245abnp. 245c. In: 773a: 773t. 773n. 773d, 773g.
	ATEXT,TTEXT,NTEXT,DTEXT,GTEXT,URL = '','','','','',''
	if metadata.leader[7] == 'a':
		# 773
		if '773' in metadata:
			# 100a - 700a: 245abnp. 245c. In: 787a: 773t. 787n. 787d, 773g.
			if 'a' not in metadata['773'] and 'd' not in metadata['773']:
				if 't' in metadata['773']:
					if '787' in metadata:
						MATCH=False
						for F in metadata.get_fields('787'):
							if 't' in F:
								if metadata['773']['t'][:10] in F['t']:
									MATCH=True
									# 787a (first..)
									if 'a' in F:
										ATEXT = F['a'].strip() + ': '
									# 773t (first..)
									if 't' in metadata['773']:
										TTEXT = metadata['773']['t'].strip() + '. '
										# URL
										if '[online]' in metadata['773']['t']:
											for U in metadata.get_fields('856'):
												if 'u' in U and 'y' in U and 'online' in U['y']:
													URL = ' URL: ' + U['u']
									# 787n (first..)
									if 'n' in F:
										NTEXT = word_to_upper(F['n']).strip() + '. '
									# 787d (first..)
									if 'd' in F:
										DTEXT = F['d'].strip() + ', '
									# 773g (first..)
									if 'g' in metadata['773']:
										GTEXT = metadata['773']['g'].rstrip('.').replace('-', u'–').strip() + '.'
										GTEXT = word_to_lower(GTEXT)
							else:
								BROKEN=True
						if not MATCH:
							BROKEN=True
					else:
						BROKEN=True
				else:
					BROKEN=True
			else:
				# 773a (first..)
				if 'a' in metadata['773']:
					ATEXT = metadata['773']['a'].strip() + ': '
				# 773t (first..)
				if 't' in metadata['773']:
					TTEXT = metadata['773']['t'].strip() + '. '
					# URL
					if '[online]' in metadata['773']['t']:
						for U in metadata.get_fields('856'):
							if 'u' in U and 'y' in U and 'online' in U['y']:
								URL = ' URL: ' + U['u']
				# 773n (first..)
				if 'n' in metadata['773']:
					NTEXT = word_to_upper(metadata['773']['n']).strip() + '. '
				# 773d (first..)
				if 'd' in metadata['773']:
					DTEXT = metadata['773']['d'].strip() + ', '
				# 773g (first..)
				if 'g' in metadata['773']:
					GTEXT = metadata['773']['g'].rstrip('.').replace('-', u'–').strip() + '.'
					GTEXT = word_to_lower(GTEXT)

		# 100a - 700a: 245abnp. 245c. In: 787a: 773t. 773n. 773d, 773g.
		TTEXT = TTEXT.replace(' - ', u' – ')
		GTEXT = GTEXT.replace('-', u'–')

		BUFF = NAME + DATA + CTEXT + 'In: ' + ATEXT + TTEXT + NTEXT + DTEXT + GTEXT + URL

		if not BROKEN:
			metadata.add_ordered_field(Field(tag='524', indicators=[' ',' '], subfields=['a', BUFF.replace('....', '...')]))

	# 100a - 700a: 245abnp. 245c. 260abc[264abc].
	ABC = ''
	if metadata.leader[7] == 'm':
		# ABC
		if '260' in metadata:
			ABC = ''.join([f.rstrip(' /') for f in metadata['260'].get_subfields('a','b','c')])
		if '264' in metadata:
			ABC = ''.join([f.rstrip(' /') for f in metadata['264'].get_subfields('a','b','c')])

		ABC = re.sub('(?<![.])([.,:;/])(?![ .])' ,'\\1 ', ABC).strip('. ') + '.'
		ABC = ABC.replace(' - ', u' – ')

		BUFF = NAME + DATA + CTEXT + ABC
		metadata.add_ordered_field(Field(tag='524', indicators=[' ',' '], subfields=['a', BUFF.replace('....', '...')]))

	# Write
	ident = metadata['001'].value()
	timestamp = int(time.mktime(time.strptime(metadata['005'].value(), '%Y%m%d%H%M%S.%f')))
	json = metadata.as_json()
	xml = marcxml.record_to_xml(metadata)
	marc = metadata.as_marc()

	BATCH.append((ident, timestamp, json, xml, marc))

con = sqlite3.connect(DB)
cur = con.cursor()
cur.executemany("INSERT INTO record (ident, timestamp, json, xml, marc) VALUES (?,?,?,?,?) ON CONFLICT (ident) DO UPDATE SET timestamp=excluded.timestamp, json=excluded.json, xml=excluded.xml, marc=excluded.marc;", BATCH)
#cur.execute("DELETE FROM record WHERE timestamp < (?);",(datetime.today()-timedelta(days=1).strftime('%y%m'),))
con.commit()
con.close()

sys.exit(0)

