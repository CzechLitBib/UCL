#!/usr/bin/python3
#
# subfield '7' module
#

import smtplib,re

from datetime import datetime,timedelta
from email.mime.text import MIMEText

# VAR -------------------

EDITOR=['UCLRE', 'UCLJCH','UCLFB']
MAIL_TARGET=['vm','vr']

SEVEN='/var/www/html/seven/data/' + (datetime.today().replace(day=1)-timedelta(days=1)).strftime('%Y/%m') + '/'
NKP='/var/www/html/nkp/data/' + (datetime.today().replace(day=1)-timedelta(days=1)).strftime('%Y/%m') + '/'

REC={}
REC_7={}
REC_OLD={}
REC_OLD_7={}

BRIG_REC={}
BRIG_REC_7={}
BRIG_REC_OLD={}
BRIG_REC_OLD_7={}

# DEF -------------------

def get_value(field):
	data = []
	for sub in field:
		if sub[0] != '4':
			data.append(sub[1].strip())
	return ' '.join(data)

def is_worker(metadata):
	out=[]
	cat = metadata.get_fields('CAT','KAT')
	for F in cat:# remove all bots
		if 'a' in F and 'BATCH' not in F['a']:
			 out.append(F)
	for F in out[-1:]:# last one
		if 'a' in F and F['a'] in EDITOR:
			return True
	return False

def notify(db):
	MAIL_CONF = db.execute("SELECT username,passwd,server FROM email;").fetchone()
	sif_email_map = dict(db.execute("SELECT code,email FROM user;").fetchall())
	for sif in MAIL_TARGET:
		if sif in sif_email_map:
			html = ('<br>Dobrý den,<br><br>Výstupní data za uplynulý měsíc jsou dotupná na adrese:<br><br>' +
				'<a target="_blank" href="https://vyvoj.ucl.cas.cz/nkp">https://vyvoj.ucl.cas.cz/nkp</a><br><br>' +
				'---------------------------<br><br>TATO ZPRÁVA BYLA VYGENEROVÁNA AUTOMATICKY, NEODPOVÍDEJTE NA NI.<br>')
			msg = MIMEText(html, 'html', 'utf-8')
			msg['Subject'] = 'UCL - Kontrolní zpráva'
			msg['From'] = 'UCL Kontrola <' + MAIL_CONF['username'] + '>'
			msg['To'] = sif_email_map[sif]
			try:
				s = smtplib.SMTP(MAIL_CONF['server'], timeout=5)
				s.ehlo()
				s.starttls()
				s.login(MAIL_CONF['username'], MAIL_CONF['passwd'])
				s.sendmail(MAIL_CONF['username'], sif_email_map[sif], msg.as_string())
				s.quit()
			except Exception as e:
				print(e)

def run(DATA,db):

	# tag list
	tag_list=[]

	for record in DATA:

		# ident
		if '001' in record:
			ident = record['001'].value()
		else:
			continue

		# 1xx/6xx-653/700/710/711/730 ------------------
		for tag, value, seven in [(f.tag, get_value(f), f['7']) for f in record.fields]:
			if tag != '653' and re.match('(1..|6..|700|710|711|730)', tag):
				# update TAG list
				if tag not in tag_list: tag_list.append(tag)
				# NEW
				if record['008'].value()[:4] == (datetime.today()-timedelta(days=1)).strftime('%y%m'):
					if not seven and value:
						# BRIG
						if is_worker(record):
							if tag not in BRIG_REC: BRIG_REC[tag] = {}
							if value not in BRIG_REC[tag]: BRIG_REC[tag][value] = []
							BRIG_REC[tag][value].append(ident)
						# DATA
						if tag not in REC: REC[tag] = {}
						if value not in REC[tag]: REC[tag][value] = []
						REC[tag][value].append(ident)
					if seven and value:
						# BRIG 7
						if is_worker(record):
							if tag not in BRIG_REC_7: BRIG_REC_7[tag] = {}
							if value not in BRIG_REC_7[tag]: BRIG_REC_7[tag][value] = []
							BRIG_REC_7[tag][value].append(ident)
						# DATA 7
						if tag not in REC_7: REC_7[tag] = {}
						if value not in REC_7[tag]: REC_7[tag][value] = []
						REC_7[tag][value].append(ident)
				# OLD
				else:
					if not seven and value:
						if is_worker(record):
							# BRIG OLDER
							if tag not in BRIG_REC_OLD: BRIG_REC_OLD[tag] = {}
							if value not in BRIG_REC_OLD[tag]: BRIG_REC_OLD[tag][value] = []
							BRIG_REC_OLD[tag][value].append(ident)
						# DATA OLDER
						if tag not in REC_OLD: REC_OLD[tag] = {}
						if value not in REC_OLD[tag]: REC_OLD[tag][value] = []
						REC_OLD[tag][value].append(ident)
					if seven and value:
						# BRIG OLDER 7
						if is_worker(record):
							if tag not in BRIG_REC_OLD_7: BRIG_REC_OLD_7[tag] = {}
							if value not in BRIG_REC_OLD_7[tag]: BRIG_REC_OLD_7[tag][value] = []
							BRIG_REC_OLD_7[tag][value].append(ident)
						# DATA OLDER 7
						if tag not in REC_OLD_7: REC_OLD_7[tag] = {}
						if value not in REC_OLD_7[tag]: REC_OLD_7[tag][value] = []
						REC_OLD_7[tag][value].append(ident)

	for TAG in tag_list:
	
		# WRITE NEW

		SORT = {}
		BRIG_SORT = {}

		if TAG in REC:
			with open(SEVEN + TAG + '.csv', 'w') as csv:
				for value in REC[TAG]:
					SORT[value] = len(REC[TAG][value])
					for ident in REC[TAG][value]:
						csv.write(value + '||' + ident + '\n')
		if TAG in BRIG_REC:
			with open(NKP + TAG + '.csv', 'w') as brig_csv:
				for value in BRIG_REC[TAG]:
					BRIG_SORT[value] = len(BRIG_REC[TAG][value])
					for ident in BRIG_REC[TAG][value]:
						brig_csv.write(value + '||' + ident + '\n')
		if SORT:
			with open(SEVEN + TAG + '.stat.csv', 'w') as stat_csv:
				for value in sorted(SORT, key=SORT.get, reverse=True):
					stat_csv.write(str(SORT[value]) + '||' + value + '\n')
		if BRIG_SORT:
			with open(NKP + TAG + '.stat.csv', 'w') as brig_stat_csv:
				for value in sorted(BRIG_SORT, key=BRIG_SORT.get, reverse=True):
					brig_stat_csv.write(str(BRIG_SORT[value]) + '||' + value + '\n')

		SORT = {}
		BRIG_SORT = {}

		if TAG in REC_7:
			with open(SEVEN + TAG + '.7.csv', 'w') as csv:
				for value in REC_7[TAG]:
					SORT[value] = len(REC_7[TAG][value])
					for ident in REC_7[TAG][value]:
						csv.write(value + '||' + ident + '\n')
		if TAG in BRIG_REC_7:
			with open(NKP + TAG + '.7.csv', 'w') as brig_csv:
				for value in BRIG_REC_7[TAG]:
					BRIG_SORT[value] = len(BRIG_REC_7[TAG][value])
					for ident in BRIG_REC_7[TAG][value]:
						brig_csv.write(value + '||' + ident + '\n')
		if SORT:
			with open(SEVEN + TAG + '.7.stat.csv', 'w') as stat_csv:
				for value in sorted(SORT, key=SORT.get, reverse=True):
					stat_csv.write(str(SORT[value]) + '||' + value + '\n')
		if BRIG_SORT:
			with open(NKP + TAG + '.7.stat.csv', 'w') as brig_stat_csv:
				for value in sorted(BRIG_SORT, key=BRIG_SORT.get, reverse=True):
					brig_stat_csv.write(str(BRIG_SORT[value]) + '||' + value + '\n')

		# WRITE OLDER

		SORT = {}
		BRIG_SORT = {}

		if TAG in REC_OLD:
			with open(SEVEN + TAG + '.old.csv', 'w') as csv:
				for value in REC_OLD[TAG]:
					SORT[value] = len(REC_OLD[TAG][value])
					for ident in REC_OLD[TAG][value]:
						csv.write(value + '||' + ident + '\n')
		if TAG in BRIG_REC_OLD:
			with open(NKP + TAG + '.old.csv', 'w') as brig_csv:
				for value in BRIG_REC_OLD[TAG]:
					BRIG_SORT[value] = len(BRIG_REC_OLD[TAG][value])
					for ident in BRIG_REC_OLD[TAG][value]:
						brig_csv.write(value + '||' + ident + '\n')
		if SORT:
			with open(SEVEN + TAG + '.old.stat.csv', 'w') as stat_csv:
				for value in sorted(SORT, key=SORT.get, reverse=True):
					stat_csv.write(str(SORT[value]) + '||' + value + '\n')
		if BRIG_SORT:
			with open(NKP + TAG + '.old.stat.csv', 'w') as brig_stat_csv:
				for value in sorted(BRIG_SORT, key=BRIG_SORT.get, reverse=True):
					brig_stat_csv.write(str(BRIG_SORT[value]) + '||' + value + '\n')

		SORT = {}
		BRIG_SORT = {}

		if TAG in REC_OLD_7:
			with open(SEVEN + TAG + '.old.7.csv', 'w') as csv:
				for value in REC_OLD_7[TAG]:
					SORT[value] = len(REC_OLD_7[TAG][value])
					for ident in REC_OLD_7[TAG][value]:
						csv.write(value + '||' + ident + '\n')
		if TAG in BRIG_REC_OLD_7:
			with open(NKP + TAG + '.old.7.csv', 'w') as brig_csv:
				for value in BRIG_REC_OLD_7[TAG]:
					BRIG_SORT[value] = len(BRIG_REC_OLD_7[TAG][value])
					for ident in BRIG_REC_OLD_7[TAG][value]:
						brig_csv.write(value + '||' + ident + '\n')
		if SORT:
			with open(SEVEN + TAG + '.old.7.stat.csv', 'w') as stat_csv:
				for value in sorted(SORT, key=SORT.get, reverse=True):
					stat_csv.write(str(SORT[value]) + '||' + value + '\n')
		if BRIG_SORT:	
			with open(NKP + TAG + '.old.7.stat.csv', 'w') as brig_stat_csv:
				for value in sorted(BRIG_SORT, key=BRIG_SORT.get, reverse=True):
					brig_stat_csv.write(str(BRIG_SORT[value]) + '||' + value + '\n')

	# Notify
	notify(db)

