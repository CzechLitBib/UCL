#!/usr/bin/python3
#
# 5xx module
#

import smtplib,urllib,io,re

from datetime import datetime,timedelta
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart

# VAR -------------------

SIF_CODE='/usr/local/bin/code/sif.txt'

MAIL_SENDER='xxx'
MAIL_SERVER='xxx'
MAIL_SERVER_BACKUP='xxx'

# DEF -------------------

def notify(buff,sif_code):
	body = ('<br>Dobrý den,<br><br>V příloze naleznete seznam všech textových polí z vašich záznamů za poslední měsíc.<br><br>' +
		'Prosíme o jazykovou kontrolu (např. kontrola gramatiky ve Wordu) a opravu.<br><br>---------------------------<br><br>' +
		'TATO ZPRÁVA BYLA VYGENEROVÁNA AUTOMATICKY, NEODPOVÍDEJTE NA NI.<br>'
		)
	for SIF in buff:
		# rewind
		buff[SIF].seek(0)
		# send buffer as CSV attachment
		msg = MIMEMultipart()
		msg.attach(MIMEText(body, 'html', 'utf-8'))
		att = MIMEText(buff[SIF].read(), _charset='utf-8')
		att['Content-Disposition'] = "attachment; filename*=utf-8''" + urllib.parse.quote((SIF + '.csv'))
		msg.attach(att)
		msg['Subject'] = 'Kontrolní zpráva - texty'
		msg['From'] = 'Kontrola MARC <' + MAIL_SENDER + '>'
		msg['To'] = sif_code[SIF]
		try:
			s = smtplib.SMTP(MAIL_SERVER, timeout=10)
			s.sendmail(MAIL_SENDER sif_code[SIF], msg.as_string())
			s.quit()
		except:
			try:
				s = smtplib.SMTP(MAIL_SERVER_BACKUP, timeout=10)
				s.sendmail(MAIL_SENDER, sif_code[SIF], msg.as_string())
				s.quit()
			except:
				print('Sendmail error.')

def sif():
	try:
		sif_code = {}
		with open(SIF_CODE, 'r') as f:
			for line in f:
				acct_code, acct_addr = line.split(':')
				sif_code[acct_code] = acct_addr.strip()
		return sif_code
	except:
		return {}

def run(DATA):

	# IO data list
	buff={}

	# SIF code list
	sif_code = sif()

	for record in DATA:

		# only newly created
		OUTDATE=False
		for F in record.get_fields('CAT','KAT')[:1]:# first CAT/KAT
			if 'c' in F:
				CAT_DATE = datetime.strptime(F['c'], '%Y%m%d')
				# 1st day this month
				if CAT_DATE >= datetime.today().replace(day=1): OUTDATE=True
				# 1st day prev. month 
				if CAT_DATE < (datetime.today().replace(day=1) - timedelta(days=1)).replace(day=1): OUTDATE=True 
		if OUTDATE: continue

		# ident
		if '001' in record:
			IDENT = record['001'].value()
		else:
			continue

		# SIF
		SIF = ''
		if 'SIF' in record and 'a' in record['SIF']:
			SIF = record['SIF']['a'].lower()

		# data
		for TAG, VALUE in [(f.tag, f.value()) for f in record.fields]:
			if TAG != '599' and re.match('(245|246|5..)', TAG):
				if SIF in sif_code:
					if SIF not in buff: buff[SIF] = io.StringIO()
					buff[SIF].write(IDENT + ';' + SIF + ';' + VALUE + "\n")

	# Notify
	notify(buff,sif_code)

