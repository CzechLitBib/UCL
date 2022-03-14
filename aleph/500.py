#!/usr/bin/python3

import sys

from pymarc import marcxml

DATA='ucla.xml'
IN='500.in'
OUT='500.aleph'
SIGNED=[]

aleph = open(OUT,'a')

with open(IN,'r') as sign: SIGNED = sign.read().splitlines()

def aleph_write(record):
	IDENT = record['001'].value()
	for F in record.get_fields('500'):
		SUB=''
		for i in range(0, int(len(F.subfields )/2)):
			SUB += '$$' + F.subfields[i*2] + F.subfields[i*2 + 1]
		aleph.write(IDENT + ' ' + F.tag + F.indicator1 + F.indicator2 + ' L ' + SUB + '\n')
	aleph.write(IDENT + ' 500   L $$aNepodepsáno.\n')

def validate(record):
	if record['001'].value() in SIGNED:
		for F in record.get_fields('500'):
			if 'a' in F and F['a'] == 'Nepodepsáno.':
				print("Done: " + record['001'].value())
				break
		else:
			aleph_write(record)

marcxml.map_xml(validate, DATA)

aleph.close()

