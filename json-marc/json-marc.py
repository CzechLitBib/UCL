#!/usr/bin/python
# -*- coding: utf-8 -*-
#
# JSON to MARC convertor.
# 

# INCLUDE -----------------

from __future__ import print_function

import tarfile,argparse,json,sys,os,re

from pymarc import Record
from pymarc.field import Field

# VAR -----------------

#IN='retrobi.json'
IN='demo.json'
OUT='retro.tar.gz'

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

# INIT -----------------

record = Record()

record.leader = '     nab a22     4a 4500'
#record.add_ordered_field(Field(tag='FMT', data='RS'))
record.add_ordered_field(Field(tag='003', data='CZ PrUCL'))
record.add_ordered_field(Field(tag='005', data='20201231'))
record.add_ordered_field(Field(tag='040', indicators=[' ',' '], subfields=['a', 'ABB060','b', 'cze']))
record.add_ordered_field(Field(tag='041', indicators=['0',' '], subfields=['a', 'cze']))
record.add_ordered_field(Field(tag='336', indicators=[' ',' '], subfields=['a', 'text', 'b', 'txt', '2', 'rdacontent']))
record.add_ordered_field(Field(tag='337', indicators=[' ',' '], subfields=['a', u'bez média', 'b', 'n', '2', 'rdamedia']))
record.add_ordered_field(Field(tag='338', indicators=[' ',' '], subfields=['a', u'jiný', 'b', 'nz', '2', 'rdacarrier']))
record.add_ordered_field(Field(tag='500', indicators=[' ',' '], subfields=['a', u'Strojově převedený záznam z RETROBI bez redakční kontroly.']))
record.add_ordered_field(Field(tag='910', indicators=[' ',' '], subfields=['a', 'ABB060']))
record.add_ordered_field(Field(tag='964', indicators=[' ',' '], subfields=['a', 'RETROBI']))
record.add_ordered_field(Field(tag='OWN', indicators=[' ',' '], subfields=['a', 'UCLA']))
#record.add_ordered_field(Field(tag='SIF', data='RET'))

# DEF -----------------

with open(IN, 'rb') as f:
	for LINE in f:
		j = json.loads(re.sub('(.*),$','\\1',LINE.strip()), strict=False)
		#print(json.dumps(j, indent=2))
		# ID
		record.add_ordered_field(Field(tag='001', data='RET-' +  j['id']))
		# 008
		DAT='19600101'
		if int(j['doc']['tree']['bibliograficka_cast'][0]['zdroj'][0]['rok'][0]):
			DAT+='s' + j['doc']['tree']['bibliograficka_cast'][0]['zdroj'][0]['rok'][0]
		else:
			DAT+='n    '
		DAT+='    xr            ||| ||'
		if j['doc']['tree']['bibliograficka_cast'][0]['zdroj'][0]['nazev'][0] in SLO_MAP:
			DAT+='slo'
		elif j['doc']['tree']['bibliograficka_cast'][0]['zdroj'][0]['nazev'][0] in GER_MAP:
			DAT+='ger'
		else:
			DAT+='cze'
		
		DAT+=' d'
		record.add_ordered_field(Field(tag='008', data=DAT))
		#

#parser.parse(f=j)
#j.close()

# EXIT -------------------

#log.close()
#print('Done.')

