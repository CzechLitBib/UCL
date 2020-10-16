#!/usr/bin/python
# -*- coding: utf-8 -*-

from __future__ import print_function

import httplib,urllib,base64

ISSN='issn.csv'

SERVER='registrdigitalizace.cz'

URL='/soapservices/DigitizationRegistryService'

DATA='''<soapenv:Envelope
	xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
	xmlns:urn="http://registrdigitalizace.cz/soapservices">
	<soapenv:Header/>
	<soapenv:Body>
		<urn:findRecords>
			<parameters>
				<query>
					<issn>0009-0468</issn>
				</query>
				<format></format>
				<maxResults></maxResults>
			</parameters>
		</urn:findRecords>
	</soapenv:Body>
</soapenv:Envelope>'''

HEADER={
	#'Content-Type':'application/soap+xml; charset=utf-8',
	'Content-Type' : 'application/x-www-form-urlencoded',
	'Content-Length':len(DATA.encode('utf-8')),
	#'Authorization':'Basic ' + base64.encodestring('ctenar:ctenar').replace('\n','')
}

c = httplib.HTTPConnection(SERVER, '80', timeout=10)
c.request('POST', URL, urllib.urlencode({'xml':DATA}), HEADER)
r = c.getresponse()

print(r.status)
print(r.reason)
print(r.read())

#with open(ISSN, 'r') as f:
#	for line in f:
#		issn, name = line.split('||')
#		print("Trying.. " + issn) 
#		url = check_issn(issn.strip(),name.strip())

	#	for u in url:
	#		print(u)
			#print(ident + ' | ' + name1 + ' | ' + value.encode('utf-8')+ ' | ' + code)

