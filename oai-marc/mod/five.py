#!/usr/bin/python3
#
# 5xx module
#

# INCLUDE -------------------

import urllib,smtplib,sys,os,re

from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart

# VAR -------------------

SIF_CODE='/usr/local/bin/sif_code.txt'

MAIL_SENDER='xxx'
MAIL_SERVER='xxx'
MAIL_SERVER_BACKUP='xxx'

# DEF -------------------

def notify():
	body = ('Dobrý den,<br><br>V příloze naleznete seznam všech textových polí z vašich záznamů za poslední měsíc.<br><br>' +
		'Prosíme o jazykovou kontrolu (např. kontrola gramatiky ve Wordu) a opravu.<br>---------------------------<br><br>' +
		'TATO ZPRÁVA BYLA VYGENEROVÁNA AUTOMATICKY,<br>NEODPOVÍDEJTE NA NI.<br>'
		)
	for SIF in sif_code:
		try:
			f = open(OUTDIR + '/' + SIF + '.csv', 'r')
			msg = MIMEMultipart()
			msg.attach(MIMEText(body.decode('utf-8'), 'html', 'utf-8'))
			att = MIMEText(f.read(), _charset='utf-8')
			att['Content-Disposition'] = "attachment; filename*=utf-8''" + urllib.parse.quote((SIF + '.csv').encode('utf-8'))
			msg.attach(att)
			msg['Subject'] = 'Kontrolní zpráva'
			msg['From'] = 'Kontrola MARC <' + MAIL_SENDER + '>'
			msg['To'] = sif_code[SIF]
			try:
				s = smtplib.SMTP(MAIL_SERVER, timeout=10)
				s.sendmail(MAIL_SENDER, sif_code[SIF], msg.as_string())
				s.quit()
			except:
				try:
					s = smtplib.SMTP(MAIL_SERVER_BACKUP, timeout=10)
					s.sendmail(MAIL_SENDER, sif_code[SIF], msg.as_string())
					s.quit()
				except:
					print('Sendmail error.')
			f.close()
		except:
			pass
# INIT -------------------

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

def run(records):

	for record in records:

		header = record[0]
		metadata = record[1]

		# skip deleted records
		if header.isDeleted(): continue

		# SKIP NOT NEW ------------------

		OUTDATE=False
		for F in metadata.get_fields('CAT','KAT')[:1]:# first CAT/KAT
			if 'c' in F:
				CAT_DATE = date(int(F['c'][:4]), int(F['c'][4:6]), int(F['c'][6:]))
				if CAT_DATE >= date.today().replace(day=1): OUTDATE=True# 1st day this month
				if CAT_DATE < (date.today().replace(day=1) - timedelta(days=1)).replace(day=1): OUTDATE=True# 1st day prev. month 
		if OUTDATE: continue

		# 1xx/6xx-653/700/710/711/730 ------------------

		if 'SIF' in metadata:
			if 'a' in metadata['SIF']: SIF = metadata['SIF']['a'].lower()
		else:
			SIF = ''

		for TAG, VALUE in [(f.tag, f.value()) for f in metadata.fields]:
			if TAG != '599':
				if re.match('(245|246|5..)', TAG):
					if SIF in sif_code:
						continue
						# write attachment file buffer 


