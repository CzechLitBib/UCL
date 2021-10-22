#!/usr/bin/python3
#
# Get marcsolr mapping
#
# BUG: 072,520 -  Non repeat.
#          FMT - Invalid control field.
#

import re

# ID

print('id=001, first')

# FIELD

CTRL=[
	'field_LDR',
	'field_001',
	'field_003',
	'field_005',
	'field_008'
]

with open('field_all.txt') as f:
	for F in  f.read().splitlines():
		if F in CTRL:
			if F == 'field_LDR':
				print(F + '=000')
			else:
				print(F + '=' + re.sub('.*_', '', F))
		else:
			print(F + '=' + re.sub('.*_', '', F) + ", join($$)")

# SUBFIELD
REPEAT=[
	'subfield_245-n',
	'subfield_245-p',
	'subfield_773-q',
	'subfield_773-z',
	'subfield_773-9',
	'subfield_787-k',
	'subfield_787-z'
]

with open('subfield_all.txt') as f:
	for S in  f.read().splitlines():
		if S in REPEAT:
			print(S + '=' + re.sub('.*_(.*)(.)$', '\\1\\2\\2', S.replace('-','')) + ", join($$)")
		else:
			print(S + '=' + re.sub('.*_', '', S).replace('-',''))

# LOCAL

print('local_LDR-8=000[7]')
print('local_008-16=008[0-5]')
print('local_008-7=008[6]')
print('local_008-811=008[7-10]')
print('local_008-815=008[7-14]')
print('local_008-1618=008[15-17]')
print('local_008-3638=008[35-37]')

