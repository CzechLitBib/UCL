#!/usr/bin/python
#
# JSON to MARC convertor.
#

# INCLUDE -----------------

from __future__ import print_function

import json,sys,os,re

from pymarc import JSONReader,JSONWriter,MARCWriter,XMLWriter

# VAR -----------------

IMPORT='import'
LOG='json-marc.log'

# INIT -----------------

try:
	os.mkdir('import')
	os.mkdir('export')
	os.mkdir('export/json')
	os.mkdir('export/marc')
	os.mkdir('export/xml')
except: pass

try:
	log = open(LOG,'a',0)
except:
	print('Read only FS exiting.')
	sys.exit(1)

# MAIN -----------------

for f in os.listdir(IMPORT):

	# LOAD JSON
	j = json.load(f, encoding='utf-8')

	# MODIFY STRUCTURE
	print(json.dumps(j, indent=2))

	# WRITEMARC
	reader = JSONReader(j)
	for record in reader:
		# EXPORT MARC21
		writer = MARCWriter(open('export/marc/' + re.sub('(.*)\.json', '\\1', f)  + '.dat', 'wb'))
		writer.write(record)
		writer.close()
		# JSON
		writer = JSONWriter(open('export/json/' + re.sub('(.*)\.json', '\\1', f) + '.json', 'wt'))
		writer.write(record)
		writer.close()
		# MARCXML
		writer = XMLWriter(open('export/xml/' + re.sub('(.*)\.json', '\\1', f) + '.xml', 'wb'))
		writer.write(record)
		writer.close()

# EXIT -------------------

log.close()
print('Done.')

