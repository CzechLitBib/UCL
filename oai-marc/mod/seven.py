#!/usr/bin/python3
#
# subfield '7' module
#

import smtplib,sys,os,re

from email.mime.text import MIMEText

# VAR -------------------

EDITOR=['UCLRE', 'UCLJCH','UCLFB']

MAIL_SENDER='xxx'
MAIL_TARGET=['xxx']
MAIL_SERVER='xxx'
MAIL_SERVER_BACKUP='xxx'

LAST_MONTH=(datetime.today()-timedelta(days=1)).strftime('%y%m')
LAST_MONTH_DIR=(datetime.today()-timedelta(days=1)).strftime('%Y/%m')

BRIG='/var/www/html/nkp/data/' + LAST_MONTH_DIR
MONTHLY='/var/www/html/seven/data/' + LAST_MONTH_DIR

DATA={}
DATA_7={}
DATA_OLD={}
DATA_OLD_7={}
BRIG_DATA={}
BRIG_DATA_7={}
BRIG_DATA_OLD={}
BRIG_DATA_OLD_7={}

# DEF -------------------

def get_value(field):
	ret = []
	for sub in field:
		if sub[0] != '4':
			ret.append(sub[1].strip())
	return ' '.join(ret).encode('utf-8')

def is_worker(metadata):
	catlist = metadata.get_fields('CAT','KAT')
	out=[]
	for F in catlist:# remove all bots
		if 'a' in F and 'BATCH' not in F['a']:
			 out.append(F)
	for F in out[-1:]:# last one
		if 'a' in F:
			if F['a'] in EDITOR: return True
	return False

def notify():
	for mail in MAIL_TARGET:
		html = ('Dobrý den,<br><br>Výstupní data za uplynulý měsíc jsou dotupná na adrese:<br><br>' +
			'<a target="_blank" href="http://pokuston.ucl.cas.cz:38080/nkp/">http://pokuston.ucl.cas.cz:38080/nkp</a><br><br>' +
			'TATO ZPRÁVA BYLA VYGENEROVÁNA AUTOMATICKY,<br>NEODPOVÍDEJTE NA NI.<br>')
		msg = MIMEText(html.decode('utf-8'), 'html', 'utf-8')
		msg['Subject'] = 'UCL - Kontrolní zpráva'
		msg['From'] = 'UCL Kontrola <' + MAIL_SENDER + '>'
		msg['To'] = mail
		try:
			s = smtplib.SMTP(MAIL_SERVER, timeout=10)
			s.sendmail(MAIL_SENDER, mail, msg.as_string())
			s.quit()
		except:
			try:
				s = smtplib.SMTP(MAIL_SERVER_BACKUP, timeout=10)
				s.sendmail(MAIL_SENDER, mail, msg.as_string())
				s.quit()
			except:
				print('Sendmail error. ' + mail)

def run(records):

	for record in records:

		header = record[0]
		metadata = record[1]

		# skip deleted records
		if header.isDeleted(): continue

		aleph = re.sub('^.*-(\d+)$', '\\1', header.identifier()).encode('utf-8')

		# 1xx/6xx-653/700/710/711/730 ------------------
		for tag, value, seven in [(f.tag, get_value(f), f['7']) for f in metadata.fields]:
			if re.match('(100|110|111|600|610|611|700|710|711)', tag):
				# NEW
				if re.match(LAST_MONTH, metadata['008'].value()[0:4]):
					if not seven and value:
						if is_worker(metadata):
							if tag not in BRIG_DATA: BRIG_DATA[tag] = {}
							if value not in BRIG_DATA[tag]: BRIG_DATA[tag][value] = []
							BRIG_DATA[tag][value].append(aleph)
						if tag not in DATA: DATA[tag] = {}
						if value not in DATA[tag]: DATA[tag][value] = []
						DATA[tag][value].append(aleph)
					if seven and value:
						if is_worker(metadata):
							if tag not in BRIG_DATA_7: BRIG_DATA_7[tag] = {}
							if value not in BRIG_DATA_7[tag]: BRIG_DATA_7[tag][value] = []
							BRIG_DATA_7[tag][value].append(aleph)
						if tag not in DATA_7: DATA_7[tag] = {}
						if value not in DATA_7[tag]: DATA_7[tag][value] = []
						DATA_7[tag][value].append(aleph)
				# OLD
				else:
					if not seven and value:
						if is_worker(metadata):
							if tag not in BRIG_DATA_OLD: BRIG_DATA_OLD[tag] = {}
							if value not in BRIG_DATA_OLD[tag]: BRIG_DATA_OLD[tag][value] = []
							BRIG_DATA_OLD[tag][value].append(aleph)
						if tag not in DATA_OLD: DATA_OLD[tag] = {}
						if value not in DATA_OLD[tag]: DATA_OLD[tag][value] = []
						DATA_OLD[tag][value].append(aleph)
					if seven and value:
						if is_worker(metadata):
							if tag not in BRIG_DATA_OLD_7: BRIG_DATA_OLD_7[tag] = {}
							if value not in BRIG_DATA_OLD_7[tag]: BRIG_DATA_OLD_7[tag][value] = []
							BRIG_DATA_OLD_7[tag][value].append(aleph)
						if tag not in DATA_OLD_7: DATA_OLD_7[tag] = {}
						if value not in DATA_OLD_7[tag]: DATA_OLD_7[tag][value] = []
						DATA_OLD_7[tag][value].append(aleph)


	ALL = 0
	ALL_7 = 0

	BRIG_ALL = 0
	BRIG_ALL_7 = 0

	for TAG in ['100','110','111','600','610','611','700','710','711']:

		TYPE='Author'

		CNT = 0
		CNT_7 = 0

		BRIG_CNT = 0
		BRIG_CNT_7 = 0

		# NO SEVEN

		SORT = {}
		BRIG_SORT = {}

		csv = open(MONTHLY + TAG + '.csv', 'a')
		brig_csv = open(BRIG + TAG + '.csv', 'a')
		srt_csv = open(MONTHLY + TAG + '.stat.csv', 'a')
		brig_srt_csv = open(BRIG + TAG + '.stat.csv', 'a')

		# gen html and csv
		if TAG in DATA:
			for value in DATA[TAG]:
				SORT[value] = len(DATA[TAG][value])
				for aleph in DATA[TAG][value]:
					csv.write(value + '||' + aleph + '\n')
					CNT+=1
		csv.close()
		# brig gen html and csv
		if TAG in BRIG_DATA:
			for value in BRIG_DATA[TAG]:
				BRIG_SORT[value] = len(BRIG_DATA[TAG][value])
				for aleph in BRIG_DATA[TAG][value]:
					brig_csv.write(value + '||' + aleph + '\n')
					BRIG_CNT+=1
		brig_csv.close()
		# gen stat
		if re.match('6..', TAG): TYPE='Subject'
		if re.match('630', TAG): TYPE='LinkedResource'
		if re.match('655', TAG): TYPE='Genre'
		for value in sorted(SORT, key=SORT.get, reverse=True):
			srt_csv.write(str(SORT[value]) + '||' + value + '\n')
		srt_csv.close()
		# brig gen stat
		if re.match('6..', TAG): TYPE='Subject'
		if re.match('630', TAG): TYPE='LinkedResource'
		if re.match('655', TAG): TYPE='Genre'
		for value in sorted(BRIG_SORT, key=BRIG_SORT.get, reverse=True):
			brig_srt_csv.write(str(BRIG_SORT[value]) + '||' + value + '\n')
		brig_srt_csv.close()

		# SEVEN

		SORT = {}
		BRIG_SORT = {}

		csv = open(MONTHLY + TAG + '.7.csv', 'a')
		brig_csv = open(BRIG + TAG + '.7.csv', 'a')
		srt_csv = open(MONTHLY + TAG + '.7.stat.csv', 'a')
		brig_srt_csv = open(BRIG + TAG + '.7.stat.csv', 'a')
	
		# gen html and csv
		if TAG in DATA_7:
			for value in DATA_7[TAG]:
				SORT[value] = len(DATA_7[TAG][value])
				for aleph in DATA_7[TAG][value]:
					csv.write(value + '||' + aleph + '\n')
					CNT_7+=1
		csv.close()
		# brig gen html and csv
		if TAG in BRIG_DATA_7:
			for value in BRIG_DATA_7[TAG]:
				BRIG_SORT[value] = len(BRIG_DATA_7[TAG][value])
				for aleph in BRIG_DATA_7[TAG][value]:
					brig_csv.write(value + '||' + aleph + '\n')
					BRIG_CNT_7+=1
		brig_csv.close()
		# gen stat
		if re.match('6..', TAG): TYPE='Subject'
		if re.match('630', TAG): TYPE='LinkedResource'
		if re.match('655', TAG): TYPE='Genre'
		for value in sorted(SORT, key=SORT.get, reverse=True):
			srt_csv.write(str(SORT[value]) + '||' + value + '\n')
		srt_csv.close()
		# brig gen stat
		if re.match('6..', TAG): TYPE='Subject'
		if re.match('630', TAG): TYPE='LinkedResource'
		if re.match('655', TAG): TYPE='Genre'
		for value in sorted(BRIG_SORT, key=BRIG_SORT.get, reverse=True):
			brig_srt_csv.write(str(BRIG_SORT[value]) + '||' + value + '\n')
		brig_srt_csv.close()

		# global counter
		ALL+=CNT
		ALL_7+=CNT_7
		BRIG_ALL+=BRIG_CNT
		BRIG_ALL_7+=BRIG_CNT_7


	ALL = 0
	ALL_7 = 0

	BRIG_ALL = 0
	BRIG_ALL_7 = 0

	for TAG in ['100','110','111','600','610','611','700','710','711']:

		TYPE='Author'

		CNT = 0
		CNT_7 = 0

		BRIG_CNT = 0
		BRIG_CNT_7 = 0

		# NO SEVEN

		SORT = {}
		BRIG_SORT = {}

		csv = open(MONTHLY + TAG + '.old.csv', 'a')
		brig_csv = open(BRIG + TAG + '.old.csv', 'a')
		srt_csv = open(MONTHLY + TAG + '.old.stat.csv', 'a')
		brig_srt_csv = open(BRIG + TAG + '.old.stat.csv', 'a')

		# gen html and csv
		if TAG in DATA_OLD:
			for value in DATA_OLD[TAG]:
				SORT[value] = len(DATA_OLD[TAG][value])
				for aleph in DATA_OLD[TAG][value]:
					csv.write(value + '||' + aleph + '\n')
					CNT+=1
		csv.close()
		# brig gen html and csv
		if TAG in BRIG_DATA_OLD:
			for value in BRIG_DATA_OLD[TAG]:
				BRIG_SORT[value] = len(BRIG_DATA_OLD[TAG][value])
				for aleph in BRIG_DATA_OLD[TAG][value]:
					BRIG_CNT+=1
		brig_csv.close()
		# gen stat
		if re.match('6..', TAG): TYPE='Subject'
		if re.match('630', TAG): TYPE='LinkedResource'
		if re.match('655', TAG): TYPE='Genre'
		for value in sorted(SORT, key=SORT.get, reverse=True):
			srt_csv.write(str(SORT[value]) + '||' + value + '\n')
		srt_csv.close()
		# brig gen stat
		if re.match('6..', TAG): TYPE='Subject'
		if re.match('630', TAG): TYPE='LinkedResource'
		if re.match('655', TAG): TYPE='Genre'
		for value in sorted(BRIG_SORT, key=BRIG_SORT.get, reverse=True):
			brig_srt_csv.write(str(BRIG_SORT[value]) + '||' + value + '\n')
		brig_srt_csv.close()

		# SEVEN

		SORT = {}
		BRIG_SORT = {}

		csv = open(MONTHLY + TAG + '.old.7.csv', 'a')
		brig_csv = open(BRIG + TAG + '.old.7.csv', 'a')
		srt_csv = open(MONTHLY + TAG + '.old.7.stat.csv', 'a')
		brig_srt_csv = open(BRIG + TAG + '.old.7.stat.csv', 'a')
	
		# gen html and csv
		if TAG in DATA_OLD_7:
			for value in DATA_OLD_7[TAG]:
				SORT[value] = len(DATA_OLD_7[TAG][value])
				for aleph in DATA_OLD_7[TAG][value]:
					csv.write(value + '||' + aleph + '\n')
					CNT_7+=1
		csv.close()
		# brig gen html and csv
		if TAG in BRIG_DATA_OLD_7:
			for value in BRIG_DATA_OLD_7[TAG]:
				BRIG_SORT[value] = len(BRIG_DATA_OLD_7[TAG][value])
				for aleph in BRIG_DATA_OLD_7[TAG][value]:
					brig_csv.write(value + '||' + aleph + '\n')
					BRIG_CNT_7+=1
		brig_csv.close()
		# gen stat
		if re.match('6..', TAG): TYPE='Subject'
		if re.match('630', TAG): TYPE='LinkedResource'
		if re.match('655', TAG): TYPE='Genre'
		for value in sorted(SORT, key=SORT.get, reverse=True):
			srt_csv.write(str(SORT[value]) + '||' + value + '\n')
		srt_csv.close()
		# brig gen stat
		if re.match('6..', TAG): TYPE='Subject'
		if re.match('630', TAG): TYPE='LinkedResource'
		if re.match('655', TAG): TYPE='Genre'
		for value in sorted(BRIG_SORT, key=BRIG_SORT.get, reverse=True):
			brig_srt_csv.write(str(BRIG_SORT[value]) + '||' + value + '\n')
		brig_srt_csv.close()
	
	# global counter

	ALL+=CNT
	ALL_7+=CNT_7
	BRIG_CNT+=BRIG_CNT
	BRIG_ALL_7+=BRIG_CNT_7

