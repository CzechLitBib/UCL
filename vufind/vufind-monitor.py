#!/usr/bin/python3
#
# Record "daily" update monitor.
#

import requests,smtplib

from datetime import date,timedelta
from email.mime.text import MIMEText

#DATA = {'fl':'id', 'q':'record_change_date:[' + (date.today() - timedelta(days=1)).isoformat() + ' TO ' + date.today().isoformat() + ']'}
DATA = {'fl':'id', 'q':'record_change_date:[' + (date.today() - timedelta(days=1)).strftime('%Y-%m-%dT%H:%M:%SZ') + ' TO ' + date.today().strftime('%Y-%m-%dT%H:%M:%SZ') + ']'}

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
	req = requests.get('http://localhost:8983/solr/biblio/select', params=DATA, timeout=10)
	if req.status_code == 200:
		res = req.json()
		if res['response']['numFound'] == 0: notify()
except:
	print('Connection error.')

