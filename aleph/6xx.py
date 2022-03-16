#!/usr/bin/python3

import sys,re

from pymarc import marcxml

DATA='ucla.xml'

SIX = ['610','611','648','650','651','655']

IN='in/*_fix.csv'

spec = open('special.csv','w')

MAP={}
for F in SIX:
	MAP[F] = {}
	with open('in/' + F + '_fix.csv', 'r') as f:
		for line in f.read().splitlines():
			data = line.split('|')
			if len(data) == 4:
				if re.findall('(?<!\$)\$(?!\$)', data[2]) or re.findall('\$\$[^a-z0-9]', data[2]):
					print(F + ': Broken CSV data.')
				else:
					if F == '650':# CSV mismatch
						if F == data[3].strip()[:3]: # exclude spec.
							MAP[F][data[0]] = [data[2], data[3].strip()[3:]]
						else:
							spec.write(F + '|' + '|'.join(data) + '\n')
					else:
						if F == data[3].strip()[:3]: # exclude spec.
							MAP[F][data[1]] = [data[2], data[3].strip()[3:], data[3].strip()[:3]]
						else:
							spec.write(F + '|' + '|'.join(data) + '\n')
			else:
				print(F + " :Broken CSV line.")
spec.close()

def get_value(field):
	data = []
	for sub in field:
		if sub[0] != '4':
			data.append(sub[1].strip())
	return ' '.join(data)

def aleph_write(record, field):
	with open(field + '.aleph', 'a') as f:
		IDENT = record['001'].value()
		for F in record.get_fields(field):
			V = get_value(F)
			SUB=''
			if V in MAP[field]:
				f.write(IDENT + ' ' + F.tag + MAP[field][V][1] + ' L ' + MAP[field][V][0] + '$$2czenas' + '\n')
			else:
				for i in range(0, int(len(F.subfields)/2)):
					SUB+='$$' + F.subfields[i*2] + F.subfields[i*2 + 1]
				f.write(IDENT + ' ' + F.tag + F.indicator1 + F.indicator2 + ' L ' + SUB + '\n')

def validate(record):
	for F1 in SIX:
		for F2 in record.get_fields(F1):
			if get_value(F2) in MAP[F1]:
				aleph_write(record, F1)
				break

marcxml.map_xml(validate, DATA)

