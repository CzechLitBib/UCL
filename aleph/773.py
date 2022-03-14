#!/usr/bin/python3

from pymarc import marcxml

DATA='ucla.xml'
OUT='773.aleph'

aleph = open(OUT,'a')

def aleph_write(record):
	IDENT = record['001'].value()
	for F in record.get_fields('773'):
		SUB=''
		if F.indicator1 + F.indicator2 == '08': F.indicator2 = ' ';
		for i in range(0, int(len(F.subfields )/2)):
			SUB += '$$' + F.subfields[i*2] + F.subfields[i*2 + 1]
		aleph.write(IDENT + ' ' + F.tag + F.indicator1 + F.indicator2 + ' L ' + SUB + '\n')

def validate(record):
	for F in record.get_fields('773'):
		if F.indicator1 + F.indicator2 == '08':
			aleph_write(record)
			break

marcxml.map_xml(validate, DATA)

aleph.close()

