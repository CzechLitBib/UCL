#!/usr/bin/python3

import sys,re

from pymarc import marcxml

DATA='ucla.xml'
IN='100+700.csv'
OUT='100+700.aleph'

aleph = open(OUT,'w')

MAP={}

with open(IN, 'r') as f:
	for line in f.read().splitlines():
		data = line.split('|')
		if len(data) == 3:
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

def aleph_write_700(record):
	IDENT = record['001'].value()
	for F in record.get_fields('700'):
		V = get_value(F)
		SUB=''
		if V in MAP:
			if '$$7' in MAP[V]:
				aleph.write(IDENT + ' ' + F.tag + F.indicator1 + F.indicator2 + ' L ' + MAP[V].replace('$$7', '$$4aut$$7') + '\n')
			else:
				aleph.write(IDENT + ' ' + F.tag + F.indicator1 + F.indicator2 + ' L ' + MAP[V] + '$$4aut' + '\n')
		else:
			for i in range(0, int(len(F.subfields)/2)):
				SUB+='$$' + F.subfields[i*2] + F.subfields[i*2 + 1]
			aleph.write(IDENT + ' ' + F.tag + F.indicator1 + F.indicator2 + ' L ' + SUB + '\n')

def aleph_write_100(record):
	IDENT = record['001'].value()
	for F in record.get_fields('100'):
		V = get_value(F)
		SUB=''
		if V in MAP:
			if '$$7' in MAP[V]:
				aleph.write(IDENT + ' ' + F.tag + F.indicator1 + F.indicator2 + ' L ' + MAP[V].replace('$$7', '$$4aut$$7') + '\n')
			else:
				aleph.write(IDENT + ' ' + F.tag + F.indicator1 + F.indicator2 + ' L ' + MAP[V] + '$$4aut' + '\n')
		elif '4' not in F:
			for i in range(0, int(len(F.subfields)/2)):
				if F.subfields[i*2] == 'x' and F.subfields[i*2 + 1] == 'OPRAVA UCL': continue# remove 'x' OPRAVA UCL
				SUB+='$$' + F.subfields[i*2] + F.subfields[i*2 + 1]
			if '$$7' in SUB:
				aleph.write(IDENT + ' ' + F.tag + F.indicator1 + F.indicator2 + ' L ' + SUB.replace('$$7','$$4aut$$7') + '\n')
			else:
				aleph.write(IDENT + ' ' + F.tag + F.indicator1 + F.indicator2 + ' L ' + SUB + '$$4aut' + '\n')
		else:
			for i in range(0, int(len(F.subfields)/2)):
				SUB+='$$' + F.subfields[i*2] + F.subfields[i*2 + 1]
			aleph.write(IDENT + ' ' + F.tag + F.indicator1 + F.indicator2 + ' L ' + SUB + '\n')

def validate(record):
	for F in record.get_fields('700'):
		if get_value(F) in MAP:
			aleph_write_700(record)
			break
	for F in record.get_fields('100'):
		if get_value(F) in MAP or '4' not in F:
			aleph_write_100(record)
			break
	


marcxml.map_xml(validate, DATA)

aleph.close()

