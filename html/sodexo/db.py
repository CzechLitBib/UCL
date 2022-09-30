#!/usr/bin/python3
#
# Sodexo DB 
#

import sqlite3

DB='sodexo.db'

con = sqlite3.connect(DB)
cur = con.cursor()

cur.execute("CREATE TABLE data (y INTEGER, n TEXT, sn TEXT, q INTEGER);")

con.commit()
con.close()

