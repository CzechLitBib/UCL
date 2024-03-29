#!/usr/bin/python3
#
# ČLB - Návrhy podkladů DB 
#

import sqlite3

DB='form.db'

con = sqlite3.connect(DB)
cur = con.cursor()

cur.execute("""CREATE TABLE data (
	id		TEXT,
	visible		INTEGER,
	format		TEXT,
	public		INTEGER,
	dedication	INTEGER,
	link		TEXT,
	email		TEXT,
	note		TEXT,
	author		TEXT,
	name		TEXT,
	text_author	TEXT,
	text_name	TEXT,
	place		TEXT,
	publisher	TEXT,
	year		TEXT,
	source		TEXT,
	quote		TEXT,
	page		TEXT,
	other		TEXT);"""
)

cur.execute("CREATE TABLE file (id TEXT, name TEXT);")

cur.execute("CREATE UNIQUE INDEX 'data_index' ON data (id);")
cur.execute("CREATE UNIQUE INDEX 'file_index' ON file (id);")

con.commit()
con.close()

