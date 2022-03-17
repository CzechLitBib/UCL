#!/usr/bin/python3

from pymarc import marcxml

DATA='ucla.xml'
OUT='041.aleph'

aleph = open(OUT,'a')

def validate(record):
	IDENT = record['001'].value()
	for F in record.get_fields('041'):
		SUB=''
		if int(len(F.subfields)) == 2:
			if F.subfields[1] == record['008'].value()[35:38].strip():
				for i in range(0, int(len(F.subfields)/2)):
					SUB+='$$' + F.subfields[i*2] + F.subfields[i*2 + 1]
				aleph.write(IDENT + ' ' + F.tag + F.indicator1 + F.indicator2 + ' L ' + SUB + '\n')
			else:
				print("Mismatch: " + record['001'].value())
				continue

		if int(len(F.subfields)) < 2:
				print("Empty: " + record['001'].value())
				continue

marcxml.map_xml(validate, DATA)

aleph.close()

