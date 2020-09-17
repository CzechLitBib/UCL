#!/usr/bin/python
# -*- coding: utf-8 -*-
#
# Aleph 100  to SQLite3 
#
#000000001 1001  L $$aAbraham, Ji<F8><ED>,$$d1931-$$7jk01010001
#000000002 1001  L $$aAbraham, Josef,$$d1890-1933$$7jk01010002
#000000003 1001  L $$aAbraham, Pavel,$$d1957-$$7jk01010003
#

from __future__ import print_function

import sqlite3,sys,os,re

IN='AUT-UTF-8.bib'
OUT='AUT.db'

con = sqlite3.connect(OUT)# sqlite:///your_filename.db
cur = con.cursor()
cur.execute("CREATE TABLE t (id, a, d, seven);") # use your column names here

with open(IN, 'r') as f:
	for line in f:
		buff=[]
		ID,A,D,SEVEN='','','',''

		data = line.strip().split('$$')
		ID = data[0].split(' ')[0]
		for val in data:
			if re.match('^a', val): A = re.sub('^a(.*)', '\\1', val).decode('utf-8')
			if re.match('^d', val): D = re.sub('^d(.*)', '\\1', val).decode('utf-8')
			if re.match('^7', val): SEVEN = re.sub('^7(.*)', '\\1', val)
		buff=[ID, A, D, SEVEN]
		#print(buff)
		#sys.exit(1)
		cur.execute("INSERT INTO t (id, a, d, seven) VALUES (?, ?, ?, ?);", buff)

con.commit()
con.close()

