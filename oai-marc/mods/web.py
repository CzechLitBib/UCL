#!/usr/bin/python

import urllib2

def url_response(url):
	req = urllib2.Request(url)
	req.add_header('User-Agent', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:70.0) Gecko/20100101 Firefox/70.0')
	print urllib2.urlopen(req, timeout=10).getcode()

url_response('https://magazin.aktualne.cz/kultura/komiksove-signaly-z-neznama-konci-v-brne-zacnou-v-/r~i:gallery:29471/')
