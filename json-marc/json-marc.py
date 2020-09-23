#!/usr/bin/python
# -*- coding: utf-8 -*-
#
# JSON to MARC convertor.
# 

# INCLUDE -----------------

from __future__ import print_function

import sqlite3,json,sys,os,re

from pymarc import Record
from pymarc.field import Field

# VAR -----------------

#IN='tmp/retrobi.json'
IN='demo.json'
OUT='retrobi.bib'
AUTLOG='aut.log'
BROKEN='broken.log'
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

# INIT -----------------

try:
	con = sqlite3.connect(DB)# sqlite:///your_filename.db
	cur = con.cursor()
except:
	print("Failed to SQLite connect.")
	sys.exit(1)

bib = open(OUT,'w')
autlog = open(AUTLOG, 'w')
broken = open(BROKEN, 'w')

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

# MAIN -----------------

with open(IN, 'rb') as f:
	for LINE in f:

		# INIT -----------------

		record = Record()

		record.add_ordered_field(Field(tag='LDR', data='-----nab-a22-----4a-4500'))
		record.add_ordered_field(Field(tag='FMT', data='RS'))
		record.add_ordered_field(Field(tag='003', data='CZ-PrUCL'))
		record.add_ordered_field(Field(tag='005', data='20201231'))
		record.add_ordered_field(Field(tag='040', indicators=['\\','\\'], subfields=['a', 'ABB060','b', 'cze']))
		record.add_ordered_field(Field(tag='041', indicators=['0','\\'], subfields=['a', 'cze']))
		record.add_ordered_field(Field(tag='336', indicators=['\\','\\'], subfields=['a', 'text', 'b', 'txt', '2', 'rdacontent']))
		record.add_ordered_field(Field(tag='337', indicators=['\\','\\'], subfields=['a', u'bez média', 'b', 'n', '2', 'rdamedia']))
		record.add_ordered_field(Field(tag='338', indicators=['\\','\\'], subfields=['a', u'jiný', 'b', 'nz', '2', 'rdacarrier']))
		record.add_ordered_field(Field(tag='500', indicators=['\\','\\'], subfields=['a', u'Strojově převedený záznam z RETROBI bez redakční kontroly.']))
		record.add_ordered_field(Field(tag='910', indicators=['\\','\\'], subfields=['a', 'ABB060']))
		record.add_ordered_field(Field(tag='964', indicators=['\\','\\'], subfields=['a', 'RETROBI']))
		record.add_ordered_field(Field(tag='OWN', indicators=['\\','\\'], subfields=['a', 'UCLA']))

		# PARSE -----------------

		j = json.loads(LINE.strip().rstrip(','), strict=False)
		#print(json.dumps(j, indent=2))
		#print(j['id'])
		# Broken
		if not 'tree' in j['doc']:
			broken.write('Broken: ' + j['id'] + '\n')
			continue
		# 001
		record.add_ordered_field(Field(tag='001', data='RET-' +  j['id']))
		# 008
		lang='cze'
		DAT='19600101'
		if j['doc']['tree']['bibliograficka_cast'][0]['zdroj'][0]['rok'][0]:
			if len(j['doc']['tree']['bibliograficka_cast'][0]['zdroj'][0]['rok'][0]) == 4:
				DAT+='s' + j['doc']['tree']['bibliograficka_cast'][0]['zdroj'][0]['rok'][0]
			else: DAT+='n----'
		else: DAT+='n----'
		DAT+='----xr------------|||-||'
		if j['doc']['tree']['bibliograficka_cast'][0]['zdroj'][0]['nazev'][0] in SLO_MAP: lang='slo'
		if j['doc']['tree']['bibliograficka_cast'][0]['zdroj'][0]['nazev'][0] in GER_MAP: lang='ger'
		DAT+=lang + '-d'
		record.add_ordered_field(Field(tag='008', data=DAT))
		# 100
		name = j['doc']['tree']['nazvova_cast'][0]['autor'][0]['jmeno'][0]
		ident = j['doc']['tree']['nazvova_cast'][0]['autor'][0]['id'][0]
		if name and ident:
			mdt = get_mdt('100', name, ident, autlog, j['id'])
			if mdt:
				mdt.append('4')
				mdt.append('aut')
				record.add_ordered_field(Field(tag='100', indicators=['1','\\'], subfields=mdt))
		# 245
		name = re.sub('(.*), (.*)', '\\2 \\1', j['doc']['tree']['nazvova_cast'][0]['autor'][0]['jmeno'][0])
		if name:
			record.add_ordered_field(Field(tag='245', indicators=['1','0'], subfields=['a', u'[Název textu k dispozici na připojeném lístku]', 'c', name]))
		else:
			record.add_ordered_field(Field(tag='245', indicators=['1','0'], subfields=['a', u'[Název textu k dispozici na připojeném lístku]']))
		# 520
		anotace = j['doc']['tree']['anotacni_cast'][0]['anotace'][0].rstrip('|')
		if 'segment_annotation' in j['doc']:
			anotace = j['doc']['segment_annotation'].rstrip('|')
		if anotace:
			record.add_ordered_field(Field(tag='520', indicators=['2','\\'], subfields=['a', re.sub('^\[(.*)\]$', '\\1', anotace)]))
		# 600
		name = j['doc']['tree']['anotacni_cast'][0]['odkazovana_osoba'][0]['jmeno'][0]
		ident = j['doc']['tree']['anotacni_cast'][0]['odkazovana_osoba'][0]['id'][0]
		if name and ident:
			mdt = get_mdt('600', name, ident, autlog, j['id'])
			if mdt:
				mdt.append('2')
				mdt.append('czenas')
				record.add_ordered_field(Field(tag='600', indicators=['1','7'], subfields=mdt))
			else:
				record.add_ordered_field(Field(tag='600', indicators=['1','7'], subfields=['a', name, '7 ', ident]))
		# 655
		char = j['doc']['tree']['nazvova_cast'][0]['charakteristika'][0]
		if char:
			record.add_ordered_field(Field(tag='655', indicators=['\\','7'], subfields=['a', char]))
		# 773
		src = j['doc']['tree']['bibliograficka_cast'][0]['zdroj'][0]['nazev'][0]
		year = j['doc']['tree']['bibliograficka_cast'][0]['zdroj'][0]['rok'][0]
		if 'segment_bibliography' in j['doc']:
			src = re.sub('(\D+).*','\\1', j['doc']['segment_bibliography'].replace('In: ', '')).strip()
			date = re.sub('(\D+)(.*)','\\2', j['doc']['segment_bibliography'].replace('In: ', '')).strip().rstrip('|')
			# trailing dot
			date = re.sub('(?<=\d{3}).$', '', date)# trailing dot
			# 'str. ' -> 's. '
			date = date.replace(', str. ',', s. ')
			# page
			page = re.findall(' s\. [^,]+,', date)
			if page and len(page) == 1:
				date = date.replace(page[0], '')
				date = date + ',' + page[0].strip(',')
			if lang == 'ger':
				record.add_ordered_field(Field(tag='773', indicators=['0','\\'], subfields=['t', src, 'g', 'Jg. ' + date, '9', year]))
			else:
				record.add_ordered_field(Field(tag='773', indicators=['0','\\'], subfields=['t', src, 'g', u'Roč. ' + date, '9', year]))
		else:
			base_year = year
			if len(year) == 4: year = 'R. ' + year
			if src and year:
				record.add_ordered_field(Field(tag='773', indicators=['0','\\'], subfields=['t', src, 'g', year, '9', base_year]))
			if src and not year:
				record.add_ordered_field(Field(tag='773', indicators=['0','\\'], subfields=['t', src]))
			if not src and year:
				record.add_ordered_field(Field(tag='773', indicators=['0','\\'], subfields=['g', year, '9', base_year]))
		# 856
		link = 'http://retrobi.ucl.cas.cz/retrobi/katalog/listek/' + j['id']
		record.add_ordered_field(Field(tag='856', indicators=['4','0'], subfields=['u', link, 'y', u'původní lístek v RETROBI', '4', 'N']))
		# SIF
		record.add_ordered_field(Field(tag='SIF', indicators=['\\', '\\'], subfields=['a', 'RET']))
		# SIR
		if 'segment_excerpter' in j['doc']:
			sir = j['doc']['segment_excerpter']
			record.add_ordered_field(Field(tag='SIR', indicators=['\\', '\\'], subfields=['a', sir]))
		# TIT + TIZ
		tit=''
		if 'segment_title' in j['doc']:
			tit = j['doc']['segment_title'].strip('|')
			# 655-4 a
			# last square bracer
			brace = re.findall('(?<= \[)(?<!=)[^\[\]]+(?=\]$)', tit)
			if brace:
				if '655' not in record:
					record.add_ordered_field(Field(tag='655', indicators=['\\', '4'], subfields=['a', brace[0]]))
				else:
					if 'a' not in record['655']:
						record['655'].add_subfield('a', brace[0], 0)
						record['655'].indicator2 = '4'
				tit = tit.replace(' [' + brace[0] + ']', '')
				# frist colon
				colon = re.findall('(^[^:]+):.*', tit)
				if colon:
					if 'c' in record['245']:
						record['245']['c'] =  colon[0]
					else:
						record['245'].add_subfield('c', colon[0])
					tit = tit.replace(colon[0] + ': ', '')
					# first dot
					dot = re.findall('(^[^\.]+)\..*', tit)
					if dot:
						record['245']['a'] = dot[0] + ' /'
						tit = tit.replace(dot[0] + '. ', '')
						# 245
						record['245']['c'] = record['245']['c'] + ' ; ' + tit
						record.add_ordered_field(Field(tag='TIZ', indicators=['\\', '\\'], subfields=['a', tit]))
						# lang
						lang = re.findall(u'(?<=\[[Zz] ).*?(?=\.\] [Pp]řel\.)|(?<=[Pp]řel\. \[[Zz] ).*?(?=\.])', tit)
						if lang:
							record['041'].indicator1 = '1'
							record['041'].add_subfield('h', get_lang(lang[0]))
							tit = tit.replace('[Z ' + lang[0] + '.]', '').replace('[z '+lang[0] + '.]', '')
							# trans
							trans = re.findall(u'(?<=[Pp]řel\. ).*', tit.strip())
							if trans:
								record.add_ordered_field(Field(tag='700', indicators=['1', '\\'], subfields=['a', trans[0]]))
			record.add_ordered_field(Field(tag='TIT', indicators=['\\', '\\'], subfields=['a', tit]))
		# TXT
		ocr=''
		if 'ocr_fix' in j['doc']:
			ocr = j['doc']['ocr_fix']
			record.add_ordered_field(Field(tag='TXT', indicators=['\\', '\\'], subfields=['a', ocr]))
		elif 'ocr' in j['doc']:
			ocr = j['doc']['ocr']
			record.add_ordered_field(Field(tag='TXT', indicators=['\\', '\\'], subfields=['a', ocr]))

		# WRITE -----------------

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
			if F.tag == 'LDR': DATA = '-----nab-a22-----4a-4500'
			if F.tag in ['LDR', 'FMT', '001', '003', '005', '008']:
				bib.write('=' + str(F.tag) + '  ' + DATA.encode('utf-8')+ '\n')
			else:
				bib.write('=' + str(F.tag) + '  ' + str(IND) + DATA.encode('utf-8') + '\n')
		bib.write('\n')

# EXIT -------------------

broken.close()
autlog.close()
bib.close()
con.close()

