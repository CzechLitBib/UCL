#!/usr/bin/python
# -*- coding: utf-8 -*-
#
# kramerius5.nkp.cz
# digitalniknihovna.cz/mzk
# kramerius.lib.cas.cz
#
# Kramerius linker
#
from __future__ import print_function

import requests,json,httplib

# Ceska literatura uuid:f9f595d7-4116-11e1-99e8-005056a60003

CESLIT='uuid:f9f595d7-4116-11e1-99e8-005056a60003'

DATA=''

#session = requests.Session()

#req = session.get('https://kramerius.lib.cas.cz/search/api/v5.0/item/' + CESLIT + '/children')

#if req.status_code == 200:
#	DATA = json.loads(req.text, strict=False)

#print(json.dumps(DATA, indent=2))

with open('ceslit.json', 'r') as j:
	DATA = json.loads(j.read(), strict=False)

print(json.dumps(DATA, indent=2))
