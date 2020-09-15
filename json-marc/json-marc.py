#!/usr/bin/python
# -*- coding: utf-8 -*-
#
# JSON to MARC convertor.
# 

# INCLUDE -----------------

from __future__ import print_function

import StringIO,tarfile,json,sys,os,re

from pymarc import Record
from pymarc.field import Field

# VAR -----------------

#IN='retrobi.json'
IN='demo.json'
OUT='retrobi.bib'

#buff=StringIO.StringIO()

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

bib = open(OUT,'w')

# DEF -----------------

with open(IN, 'rb') as f:
	for LINE in f:

		# INIT -----------------

		record = Record()

		record.add_ordered_field(Field(tag='LDR', data='     nab a22     4a 4500'))
		record.add_ordered_field(Field(tag='FMT', data='RS'))
		record.add_ordered_field(Field(tag='003', data='CZ PrUCL'))
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
		record.add_ordered_field(Field(tag='SIF', data='RET'))

		# PARSE -----------------

		j = json.loads(re.sub('(.*),$','\\1',LINE.strip()), strict=False)
		#print(json.dumps(j, indent=2))
		
		if not 'tree' in j['doc']:
			print('Broken: ' + j['id'])
			continue
		#else:
		#	print(j['id'] + ' Done.')

		# 001
		record.add_ordered_field(Field(tag='001', data='RET-' +  j['id']))
		# 008
		DAT='19600101'
		if j['doc']['tree']['bibliograficka_cast'][0]['zdroj'][0]['rok'][0]:
			if len(j['doc']['tree']['bibliograficka_cast'][0]['zdroj'][0]['rok'][0]) == 4:
				DAT+='s' + j['doc']['tree']['bibliograficka_cast'][0]['zdroj'][0]['rok'][0]
			else: DAT+='n    '
		else: DAT+='n    '
		DAT+='    xr            ||| ||'
		if j['doc']['tree']['bibliograficka_cast'][0]['zdroj'][0]['nazev'][0] in SLO_MAP:
			DAT+='slo'
		elif j['doc']['tree']['bibliograficka_cast'][0]['zdroj'][0]['nazev'][0] in GER_MAP:
			DAT+='ger'
		else:
			DAT+='cze'
		DAT+=' d'
		record.add_ordered_field(Field(tag='008', data=DAT))
		# 100
		author = j['doc']['tree']['nazvova_cast'][0]['autor'][0]['jmeno'][0]
		ident = j['doc']['tree']['nazvova_cast'][0]['autor'][0]['id'][0]
		record.add_ordered_field(Field(tag='100', indicators=['1','\\'], subfields=['a', author, '7', ident, '4', 'AUT']))
		# 245
		label = j['doc']['tree']['nazvova_cast'][0]['nazev'][0] + ' /'
		name = re.sub('(.*), (.*)', '\\2 \\1', j['doc']['tree']['nazvova_cast'][0]['autor'][0]['jmeno'][0])
		record.add_ordered_field(Field(tag='245', indicators=['1','0'], subfields=['a', label, 'c', name]))
		# 520
		anotace = j['doc']['tree']['anotacni_cast'][0]['anotace'][0]
		record.add_ordered_field(Field(tag='520', indicators=['2','\\'], subfields=['a', anotace]))
		# 600
		aname = j['doc']['tree']['anotacni_cast'][0]['odkazovana_osoba'][0]['jmeno'][0]
		aident = j['doc']['tree']['anotacni_cast'][0]['odkazovana_osoba'][0]['id'][0]
		record.add_ordered_field(Field(tag='600', indicators=['1','4'], subfields=['a', aname, '7', aident, '2', 'czenas']))
		# 655
		char = j['doc']['tree']['nazvova_cast'][0]['charakteristika'][0]
		record.add_ordered_field(Field(tag='655', indicators=['\\','7'], subfields=['a', char]))
		# 773
		src = j['doc']['tree']['bibliograficka_cast'][0]['zdroj'][0]['nazev'][0]
		year = j['doc']['tree']['bibliograficka_cast'][0]['zdroj'][0]['rok'][0]
		record.add_ordered_field(Field(tag='773', indicators=['0','\\'], subfields=['t', src, 'g', year, '9', year]))
		# 856
		link = 'http://retrobi.ucl.cas.cz/retrobi/katalog/listek/' + j['id']
		record.add_ordered_field(Field(tag='856', indicators=['4','0'], subfields=['u', link, 'y', u'původní lístek v RETROBI', '4', 'N']))
		# WRITE -----------------

		try:
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
				if F.tag == 'SIF': DATA = 'RET'
				if F.tag == 'LDR': DATA = '     nab a22     4a 4500'
				if F.tag in ['LDR', 'FMT', 'SIF', '001', '003', '005', '008']:
					#print('=' + str(F.tag) + '  ' + DATA)
					bib.write('=' + str(F.tag) + '  ' + DATA.encode('utf-8')+ '\n')
				else:
					#print('=' + str(F.tag) + '  '+ IND + DATA)
					bib.write('=' + str(F.tag) + '  ' + str(IND) + DATA.encode('utf-8') + '\n')
			bib.write('\n')
		except:
			print( 'Write failed.')

# EXIT -------------------

bib.close()

print('Done.')

