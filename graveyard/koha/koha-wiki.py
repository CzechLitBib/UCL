#!/usr/bin/python
#
# MediaWiki to Koha Scrapper
#

import requests,StringIO,lxml.html,json,time,sys,re

# VAR ----------------------

IN='koha_100.csv'
OUT='koha_wiki.csv'

WIKIBASE='https://cs.wikipedia.org/w/api.php?'
WIKIPAGESEARCH='action=query&list=search&format=json&utf8=&srsearch='
WIKITEMPLATE='action=expandtemplates&text={{%C5%A0ablona:Autoritn%C3%AD_data}}&prop=wikitext&format=json&title='

AUTMAP = {
	'aleph.nkp.cz':'AUT',
	'd-nb.info':'GND',
	'isni.org':'ISNI',
	'id.loc.gov':'LCCN',
	'viaf.org':'VIAF',
	'www.worldcat.org':'WorldCat',
	'www.getty.edu':'ULAN',
	'orcid.org':'ORCID'
}

f = open(IN, 'r')
out = open(OUT, 'w')

# MAIN ----------------------

session = requests.Session()
parser = lxml.html.HTMLParser()

for line in f:
	IDENT,SEVEN = line.split(';')
	req1 = session.get(WIKIBASE + WIKIPAGESEARCH + SEVEN)
	if req1.status_code == 200:
		search = json.loads(req1.text, strict=False)
		if search['query']['searchinfo']['totalhits'] > 0:
			if IDENT == '716' or IDENT == '37538':# Karek Capek
				TITLE = search['query']['search'][1]['title']
			else:
				TITLE = search['query']['search'][0]['title']
			req2 = session.get(WIKIBASE + WIKITEMPLATE + TITLE)
			if req2.status_code == 200:
				exptpl = json.loads(req2.text, strict=False)
				TPL = exptpl['expandtemplates']['wikitext']
				t = lxml.html.parse(StringIO.StringIO(TPL), parser)
				o = t.xpath('//span')
				BUFF = IDENT 
				for span in o:
					MATCH = False
					for aut in AUTMAP:
						if re.match('.*' + aut + '.*', span.text):
							MATCH = True
							ID = re.sub('^\[[^ ]+(.*)\]$', '\\1', span.text).replace(' ','').strip()
							BUFF += ';' + AUTMAP[aut] + ';' + ID
					if not MATCH: print(IDENT + " No map: " + span.text + '\n')
				out.write(BUFF + '\n')# write!

# END ----------------------

session.close()
out.close()
f.close()

sys.exit(0)

