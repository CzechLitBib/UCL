#!/usr/bin/python
#
# yaz-client wrapper
#
# YAZ CLIENT CMD SEQUENCE
#
# open tcp:aleph.nkp.cz:9991/AUT-UTF
# set_marcdump rec.bin
# find "fd133958"
# show 1
# quit

import subprocess,sys,os

from pymarc import MARCReader

def get_mdt(s):
	try:
		# create config, bin
		with open('z3950.cfg', 'w') as f:
			f.write('open tcp:aleph.nkp.cz:9991/AUT-UTF\n')
			f.write('set_marcdump rec.bin\n')
			f.write('find "' + s + '"\n')
			f.write('show 1\n')
			f.write('quit')
		# call client
		data = subprocess.check_output(['yaz-client', '-f', 'z3950.cfg'])
		# paprse output
		reader = MARCReader(open('rec.bin','rb'))
		for rec in reader:
			print(rec)
		# cleanup
		os.remove('z3950.cfg')		
		os.remove('rec.bin')
	except: pass
	return 0

get_mdt('fd133958')

