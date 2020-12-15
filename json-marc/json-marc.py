#!/usr/bin/python
# -*- coding: utf-8 -*-
#
# JSON to MARC convertor.
# 

# INCLUDE -----------------

from __future__ import print_function

import sqlite3,json,sys,os,re

from pymarc import Record,MARCWriter
from pymarc.field import Field

# VAR -----------------

IN='tmp/retrobi.json'
#IN='demo.json'

#OUT='retrobi.bib'
AUTLOG='log/aut.log'
BROKEN='log/broken.log'
DB='AUT.db'

LANG_MAP={
	'bul':u'bul',
	'chi':u'čín',
	'cze':u'češ',
	'dan':u'dán',
	'eng':[u'ang', u'angl', u'[angl'],
	'fin':u'fin',
	'fre':[u'fr', u'Fr', u'fra', u'fran', u'franc'],
	'ger':[u'něm', u'[něm'],
	'gre':[u'řeč', u'řec'],
	'hrv':[u'chor',u'chrv'],
	'hun':[u'maď', u'maďar', u'maďarš'],
	'ita':[u'vlaš', u'it', u'ital'],
	'jpn':u'jap',
	'lat':u'lat',
	'lot':u'lav',
	'nor':u'nor',
	'pol':u'pol',
	'por':u'port',
	'rum':u'rum',
	'rus':[u'rus', u'ruš' , u'[ruš', u'rušt'],
	'scr':u'chorv',
	'tur':u'tur'	
}

SLO_MAP=[
	u'Orol tatránski',
	u'Slovenské noviny',
	u'Slovenskje pohladi na vedi, umeňja a literatúru',
	u'Nitra',
	u'Hronka'
]

GER_MAP=[
	u'Abhandlungen der königlichen böhmischen Gesellschaft der Wissenschaften',
	u'Akademie',
	u'Archiv für österreichische Geschichte',
	u'Archiv für slavische Philologie',
	u'Beiträge zur Heimatkunde des Aussig-Karbitzer Bezirkes',
	u'Bild und Leben',
	u'Bohemia',
	u'Böhmen und Mähren',
	u'Brünner Zeitung',
	u'Camelien',
	u'Constitutionelle Allgemeine Zeitung von Böhmen',
	u'Constitutionelle Prager Zeitung',
	u'Constitutionelles Blatt aus Böhmen',
	u'Correspondenz',
	u'Čechische revue',
	u'Das literarische Echo',
	u'Das Vaterland',
	u'Der Ackermann aus Böhmen',
	u'Der Bote von der Egger',
	u'Der Freund des Volkes',
	u'Der Wegweiser',
	u'Deutsche Zeitung aus Böhmen',
	u'Deutsches Archiv für Geschichte des Mittelalters',
	u'Die Literatur',
	u'Die Waage für Freiheit, Recht und Wahrheit', 
	u'Erinnerungen an merkwürdige Gegenstände und Begebenheiten',
	u'Fort mit den Zöpfen!',
	u'Für Kalobiotik',
	u'Germanoslavica',
	u'Illustriertes Volksblatt für Böhmen',
	u'Jahrbücher für Kultur und Geschichte der Slaven',
	u'Kritische Blätter für Literatur und Kunst',
	u'Kunst und Wissenschaft',
	u'Länder',
	u'Leben der slavischen Völker',
	u'Libussa',
	u'Mittheilungen des Vereines für Geschichte der Deutschen in Böhmen',
	u'Monatschrift der Gesellschaft des vaterländischen Museums in Böhmen',
	u'Neue Litteratur',
	u'Neue Zeit',
	u'Oesterreichisches Morgenblatt',
	u'Olmützer Zeitschrift',
	u'Ost und West',
	u'Österreichischer Correspondent',
	u'Panorama',
	u'Panorama des Universums',
	u'Pilsner Anzeiger',
	u'Politik',
	u'Politik',
	u'Politisches Wochenblatt',
	u'Prag',
	u'Prager Abendblatt',
	u'Prager Bahnhof',
	u'Prager Presse',
	u'Prager Rundschau',
	u'Prager Tagblatt',
	u'Prager Zeitung',
	u'Slavische Centralblätter',
	u'Slavische Rundschau',
	u'Slowanka',
	u'Sonntagsblätter für heimatliche Interessen',
	u'Stadt und Land',
	u'Sudetendeutsche Zeitschrift für Volkskunde',
	u'Sudetendeutschen',
	u'Tagesbote aus Böhmen',
	u'Union',
	u'Unterhaltungsblätter',
	u'Volksblatt für Böhmen',
	u'Witiko',
	u'Wochenblätter für Freiheit und Gesetz',
	u'Zeitschrift des Deutschen Vereins für die Geschichte Mährens und Schlesiens',
	u'Zeitschrift für Geschichte und Kulturgeschichte Oesterreichisch-Schlesiens',
	u'Zeitschrift für Slavische Philologie',
	u'Zeitschrift für sudetendeutsche Geschichte'
]

#tar=tarfile.open(name=OUT + '.tar.gz', fileobj=buff, mode='w:gz')
#info = tarfile.TarInfo(name=OUT)

# DEF -----------------

def get_lang(lang):
	for l in LANG_MAP:
		if lang in LANG_MAP[l]:
			return l
	return lang	

def get_mdt(tag, name, ident, autlog, rec):
	tag_list=['a','b','c','d','q','7']
	idn = (ident,)
	ret=[]
	cur.execute("SELECT a,b,c,d,q,seven FROM t WHERE seven = ?", idn)
	data = cur.fetchone()

	if not data:
		autlog.write(tag + ': No AUT data. ' + rec + ' ' + ident + '\n')
		return ret
	elif name.rstrip(',') != data[0].rstrip(','):
		autlog.write(
			tag +
			': AUT name do not match. ' +
			str(rec) + ' | ' +
			str(ident) +
			' | ' + name.rstrip(',').encode('utf-8') +
			' | ' + data[0].rstrip(',').encode('utf-8') +
			'\n'
		)
		return ret
	else:
		for i in range(0,6):
			if data[i]:
				ret.append(tag_list[i])
				ret.append(data[i])
	return ret

def find(path,json):
	node = path.split('/')
	# root
	if node and node[0] not in json: return ''
	root = json[node[0]]
	# leave
	for leaf in node[1:]:
		if leaf in root:
			root = root[leaf][0]
		else:
			return ''
	return root

def segment_dot_triple_dot(TITLE,record):
	TEXT = re.findall('(?<=:).*(?=\.\.\.)', TITLE)
	PART = TEXT[0].split('. ')
	record['245']['a'] = PART[0] + ' :'
	if BRACE in [u'báseň', u'Báseň']:
		record['245'].add_subfield('b', '[' + PART[1] + ']' + ' /', 1)# a :b /c
	else:
		record['245'].add_subfield('b', PART[1] + ' /', 1)# a :b /c
	if TITLE.split('...')[1]:
		if 'c' in record['245']:
			record['245']['c'] = TITLE.split(':')[0] + ' ;' + TITLE.split('...')[1]
		else:
			record['245'].add_subfield('c', TITLE.split(':')[0] + ' ;' + TITLE.split('...')[1])
	else:
		if 'c' in record['245']:
			record['245']['c'] = TITLE.split(':')[0]
		else:
			record['245'].add_subfield('c', TITLE.split(':')[0])
	#lang + trans
	if re.findall(u'[Pp]řel\. \[[Zz] .+\.\] .+|\[[Zz] .+\.\] [Pp]řel\. .+', TITLE):
		LANG = re.findall(u'(?<=[Pp]řel\. \[[Zz] ).+(?=\.\] .+)', TITLE)
		if not LANG:
			LANG = re.findall(u'(?<=\[[Zz] ).+(?=\.\] [Pp]řel\. .+)', TITLE)
		if LANG:
			record.add_ordered_field(Field(tag='041', indicators=['1', '\\'], subfields=['a','cze', 'h', get_lang(LANG[0])]))
			TRNS = re.findall(u'(?<=[Pp]řel\. \[[Zz] ' + LANG[0] + u'\.\] ).+', TITLE)
			if not TRNS:
				TRNS = re.findall(u'(?<=\[[Zz] ' + LANG[0] + u'\.\] [Pp]řel\. ).+', TITLE)
			if TRNS:
				record.add_ordered_field(Field(tag='700', indicators=['1', '\\'], subfields=['a', TRNS[0]]))

def segment_triple_dot(TITLE,record):
	TEXT = re.findall('(?<=:).*(?=\.\.\.)', TITLE)
	record['245']['a'] = TEXT[0]
	if TITLE.split('...')[1]:
		if 'c' in record['245']:
			record['245']['c'] = TITLE.split(':')[0] + ' ;' + TITLE.split('...')[1]
		else:
			record['245'].add_subfield('c', TITLE.split(':')[0] + ' ;' + TITLE.split('...')[1])
	else:
		if 'c' in record['245']:
			record['245']['c'] = TITLE.split(':')[0]
		else:
			record['245'].add_subfield('c', TITLE.split(':')[0])
	#lang + trans
	if re.findall(u'[Pp]řel\. \[[Zz] .+\.\] .+|\[[Zz] .+\.\] [Pp]řel\. .+', TITLE):
		LANG = re.findall(u'(?<=[Pp]řel\. \[[Zz] ).+(?=\.\] .+)', TITLE)
		if not LANG: LANG = re.findall(u'(?<=\[[Zz] ).+(?=\.\] [Pp]řel\. .+)', TITLE)
		if LANG:
			record.add_ordered_field(Field(tag='041', indicators=['1', '\\'], subfields=['a','cze', 'h', get_lang(LANG[0])]))
			TRNS = re.findall(u'(?<=[Pp]řel\. \[[Zz] ' + LANG[0] + u'\.\] ).+', TITLE)
			if not TRNS: TRNS = re.findall(u'(?<=\[[Zz] ' + LANG[0] + u'\.\] [Pp]řel\. ).+', TITLE)
			if TRNS:
				record.add_ordered_field(Field(tag='700', indicators=['1', '\\'], subfields=['a', TRNS[0]]))

def segment_recursion(TITLE,record):
	T = TITLE
	# frist colon
	COLON = re.findall('(^[^:]+):.*', T)
	if COLON and ';' not in TITLE:
		if 'c' in record['245']:
			record['245']['c'] =  COLON[0]
		else:
			record['245'].add_subfield('c', COLON[0])
		T = T.replace(COLON[0] + ': ', '')
		# first dot
		DOT = re.findall('(^[^\.]+[^A-Z0-9])\..*', T)
		if DOT:
			record['245']['a'] = DOT[0] + ' /'
			T = T.replace(DOT[0] + '.', '')
			# 245
			if T: record['245']['c'] = record['245']['c'] + ' ; ' + T
			# lang
			LANG = re.findall(u'(?<=\[[Zz] ).*?(?=\.\] [Pp]řel\.)|(?<=[Pp]řel\. \[[Zz] ).*?(?=\.])', T)
			if LANG:
				record.add_ordered_field(Field(tag='041', indicators=['1', '\\'], subfields=['a','cze', 'h', get_lang(LANG[0])]))
				T = T.replace('[Z ' + LANG[0] + '.]', '').replace('[z ' + LANG[0] + '.]', '')
				# trans
				TRNS = re.findall(u'(?<=[Pp]řel\. ).*', T.strip())
				if TRNS:
					record.add_ordered_field(Field(tag='700', indicators=['1', '\\'], subfields=['a', TRNS[0]]))
		# TIZ
		if T != TITLE:
			record.add_ordered_field(Field(tag='TIZ', indicators=['\\', '\\'], subfields=['a', T]))

# INIT -----------------

try:
	con = sqlite3.connect(DB)
	cur = con.cursor()
except:
	print("SQLite3 connection failed.")
	sys.exit(1)

autlog = open(AUTLOG, 'w')
broken = open(BROKEN, 'w')

#bib = open(OUT,'w')
writer = MARCWriter(open('retrobi.mrc','wb'))

# MAIN -----------------

with open(IN, 'rb') as f:
	for LINE in f:

		# INIT -----------------

		#record = Record(force_utf8=True)
		record = Record()
	
		record.leader='     nab a22     4a 4500'# overwrite internal(pymarc.record) LDR tag
		record.add_ordered_field(Field(tag='FMT', data='RS'))
		record.add_ordered_field(Field(tag='003', data='CZ-PrUCL'))
		record.add_ordered_field(Field(tag='005', data='20201231'))
		record.add_ordered_field(Field(tag='040', indicators=['\\','\\'], subfields=['a', 'ABB060','b', 'cze']))
		#record.add_ordered_field(Field(tag='041', indicators=['0','\\'], subfields=['a', 'cze']))
		record.add_ordered_field(Field(tag='336', indicators=['\\','\\'], subfields=['a', 'text', 'b', 'txt', '2', 'rdacontent']))
		record.add_ordered_field(Field(tag='337', indicators=['\\','\\'], subfields=['a', u'bez média', 'b', 'n', '2', 'rdamedia']))
		record.add_ordered_field(Field(tag='338', indicators=['\\','\\'], subfields=['a', u'jiný', 'b', 'nz', '2', 'rdacarrier']))
		record.add_ordered_field(Field(tag='500', indicators=['\\','\\'], subfields=['a', u'Strojově převedený záznam z RETROBI bez redakční kontroly.']))
		record.add_ordered_field(Field(tag='910', indicators=['\\','\\'], subfields=['a', 'ABB060']))
		record.add_ordered_field(Field(tag='964', indicators=['\\','\\'], subfields=['a', 'RETROBI']))
		record.add_ordered_field(Field(tag='OWN', indicators=['\\','\\'], subfields=['a', 'UCLA']))
		record.add_ordered_field(Field(tag='SIF', indicators=['\\','\\'], subfields=['a', 'RET']))

		# PARSE -----------------
		try:
			jsn = json.loads(LINE.strip().rstrip(','), strict=False)
		except:
			print("Broken JSON.")# skip broken line

		# skip segmented for now
		#if find('state', jsn) != 'FRESH': continue
	
		#print(json.dumps(jsn))
		#print(json.dumps(jsn, indent=2))

		# 001
		record.add_ordered_field(Field(tag='001', data='RET-' + find('_id',jsn)))
		# 008
		LANG='cze'
		DATA='19600101'
		YEAR = find('tree/bibliograficka_cast/zdroj/rok', jsn)
		NAME = find('tree/bibliograficka_cast/zdroj/nazev', jsn)
		if len(YEAR) == 4:
			DATA+='s' + YEAR
		else:
			DATA+='n    '
		DATA+='    xr            ||| ||'
		if NAME in SLO_MAP: LANG='slo'
		if NAME in GER_MAP: LANG='ger'
		DATA+=LANG + ' d'
		record.add_ordered_field(Field(tag='008', data=DATA))
		# 100
		NAME = find('tree/nazvova_cast/autor/jmeno', jsn)
		IDENT =find('tree/nazvova_cast/autor/id', jsn)
		if NAME and IDENT:
			MDT = get_mdt('100', NAME, IDENT, autlog, find('_id', jsn))
			if MDT:
				MDT.append('4')
				MDT.append('aut')
				record.add_ordered_field(Field(tag='100', indicators=['1','\\'], subfields=MDT))
		# 245
		NAME = re.sub('(.*), (.*)', '\\2 \\1', find('tree/nazvova_cast/autor/jmeno', jsn))
		if NAME:
			record.add_ordered_field(Field(tag='245', indicators=['1','0'], subfields=['a', u'[Název textu k dispozici na připojeném lístku]', 'c', NAME]))
		else:
			record.add_ordered_field(Field(tag='245', indicators=['1','0'], subfields=['a', u'[Název textu k dispozici na připojeném lístku]']))
		# 520
		ANOT= find('tree/anotacni_cast/anotace', jsn).rstrip('|')
		if find('segment_annotation', jsn): ANOT = find('segment_annotation', jsn).rstrip('|')
		if ANOT:
			ANOT = re.sub('^\[(.*)\]$', '\\1', ANOT)# remove bracers
			if not re.match('.*\.$', ANOT): ANOT = ANOT + '.'# trailing dot
			record.add_ordered_field(Field(tag='520', indicators=['2','\\'], subfields=['a', ANOT]))
		# 600
		NAME = find('tree/anotacni_cast/odkazovana_osoba/jmeno', jsn)
		IDENT = find('tree/anotacni_cast/odkazovana_osoba/id', jsn)
		if NAME and IDENT:
			MDT = get_mdt('600', NAME, IDENT, autlog, find('_id', jsn))
			if MDT:
				MDT.append('2')
				MDT.append('czenas')
				record.add_ordered_field(Field(tag='600', indicators=['1','7'], subfields=MDT))
			else:
				record.add_ordered_field(Field(tag='600', indicators=['1','7'], subfields=['a', NAME, '7 ', IDENT]))
		# 655
		CHAR = find('tree/nazvova_cast/charakteristika', jsn)
		if CHAR:
			record.add_ordered_field(Field(tag='655', indicators=['\\','7'], subfields=['a', CHAR]))

		# 773
		NAME = find('tree/bibliograficka_cast/zdroj/nazev', jsn)
		YEAR = find('tree/bibliograficka_cast/zdroj/rok', jsn)
		if find('segment_bibliography', jsn):
			SRC = re.sub('^([^,0-9]+).*', '\\1', find('segment_bibliography', jsn).replace('In:', ''))
			SRC = SRC.rstrip('.[').strip()
			DATE = find('segment_bibliography', jsn).replace('In:', '').replace(SRC, '').strip().rstrip('|')
			# 'str' -> 's'
			DATE = re.sub('str[,.](?= [0-9])', 's.', DATE)
			# page
			PAGE = re.findall(' s\. [^,]+,', DATE)
			if PAGE and len(PAGE) == 1:
				DATE = DATE.replace(PAGE[0], '')
				DATE = DATE + ',' + PAGE[0].strip(',')
			# trailing dot year/page
			#DATE = re.sub('(?<=\d{3})\.$', '', DATE)# trailing dot
			#DATE = DATE.rstrip('.')
			if LANG == 'ger':
				record.add_ordered_field(Field(tag='773', indicators=['0','\\'], subfields=['t', SRC, 'g', 'Jg. ' + DATE, '9', YEAR]))
			else:
				record.add_ordered_field(Field(tag='773', indicators=['0','\\'], subfields=['t', SRC, 'g', u'Roč. ' + DATE, '9', YEAR]))
		else:
			if NAME and YEAR:
				record.add_ordered_field(Field(tag='773', indicators=['0','\\'], subfields=['t', NAME, 'g', 'R. ' + YEAR, '9', YEAR]))
			if NAME and not YEAR:
				record.add_ordered_field(Field(tag='773', indicators=['0','\\'], subfields=['t', NAME]))
			if not NAME and YEAR:
				record.add_ordered_field(Field(tag='773', indicators=['0','\\'], subfields=['g','R. ' + YEAR, '9', YEAR]))

		# 856
		LINK = 'http://retrobi.ucl.cas.cz/retrobi/katalog/listek/' + find('_id', jsn)
		record.add_ordered_field(Field(tag='856', indicators=['4','0'], subfields=['u', LINK, 'y', u'původní lístek v RETROBI', '4', 'N']))
		# SIR
		if find('segment_excerpter', jsn):
			record.add_ordered_field(Field(tag='SIR', indicators=['\\', '\\'], subfields=['a', find('segment_excerpter', jsn)]))
		# 245 / TIT / TIZ
		TITLE = find('segment_title', jsn)
		if TITLE:
			TITLE = TITLE.strip().rstrip('|')
			# last square bracet
			BRACE = re.findall('(?<= \[)(?<!=)[^\[\]]+(?=\]$)', TITLE)
			if BRACE:
				if '655' not in record:
					record.add_ordered_field(Field(tag='655', indicators=['\\', '4'], subfields=['a', BRACE[0]]))
				else:
					if 'a' not in record['655']:
						record['655'].add_subfield('a', BRACE[0], 0)
						record['655'].indicator2 = '4'
				TITLE = TITLE.replace(' [' + BRACE[0] + ']', '')
				# dot triple dot
				if re.match('^[^:]+: [^\.]+[^A-Z0-9]\. [^\.\]\[]+\.\.\.(?!\]\))( .+)?$',TITLE) and ';' not in TITLE:
					segment_dot_triple_dot(TITLE,record)
				# triple dot
				elif re.match('^[^:]+: [^\.\]\[]+\.\.\.(?![\]\)])( .+)?$', TITLE) and ';' not in TITLE:
					if len(re.findall('\.\.\.', TITLE)) == 1:
						segment_triple_dot(TITLE,record)
				# no dot
				elif re.match('^[^:]+: [^\.\]\[]+$', TITLE) and ';' not in TITLE:
					record['245']['a'] = TITLE.split(': ')[1] + ' /'
					if 'c' in record['245']:
						record['245']['c'] = TITLE.split(':')[0]
					else:
						record['245'].add_subfield('c', TITLE.split(':')[0])
				# default dynamic
				else:
					segment_recursion(TITLE,record)
			# TIT
			record.add_ordered_field(Field(tag='TIT', indicators=['\\', '\\'], subfields=['a', TITLE]))

		# TXT
		OCRF = find('ocr_fix', jsn).replace('\n', ' ')
		OCR = find('ocr', jsn).replace('\n', ' ')
		if OCRF:
			record.add_ordered_field(Field(tag='TXT', indicators=['\\', '\\'], subfields=['a', OCRF]))
		elif OCR:
			record.add_ordered_field(Field(tag='TXT', indicators=['\\', '\\'], subfields=['a', OCR]))

		# FIX -----------------
	
		# 100a -> 245c
		if '100' in record and '245' in record:
			if 'a' in record['100'] and 'c' in record['245']:
				name1 = re.findall('\[=([^\[\]]+)]', record['245']['c'])
				name2 = [n.strip(',') for n in record['100']['a'].split(' ')]
				if  len(name1) == 1 and name2:
					if name2[0] in name1[0]:
						record['245']['c'] = record['245']['c'].replace(name1[0], ', '.join(name2))

		# 520a -> 787w
		if '520' in record and 'a' in record['520']:
			REF=False
			if re.match('^Rf\.?: ', record['520']['a']): REF=True
			if '655' in record and 'a' in record['655']:
				if re.match('^ ?[Rr]ef', record['655']['a']): REF=True
			if REF:
				record.add_ordered_field(Field(tag='787', indicators=['0','8'], subfields=['w', record['520']['a'].replace('Rf:','').replace('Rf.: ','')]))
				record['520']['a'] = u'Referát.'

		# 700 [=] -> 700
		if '700' in record and 'a'  in record['700']:
			REC = record['700']['a'].strip()
			if not re.findall('\[|\]', REC):
				if re.match('^([^ ]+\.? ){1,2}[^ .]+$', REC):
					record['700']['a'] = REC.split(' ')[-1:][0] + ', ' + ' '.join(REC.split(' ')[:-1])
			if len(re.findall('\[=|\]', REC)) == 2:
				NAME = re.findall('(?<=\[=).+(?=\])', REC)
				if NAME:
					REC = NAME[0].strip()
					if re.match('^([^ ,]+\.? ){1,2}[^ .?]+$', REC):
						record['700']['a'] = REC.split(' ')[-1:][0] + ', ' + ' '.join(REC.split(' ')[:-1])
					elif re.match('^[^ .,]+ [^ .?]+$', REC):
						record['700']['a'] = REC.split(' ')[1] + ', ' + REC.split(' ')[0]
					elif re.match('^.+, .+$', REC):
						record['700']['a'] = REC
		# 773t "LN"
		if '773' in record and 't' in record['773']:
			if record['773']['t'] == 'LN':
				record['773']['t'] = u'Lidové noviny'

		# 773g
		if '773' in record and 'g' in record['773']:
			for P in re.findall('\d+-\d+(?=[.,]|$)', record['773']['g']):
				N1 = P.split('-')[0]
				N2 = P.split('-')[1]
				if len(N1) != len(N2) and int(N1) > int(N2):
					record['773']['g'] = record['773']['g'].replace(P, N1 + '-' + N1[:-len(N2)]  + N2)

		# WRITE -----------------

		# write MARC21 binary
		writer.write(record)
		continue

		# write Aleph

		# leader
		bib.write('=LDR  ' + record.leader.encode('utf-8')+ '\n')

		for F in record:
			try:
				IND=''
				if F.indicator1: IND += F.indicator1
				else: IND += "\\"
				if F.indicator2: IND += F.indicator2
				else: IND += "\\"
			except:
				 IND = "\\\\"
			try:
				VAL=[]
				if F.subfields:
					for sub in F:
						VAL.append(''.join(sub).strip())
        				DATA = '$' + '$'.join(VAL)
				else: DATA = ''
			except: DATA = F.value()

			if F.tag == 'FMT': DATA = 'RS'
			if F.tag in ['FMT', '001', '003', '005', '008']:
				bib.write('=' + str(F.tag) + '  ' + DATA.encode('utf-8')+ '\n')
			else:
				bib.write('=' + str(F.tag) + '  ' + str(IND) + DATA.encode('utf-8') + '\n')
		bib.write('\n')

# EXIT -------------------

broken.close()
autlog.close()
#bib.close()
con.close()

writer.close()

