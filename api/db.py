#!/usr/bin/python3
#
# Vufind Record API DB 
#

import sqlite3,time,sys

from pymarc import marcxml

IN='test.xml'
DB='vufind.db'
BATCH=[]

# CREATE DB

#con = sqlite3.connect(DB)
#cur = con.cursor()
#cur.execute("CREATE TABLE record (ident TEXT, timestamp INTEGER, metadata TEXT);")
#cur.execute("CREATE UNIQUE INDEX 'ident_index' ON record (ident);")
#cur.execute("CREATE INDEX 'timestamp_index' ON record (timestamp);")
#con.commit()
#con.close()

con = sqlite3.connect(DB)
cur = con.cursor()
cur.execute("SELECT metadata FROM record WHERE timestamp BETWEEN ? AND ? ORDER BY timestamp;", (0,9999999999))
print(cur.fetchall())
con.close()
sys.exit(1)

# POPULATE DB

def load_xml(record):

	ident = record['001'].value()
	timestamp = int(time.mktime(time.strptime(record['005'].value(), '%Y%m%d%H%M%S.%f')))
	metadata = record.as_json()

	BATCH.append((ident, timestamp, metadata))

marcxml.map_xml(load_xml, IN)

con = sqlite3.connect(DB)
cur = con.cursor()
cur.executemany("INSERT INTO record (ident,timestamp, metadata) VALUES (?, ?, ?);", BATCH)
con.commit()
con.close()

# UPDATE DB

def load_xml(record):

	ident = record['001'].value()
	timestamp = int(time.mktime(time.strptime(record['005'].value(), '%Y%m%d%H%M%S.%f')))
	metadata = record.as_json()

	BATCH.append((ident, timestamp, metadata))

marcxml.map_xml(load_xml, IN)

con = sqlite3.connect(DB)
cur = con.cursor()
cur.executemany("INSERT INTO record (ident,timestamp, metadata) VALUES (?, ?, ?);", BATCH)
con.commit()
con.close()

