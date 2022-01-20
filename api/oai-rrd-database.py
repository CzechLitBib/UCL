#!/usr/bin/python3

import rrdtool

DATABASE='vufind.rrd'

rrdtool.create(DATABASE,
	'--step', '1h', '--start', 'now',
	'DS:Aleph:GAUGE:4200:0:U',	# Data Source hearbeat/min/max
	'RRA:LAST:0.5:1:2w',		# "Roud Robin Archive"
	'RRA:LAST:0.5:1:2M',
	'RRA:LAST:0.5:1:2y'
)
