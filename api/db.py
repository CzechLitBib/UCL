#!/usr/bin/python3
#
# Record API DB 
#

import sqlite3

from pymarc import MARCReader

IN='text.xml'
DB='record.db'

def db_create(DB):
	con = sqlite3.connect(DB)
	cur = con.cursor()
	cur.execute("CREATE TABLE record (id, ident, timestamp, metadata);")
	cur.execute("CREATE INDEX 'ident_index' ON record (ident);")
	cur.execute("CREATE INDEX 'timestamp_index' ON record (timestamp);")
	con.commit()
	con.close()
	return

def db_write():
	con = sqlite3.connect(DB)
	cur = con.cursor()
	buff=[ID,A,B,C,D,Q,W,ZERO,TWO,SEVEN]
	cur.execute("INSERT INTO t (id,a,b,c,d,q,w,zero,two,seven) VALUES (?,?,?,?,?,?,?,?,?,?);", buff)
	con.commit()
	con.close()
	return

def db_write_batch():
	con = sqlite3.connect(DB)
	cur = con.cursor()
	buff=[ID,A,B,C,D,Q,W,ZERO,TWO,SEVEN]
	cur.execute("INSERT INTO t (id,a,b,c,d,q,w,zero,two,seven) VALUES (?,?,?,?,?,?,?,?,?,?);", buff)
	con.commit()
	con.close()
	return

def db_fetch_row():
	return

def db_fetch_list(iso08601_range):
	yield generator
