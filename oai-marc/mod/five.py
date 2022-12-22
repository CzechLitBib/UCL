#!/usr/bin/python3
#
# 5xx module
#

import smtplib,urllib,io,re

from datetime import datetime,timedelta
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart

# DEF -------------------

def notify(buff,db,sif_email_map):
	MAIL_CONF = db.execute("SELECT username,passwd,server FROM email;").fetchone()

	body = ('<br>Dobrý den,<br><br>V příloze naleznete seznam všech textových polí z vašich záznamů za poslední měsíc.<br><br>' +
		'Prosíme o jazykovou kontrolu (např. kontrola gramatiky ve Wordu) a opravu.<br><br>---------------------------<br><br>' +
		'TATO ZPRÁVA BYLA VYGENEROVÁNA AUTOMATICKY, NEODPOVÍDEJTE NA NI.<br>'
		)
	for sif in buff:
		# SEEK
		buff[sif].seek(0)
		# MULTIPART
		msg = MIMEMultipart()
		msg.attach(MIMEText(body, 'html', 'utf-8'))
		att = MIMEText(buff[sif].read(), _charset='utf-8')
		att['Content-Disposition'] = "attachment; filename*=utf-8''" + urllib.parse.quote(('texty_' + (datetime.today().replace(day=1)-timedelta(days=1)).strftime('%Y%m') + '.csv'))
		msg.attach(att)
		msg['Subject'] = 'Kontrolní zpráva - Texty'
		msg['From'] = 'Kontrola MARC <' + MAIL_CONF['username'] + '>'
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

	# IO data buffer
	buff={}
	# SIF email map
	sif_email_map = dict(db.execute("SELECT code,email FROM user WHERE email != '';").fetchall())

	for record in DATA:

		# ident
		if '001' in record:
			IDENT = record['001'].value()
		else:
			continue
		# NEW ONLY
		for F in record.get_fields('CAT','KAT')[:1]:# first CAT/KAT
			if 'c' in F and F['c'][0:6] == (datetime.today().replace(day=1)-timedelta(days=1)).strftime('%Y%m'): break
		else: continue
		# SIF
		SIF = ''
		if 'SIF' in record and 'a' in record['SIF']: SIF = record['SIF']['a']
		# DATA
		for tag,value in [(F.tag, F.value()) for F in record.fields]:
			if tag != '599' and re.match('(245|246|5..)', tag):
				if SIF in sif_email_map:
					if SIF not in buff:
						buff[SIF] = io.StringIO()
						buff[SIF].write('\ufeff')# UTF-8 BOM
					buff[SIF].write(IDENT + ';' + tag + ';' + value + "\n")

	# Notify
	notify(buff,db,sif_email_map)

