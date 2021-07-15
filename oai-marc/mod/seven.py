#!/usr/bin/python3
#
# subfield '7' module
#

import smtplib,re

from email.mime.text import MIMEText

# VAR -------------------

EDITOR=['UCLRE', 'UCLJCH','UCLFB']

MAIL_SENDER='xxx'
#MAIL_TARGET=['xxx']
MAIL_TARGET=['xxx']
MAIL_SERVER='xxx'
MAIL_SERVER_BACKUP='xxx'

SEVEN='/var/www/html/seven/data/' + (datetime.today()-timedelta(days=1)).strftime('%Y/%m')
NKP='/var/www/html/nkp/data/' + (datetime.today()-timedelta(days=1)).strftime('%Y/%m')

DATA={}
DATA_7={}
DATA_OLDER={}
DATA_OLDER_7={}

BRIG_DATA={}
BRIG_DATA_7={}
BRIG_DATA_OLDER={}
BRIG_DATA_OLDER_7={}

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

def notify():
	for mail_target in MAIL_TARGET:
		html = ('<br>Dobrý den,<br><br>Výstupní data za uplynulý měsíc jsou dotupná na adrese:<br><br>' +
			'<a target="_blank" href="https://vyvoj.ucl.cas.cz/nkp">https://vyvoj.cas.cz/nkp</a><br><br>' +
			'---------------------------<br><br>TATO ZPRÁVA BYLA VYGENEROVÁNA AUTOMATICKY, NEODPOVÍDEJTE NA NI.<br>')
		msg = MIMEText(html, 'html', 'utf-8')
		msg['Subject'] = 'UCL - Kontrolní zpráva'
		msg['From'] = 'UCL Kontrola <' + MAIL_SENDER + '>'
		msg['To'] = mail
		try:
			s = smtplib.SMTP(MAIL_SERVER, timeout=10)
			s.sendmail(MAIL_SENDER, mail_target, msg.as_string())
			s.quit()
		except:
			try:
				s = smtplib.SMTP(MAIL_SERVER_BACKUP, timeout=10)
				s.sendmail(MAIL_SENDER, mail_target, msg.as_string())
				s.quit()
			except:
				print('Sendmail error.')

def run(records):

	for record in records:
	
		# ident
		ident = record['001'].value()

		# 1xx/6xx-653/700/710/711/730 ------------------
		for tag, value, seven in [(f.tag, get_value(f), f['7']) for f in record.fields]:
			if tag != '653' and re.match('(1..|6..|700|710|711|730)', tag):
				# NEW
				if record['008'].value()[:4] == (datetime.today()-timedelta(days=1)).strftime('%y%m'):
					if not seven and value:
						# BRIG
						if is_worker(record):
							if tag not in BRIG_DATA: BRIG_DATA[tag] = {}
							if value not in BRIG_DATA[tag]: BRIG_DATA[tag][value] = []
							BRIG_DATA[tag][value].append(ident)
						# DATA
						if tag not in DATA: DATA[tag] = {}
						if value not in DATA[tag]: DATA[tag][value] = []
						DATA[tag][value].append(aleph)
					if seven and value:
						# BRIG 7
						if is_worker(metadata):
							if tag not in BRIG_DATA_7: BRIG_DATA_7[tag] = {}
							if value not in BRIG_DATA_7[tag]: BRIG_DATA_7[tag][value] = []
							BRIG_DATA_7[tag][value].append(ident)
						# DATA 7
						if tag not in DATA_7: DATA_7[tag] = {}
						if value not in DATA_7[tag]: DATA_7[tag][value] = []
						DATA_7[tag][value].append(ident)
				# OLDER
				else:
					if not seven and value:
						if is_worker(record):
							# BRIG OLDER
							if tag not in BRIG_DATA_OLD: BRIG_DATA_OLD[tag] = {}
							if value not in BRIG_DATA_OLD[tag]: BRIG_DATA_OLD[tag][value] = []
							BRIG_DATA_OLD[tag][value].append(ident)
						# DATA OLDER
						if tag not in DATA_OLD: DATA_OLD[tag] = {}
						if value not in DATA_OLD[tag]: DATA_OLD[tag][value] = []
						DATA_OLD[tag][value].append(ident)
					if seven and value:
						# BRIG OLDER 7
						if is_worker(metadata):
							if tag not in BRIG_DATA_OLD_7: BRIG_DATA_OLD_7[tag] = {}
							if value not in BRIG_DATA_OLD_7[tag]: BRIG_DATA_OLD_7[tag][value] = []
							BRIG_DATA_OLD_7[tag][value].append(ident)
						# DATA OLDER 7
						if tag not in DATA_OLD_7: DATA_OLD_7[tag] = {}
						if value not in DATA_OLD_7[tag]: DATA_OLD_7[tag][value] = []
						DATA_OLD_7[tag][value].append(ident)

	# WRITE NEW

	for TAG in ['100','110','111','600','610','611','700','710','711']:

		# NO SEVEN

		SORT = {}
		BRIG_SORT = {}

		if TAG in DATA:
			with open(MONTHLY + TAG + '.csv', 'w') as csv:
				for value in DATA[TAG]:
					SORT[value] = len(DATA[TAG][value])
					for ident in DATA[TAG][value]:
						csv.write(value + '||' + ident + '\n')
		if TAG in BRIG_DATA:
			with open(BRIG + TAG + '.csv', 'w') as brig_csv:
				for value in BRIG_DATA[TAG]:
					BRIG_SORT[value] = len(BRIG_DATA[TAG][value])
					for ident in BRIG_DATA[TAG][value]:
						brig_csv.write(value + '||' + ident + '\n')
		if SORT:
			with open(MONTHLY + TAG + '.stat.csv', 'w') as stat_csv:
				for value in sorted(SORT, key=SORT.get, reverse=True):
					stat_csv.write(str(SORT[value]) + '||' + value + '\n')
		if BRIG_SORT:
			with open(BRIG + TAG + '.stat.csv', 'w') as brig_stat_csv:
				for value in sorted(BRIG_SORT, key=BRIG_SORT.get, reverse=True):
					brig_stat_csv.write(str(BRIG_SORT[value]) + '||' + value + '\n')

		# SEVEN

		SORT = {}
		BRIG_SORT = {}

		if TAG in DATA_7:
			with open(MONTHLY + TAG + '.7.csv', 'w') as csv:
				for value in DATA_7[TAG]:
					SORT[value] = len(DATA_7[TAG][value])
					for ident in DATA_7[TAG][value]:
						csv.write(value + '||' + ident + '\n')
		if TAG in BRIG_DATA_7:
			with open(BRIG + TAG + '.7.csv', 'w') as brig_csv:
				for value in BRIG_DATA_7[TAG]:
					BRIG_SORT[value] = len(BRIG_DATA_7[TAG][value])
					for ident in BRIG_DATA_7[TAG][value]:
						brig_csv.write(value + '||' + ident + '\n')
		if SORT:
			with open(MONTHLY + TAG + '.7.stat.csv', 'w') as stat_csv:
				for value in sorted(SORT, key=SORT.get, reverse=True):
					stat_csv.write(str(SORT[value]) + '||' + value + '\n')
		if BRIG_SORT:
			with open(BRIG + TAG + '.7.stat.csv', 'w') as brig_stat_csv:
				for value in sorted(BRIG_SORT, key=BRIG_SORT.get, reverse=True):
					brig_srt_csv.write(str(BRIG_SORT[value]) + '||' + value + '\n')
	# WRITE OLDER

		# NO SEVEN

		SORT = {}
		BRIG_SORT = {}

		if TAG in DATA_OLD:
			with open(MONTHLY + TAG + '.old.csv', 'w') as csv:
				for value in DATA_OLD[TAG]:
					SORT[value] = len(DATA_OLD[TAG][value])
					for ident in DATA_OLD[TAG][value]:
						csv.write(value + '||' + ident + '\n')
		if TAG in BRIG_DATA_OLD:
			with open(BRIG + TAG + '.old.csv', 'w') as brig_csv:
				for value in BRIG_DATA_OLD[TAG]:
					BRIG_SORT[value] = len(BRIG_DATA_OLD[TAG][value])
					for ident in BRIG_DATA_OLD[TAG][value]:
						brig_csv.write(value + '||' + ident + '\n')
		if SORT:
			with open(MONTHLY + TAG + '.old.stat.csv', 'w') as stat_csv:
				for value in sorted(SORT, key=SORT.get, reverse=True):
					stat_csv.write(str(SORT[value]) + '||' + value + '\n')
		if BRIG_SORT:
			with open(BRIG + TAG + '.old.stat.csv', 'w') as brig_stat_csv:
				for value in sorted(BRIG_SORT, key=BRIG_SORT.get, reverse=True):
					brig_stat_csv.write(str(BRIG_SORT[value]) + '||' + value + '\n')

		# SEVEN

		SORT = {}
		BRIG_SORT = {}

		if TAG in DATA_OLD_7:
			with open(MONTHLY + TAG + '.old.7.csv', 'w') as csv:
				for value in DATA_OLD_7[TAG]:
					SORT[value] = len(DATA_OLD_7[TAG][value])
					for ident in DATA_OLD_7[TAG][value]:
						csv.write(value + '||' + ident + '\n')
		if TAG in BRIG_DATA_OLD_7:
			with open(BRIG + TAG + '.old.7.csv', 'w') as brig_csv:
				for value in BRIG_DATA_OLD_7[TAG]:
					BRIG_SORT[value] = len(BRIG_DATA_OLD_7[TAG][value])
					for ident in BRIG_DATA_OLD_7[TAG][value]:
						brig_csv.write(value + '||' + ident + '\n')
		if SORT:
			with open(MONTHLY + TAG + '.old.7.stat.csv', 'w') as stat_csv:
				for value in sorted(SORT, key=SORT.get, reverse=True):
					stat_csv.write(str(SORT[value]) + '||' + value + '\n')
		if BRIG_SORT:	
			with open(BRIG + TAG + '.old.7.stat.csv', 'w') as brig_stat_csv:
				for value in sorted(BRIG_SORT, key=BRIG_SORT.get, reverse=True):
					brig_stat_csv.write(str(BRIG_SORT[value]) + '||' + value + '\n')

