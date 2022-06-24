#!/usr/bin/python3
#
# JSON to MARC convertor.
# 

# INCLUDE -----------------

import sqlite3,json,sys,os,re

from pymarc import Record,MARCWriter
from pymarc.field import Field

# VAR -----------------

IN='retrobi_dump.json'
OUT='retrobi.mrc'

PATCH='AUT_cmp_match.txt'
DB='AUT.db'

AUTLOG='aut.log'
#BROKEN='broken.log'

LANG_MAP={
	'bul':'bul',
	'chi':'čín',
	'cze':'češ',
	'dan':'dán',
	'eng':['ang', 'angl', '[angl'],
	'fin':'fin',
	'fre':['fr', 'Fr', 'fra', 'fran', 'franc'],
	'ger':['něm', '[něm'],
	'gre':['řeč', 'řec'],
	'hrv':['chor','chrv'],
	'hun':['maď', 'maďar', 'maďarš'],
	'ita':['vlaš', 'it', 'ital'],
	'jpn':'jap',
	'lat':'lat',
	'lot':'lav',
	'nor':'nor',
	'pol':'pol',
	'por':'port',
	'rum':'rum',
	'rus':['rus', 'ruš' , '[ruš', 'rušt'],
	'scr':'chorv',
	'tur':'tur'	
}

SLO_MAP=[
	'Orol tatránski',
	'Slovenské noviny',
	'Slovenskje pohladi na vedi, umeňja a literatúru',
	'Nitra',
	'Hronka'
]

GER_MAP=[
	'Abhandlungen der königlichen böhmischen Gesellschaft der Wissenschaften',
	'Akademie',
	'Archiv für österreichische Geschichte',
	'Archiv für slavische Philologie',
	'Beiträge zur Heimatkunde des Aussig-Karbitzer Bezirkes',
	'Bild und Leben',
	'Bohemia',
	'Böhmen und Mähren',
	'Brünner Zeitung',
	'Camelien',
	'Constitutionelle Allgemeine Zeitung von Böhmen',
	'Constitutionelle Prager Zeitung',
	'Constitutionelles Blatt aus Böhmen',
	'Correspondenz',
	'Čechische revue',
	'Das literarische Echo',
	'Das Vaterland',
	'Der Ackermann aus Böhmen',
	'Der Bote von der Egger',
	'Der Freund des Volkes',
	'Der Wegweiser',
	'Deutsche Zeitung aus Böhmen',
	'Deutsches Archiv für Geschichte des Mittelalters',
	'Die Literatur',
	'Die Waage für Freiheit, Recht und Wahrheit', 
	'Erinnerungen an merkwürdige Gegenstände und Begebenheiten',
	'Fort mit den Zöpfen!',
	'Für Kalobiotik',
	'Germanoslavica',
	'Illustriertes Volksblatt für Böhmen',
	'Jahrbücher für Kultur und Geschichte der Slaven',
	'Kritische Blätter für Literatur und Kunst',
	'Kunst und Wissenschaft',
	'Länder',
	'Leben der slavischen Völker',
	'Libussa',
	'Mittheilungen des Vereines für Geschichte der Deutschen in Böhmen',
	'Monatschrift der Gesellschaft des vaterländischen Museums in Böhmen',
	'Neue Litteratur',
	'Neue Zeit',
	'Oesterreichisches Morgenblatt',
	'Olmützer Zeitschrift',
	'Ost und West',
	'Österreichischer Correspondent',
	'Panorama',
	'Panorama des Universums',
	'Pilsner Anzeiger',
	'Politik',
	'Politik',
	'Politisches Wochenblatt',
	'Prag',
	'Prager Abendblatt',
	'Prager Bahnhof',
	'Prager Presse',
	'Prager Rundschau',
	'Prager Tagblatt',
	'Prager Zeitung',
	'Slavische Centralblätter',
	'Slavische Rundschau',
	'Slowanka',
	'Sonntagsblätter für heimatliche Interessen',
	'Stadt und Land',
	'Sudetendeutsche Zeitschrift für Volkskunde',
	'Sudetendeutschen',
	'Tagesbote aus Böhmen',
	'Union',
	'Unterhaltungsblätter',
	'Volksblatt für Böhmen',
	'Witiko',
	'Wochenblätter für Freiheit und Gesetz',
	'Zeitschrift des Deutschen Vereins für die Geschichte Mährens und Schlesiens',
	'Zeitschrift für Geschichte und Kulturgeschichte Oesterreichisch-Schlesiens',
	'Zeitschrift für Slavische Philologie',
	'Zeitschrift für sudetendeutsche Geschichte'
]

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
	#elif name.rstrip(',') != data[0].rstrip(','):
	#	autlog.write(
	#		tag +
	#		': AUT name do not match. ' +
	#		str(rec) + ' | ' +
	#		str(ident) +
	#		' | ' + name.rstrip(',') +
	#		' | ' + data[0].rstrip(',') +
	#		'\n'
	#	)
	#	return ret
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
	record['245']['a'] = PART[0].strip() + ' :'
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
	record['245']['a'] = TEXT[0].strip()
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
			record['245']['a'] = DOT[0].strip() + ' /'
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
#broken = open(BROKEN, 'w')

with open(PATCH, 'r') as f:
	patch = f.read().splitlines()

writer = MARCWriter(open(OUT,'wb'))
#bib = open(OUT,'w')

# MAIN -----------------

with open(IN, 'r') as f:
	for LINE in f:

		# INIT -----------------

		#record = Record(force_utf8=True)
		record = Record()
	
		record.leader='     nab a22     4a 4500'# overwrite internal(pymarc.record) LDR tag
		record.add_ordered_field(Field(tag='FMT', data='RS'))
		record.add_ordered_field(Field(tag='003', data='CZ PrUCL'))
		record.add_ordered_field(Field(tag='005', data='20201231'))
		record.add_ordered_field(Field(tag='040', indicators=[' ',' '], subfields=['a', 'ABB060','b', 'cze']))
		#record.add_ordered_field(Field(tag='041', indicators=['0',' '], subfields=['a', 'cze']))
		record.add_ordered_field(Field(tag='336', indicators=[' ',' '], subfields=['a', 'text', 'b', 'txt', '2', 'rdacontent']))
		record.add_ordered_field(Field(tag='337', indicators=[' ',' '], subfields=['a', 'bez média', 'b', 'n', '2', 'rdamedia']))
		record.add_ordered_field(Field(tag='338', indicators=[' ',' '], subfields=['a', 'jiný', 'b', 'nz', '2', 'rdacarrier']))
		record.add_ordered_field(Field(tag='500', indicators=[' ',' '], subfields=['a', 'Strojově převedený záznam z RETROBI bez redakční kontroly.']))
		record.add_ordered_field(Field(tag='910', indicators=[' ',' '], subfields=['a', 'ABB060']))
		record.add_ordered_field(Field(tag='964', indicators=[' ',' '], subfields=['a', 'RETROBI']))
		record.add_ordered_field(Field(tag='OWN', indicators=[' ',' '], subfields=['a', 'UCLA']))
		record.add_ordered_field(Field(tag='SIF', indicators=[' ',' '], subfields=['a', 'RET']))

		# PARSE -----------------
		try:
			jsn = json.loads(LINE.strip().rstrip(','), strict=False)
		except:
			print("Broken JSON.")# skip broken line
			continue

		# skip all non state record
		if not find('state', jsn): continue
		# skip segmented for now
		#if find('state', jsn) != 'FRESH': continue
		# skip non-segmented for now
		if find('state', jsn) != 'SEGMENTED': continue
	
		#print(json.dumps(jsn))
		#print(json.dumps(jsn, indent=2))

		# skip non broken for now 'AUT do not match only' 
		IDENT =find('tree/nazvova_cast/autor/id', jsn)
		if not IDENT: continue
		if IDENT not in patch: continue

		# 001
		record.add_ordered_field(Field(tag='001', data='RET-' + find('_id',jsn)))
		# 008
		LANG='cze'
		DATA='600101'
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
				record.add_ordered_field(Field(tag='100', indicators=['1',' '], subfields=MDT))
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
			record.add_ordered_field(Field(tag='520', indicators=['2',' '], subfields=['a', ANOT]))
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
			record.add_ordered_field(Field(tag='655', indicators=[' ','7'], subfields=['a', CHAR]))

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
				record.add_ordered_field(Field(tag='773', indicators=['0',' '], subfields=['t', SRC, 'g', 'Jg. ' + DATE, '9', YEAR]))
			else:
				record.add_ordered_field(Field(tag='773', indicators=['0',' '], subfields=['t', SRC, 'g', u'Roč. ' + DATE, '9', YEAR]))
		else:
			if NAME and YEAR:
				record.add_ordered_field(Field(tag='773', indicators=['0',' '], subfields=['t', NAME, 'g', 'R. ' + YEAR, '9', YEAR]))
			if NAME and not YEAR:
				record.add_ordered_field(Field(tag='773', indicators=['0',' '], subfields=['t', NAME]))
			if not NAME and YEAR:
				record.add_ordered_field(Field(tag='773', indicators=['0',' '], subfields=['g','R. ' + YEAR, '9', YEAR]))

		# 856
		LINK = 'http://retrobi.ucl.cas.cz/retrobi/katalog/listek/' + find('_id', jsn)
		record.add_ordered_field(Field(tag='856', indicators=['4','0'], subfields=['u', LINK, 'y', u'původní lístek v RETROBI', '4', 'N']))
		# SIR
		if find('segment_excerpter', jsn):
			record.add_ordered_field(Field(tag='SIR', indicators=[' ', ' '], subfields=['a', find('segment_excerpter', jsn)]))
		# 245 / TIT / TIZ
		TITLE = find('segment_title', jsn)
		if TITLE:
			TITLE = TITLE.strip().rstrip('|')
			# last square bracet
			BRACE = re.findall('(?<= \[)(?<!=)[^\[\]]+(?=\]$)', TITLE)
			if BRACE:
				if '655' not in record:
					record.add_ordered_field(Field(tag='655', indicators=[' ', '4'], subfields=['a', BRACE[0]]))
				else:
					if 'a' not in record['655']:
						record['655'].add_subfield('a', BRACE[0], 0)
						record['655'].indicator2 = '4'
				TITLE = TITLE.replace(' [' + BRACE[0] + ']', '')
				# dot triple dot
				if re.match('^[^:]+: [^\.]+[^A-Z0-9]\. [^"\.\]\[]+\.\.\.(?!\]\))( .+)?$',TITLE) and ';' not in TITLE:
					segment_dot_triple_dot(TITLE,record)
				# triple dot
				elif re.match('^[^:]+: [^"\.\]\[]+\.\.\.(?![\]\)])( .+)?$', TITLE) and ';' not in TITLE:
					if len(re.findall('\.\.\.', TITLE)) == 1:
						segment_triple_dot(TITLE,record)
				# no dot
				elif re.match('^[^:]+: [^\.\]\[]+$', TITLE) and ';' not in TITLE:
					record['245']['a'] = TITLE.split(': ')[1].strip() + ' /'
					if 'c' in record['245']:
						record['245']['c'] = TITLE.split(':')[0]
					else:
						record['245'].add_subfield('c', TITLE.split(':')[0])
				# default dynamic
				else:
					segment_recursion(TITLE,record)
			# TIT
			record.add_ordered_field(Field(tag='TIT', indicators=[' ', ' '], subfields=['a', TITLE]))

		# 989
		OCRF = find('ocr_fix', jsn).replace('\n', ' ')
		OCR = find('ocr', jsn).replace('\n', ' ')
		if OCRF:
			record.add_ordered_field(Field(tag='989', indicators=[' ', ' '], subfields=['a', OCRF]))
		elif OCR:
			record.add_ordered_field(Field(tag='989', indicators=[' ', ' '], subfields=['a', OCR]))

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
				else: IND += " "
				if F.indicator2: IND += F.indicator2
				else: IND += " "
			except:
				 IND = "  "
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

#broken.close()
autlog.close()
writer.close()
#bib.close()
con.close()

sys.exit(0)

