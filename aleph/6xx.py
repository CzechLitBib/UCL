#!/usr/bin/python3

import sys,re

from pymarc import marcxml

DATA='ucla.xml'

SIX = ['610','611','648','650','651','655']

IN='in/*_fix.csv'

#aleph = open(OUT,'w')

MAP={}
for F in SIX:
	MAP[F] = {}
	with open('in/' + F + '_fix.csv', 'r') as f:
		for line in f.read().splitlines():
			data = line.split('|')
			if len(data) == 4:
				if re.findall('(?<!\$)\$(?!\$)', data[2]) or re.findall('\$\$[^a-z0-9]', data[2]):
					print(F + ': Broken CSV data.')
					print(data)
				else:
					if F == '650':
						MAP[F][data[0]] = data[2]
					else:
						MAP[F][data[1]] = data[2]
			else:
				print(F + " :Broken CSV line.")
				print(data)

def get_value(field):
	data = []
	for sub in field:
		if sub[0] != '4':
			data.append(sub[1].strip())
	return ' '.join(data)

def aleph_write(record, field):
	with open('in/' + field + '.aleph', 'w') as f:
		IDENT = record['001'].value()
		for F in record.get_fields(field):
			V = get_value(F)
			SUB=''
			if V in MAP:
				if F.indicator2 == '7':
					aleph.write(IDENT + ' ' + F.tag + F.indicator1 + F.indicator2 + ' L ' + MAP[V] + '$$2czenas' + '\n')
				else:
					aleph.write(IDENT + ' ' + F.tag + F.indicator1 + F.indicator2 + ' L ' + MAP[V] + '\n')
			else:
				for i in range(0, int(len(F.subfields)/2)):
					SUB+='$$' + F.subfields[i*2] + F.subfields[i*2 + 1]
				aleph.write(IDENT + ' ' + F.tag + F.indicator1 + F.indicator2 + ' L ' + SUB + '\n')

def validate(record):

	for F1 in SIX:
		for F2 in record.get_fields(F1):
			if get_value(F2) in MAP[F1]:
				aleph_write(record, F1)
				break

marcxml.map_xml(validate, DATA)

aleph.close()

