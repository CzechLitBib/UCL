#!/usr/bin/python3

from pymarc import marcxml

DATA='ucla.xml'
OUT='041.txt'

aleph = open(OUT,'a')

def validate(record):
	for F in record.get_fields('041'):
		if int(len(F.subfields)) == 2:
			if F.subfields[1] == record['008'].value()[35:38].strip():
				aleph.write(record['001'].value() + '\n')
				break
			else:
				print("Mismatch: " + record['001'].value())
				continue
		if int(len(F.subfields)) < 2:
				print("Empty: " + record['001'].value())
				continue

marcxml.map_xml(validate, DATA)

aleph.close()

