#!/usr/bin/python
# -*- coding: utf-8 -*-

import smtplib,time

from email.mime.text import MIMEText

MAIL_SENDER='xxx@xxx'
MAIL_SERVER='xxx'

CSV='oai-marc.csv'
SIF_CODE='sif_code.txt'

sif_list={}
sif_code = {}

#------------------------

def notify():
        html_header = 'Dobrý den,<br><br>Ve vašich záznamech byly při kontrolách nalezeny následující chyby:<br><br>'
        html_footer =('<br>Prosíme o opravu.<br>---------------------------<br><br>' +
                'TATO ZPRÁVA BYLA VYGENEROVÁNA AUTOMATICKY,<br>NEODPOVÍDEJTE NA NI.<br>')
        for sif in sif_list:
		SIF = sif.decode('utf-8').lower()
                if SIF in sif_code:# match email address
                        html_body=''
                        for aleph in sif_list[sif]:
                                for error in sif_list[sif][aleph]:
                                        html_body+=('<a target="_blank" href="https://aleph22.lib.cas.cz/F/?func=direct&doc_number=' +
                                                aleph + '&local_base=AV">' + aleph + '</a> ' + ' [' + error[0] + '] ' + error[1] + '<br>')
			msg = MIMEText((html_header + html_body + html_footer).decode('utf-8'), 'html', 'utf-8')
                        msg['Subject'] = 'Kontrolní zpráva - 2019/2020'
                        msg['From'] = 'Kontrola MARC <' + MAIL_SENDER + '>'
                      	msg['To'] = sif_code[SIF]
                        s = smtplib.SMTP(MAIL_SERVER)
                       	s.sendmail(MAIL_SENDER, sif_code[SIF], msg.as_string())
                        s.quit()
			time.sleep(0.5)
		else:
			print("Not match:" + sif.encode('utf-8'))

#------------------------

with open(CSV, 'r') as f:
	for l in f.readlines():
		d = l.split(';')
		if d[1] not in sif_list: sif_list[d[1]] = {}
       		if d[0] not in sif_list[d[1]]: sif_list[d[1]][d[0]] = []
		sif_list[d[1]][d[0]].append((d[2],d[3]))

with open(SIF_CODE, 'r') as f:
	for line in f:
		acct_code, acct_addr = line.decode('utf-8').split(':')
		sif_code[acct_code] = acct_addr.strip()

if sif_list and sif_code:
	notify()

