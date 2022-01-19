#!/usr/bin/python3

import rrdtool

DATABASE='vufind.rrd'

rrdtool.create(DATABASE,
	"--step", "3600", "--start", "now",	# 1hour update from now
	"DS:Aleph:GAUGE:4200:0:U",		# Data Source 10min delay heartbeat, min/max 0/undef.
	"RRA:LAST:0.5:1:288",			# 1-day "Roud Robin Archive"
	"RRA:LAST:0.5:3:672",			# 1-week
	"RRA:LAST:0.5:12:744",			# 1-month
	"RRA:LAST:0.5:72:1480",			# 1-year
	"RRA:MAX:0.5:1:288",
	"RRA:MAX:0.5:3:672",
	"RRA:MAX:0.5:12:744",
	"RRA:MAX:0.5:72:1480",
	"RRA:AVERAGE:0.5:1:288",
	"RRA:AVERAGE:0.5:3:672",
	"RRA:AVERAGE:0.5:12:744",
	"RRA:AVERAGE:0.5:72:1480"
)
