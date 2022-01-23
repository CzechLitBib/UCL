#!/usr/bin/python
#-*- coding: utf-8 -*-
#
# Record daily monitor.
#

import smtplib,httplib,urllib,json,sys
from datetime import datetime,timedelta
from email.mime.text import MIMEText

DATA = {'fl':'id', 'q':'record_change_str_mv:' + (datetime.today() - timedelta(days=1)).strftime('%Y%m%d')}

MAIL_SENDER='xxx'
MAIL_TARGET='xxx'
MAIL_SERVER='xxx'
MAIL_SERVER_BACKUP='xxx'

def notify():
	html_text = (
		'Dobrý den,<br><br>Není k dispozici žádný záznam za přechozí den.' +
		'<br><br>---------------------------<br><br>' +
		'TATO ZPRÁVA BYLA VYGENEROVÁNA AUTOMATICKY, NEODPOVÍDEJTE NA NI.<br><br>'
	)
	msg = MIMEText(html_text, 'html', 'utf-8')
	msg['Subject'] = 'Vufind Monitor'
	msg['From'] = 'Vufind <' + MAIL_SENDER + '>'
	msg['To'] = MAIL_TARGET
	try:
		s = smtplib.SMTP(MAIL_SERVER, timeout=10)
		s.sendmail(MAIL_SENDER, MAIL_TARGET, msg.as_string())
		s.quit()
	except:
		try:
			s = smtplib.SMTP(MAIL_SERVER_BACKUP, timeout=10)
			s.sendmail(MAIL_SENDER, MAIL_TARGET, msg.as_string())
			s.quit()
		except:
			print('Sendmail error.')

try:
	con = httplib.HTTPConnection('vufind.ucl.cas.cz', '8080', timeout=10)
	con.request('GET', '/solr/biblio/select?' + urllib.urlencode(DATA))
	res = con.getresponse()
	if res.status == 200:
		rec = json.loads(res.read())
		if rec['response']['numFound'] == 0: notify()
except:
	print('Connection error.')

