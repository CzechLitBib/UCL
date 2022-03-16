#!/usr/bin/python3

import sys,re

from pymarc import marcxml

DATA='ucla.xml'
IN='in/600.csv'
OUT='600.aleph'

aleph = open(OUT,'w')

MAP={}

with open(IN, 'r') as f:
	for line in f.read().splitlines():
		data = line.split('|')
		if len(data) == 4:
			if re.findall('(?<!\$)\$(?!\$)', data[2]) or re.findall('\$\$[^a-z0-9]', data[2]):
				print('Broken CSV data.')
			else:
				MAP[data[1]] = data[2]
		else:
			print("Broken CSV line.")

def get_value(field):
	data = []
	for sub in field:
		if sub[0] != '4':
			data.append(sub[1].strip())
	return ' '.join(data)

def aleph_write(record, field):
	IDENT = record['001'].value()
	for F in record.get_fields(field):
		V = get_value(F)
		SUB=''
		if V in MAP:
			aleph.write(IDENT + ' ' + F.tag + '17' + ' L ' + MAP[V] + '$$2czenas' + '\n')
		else:
			for i in range(0, int(len(F.subfields)/2)):
				SUB+='$$' + F.subfields[i*2] + F.subfields[i*2 + 1]
			aleph.write(IDENT + ' ' + F.tag + F.indicator1 + F.indicator2 + ' L ' + SUB + '\n')

def validate(record):
	for F in record.get_fields('600'):
		if get_value(F) in MAP:
			aleph_write(record, '600')
			break

marcxml.map_xml(validate, DATA)

aleph.close()

