#!/usr/bin/python
# -*- coding: utf-8 -*-

from __future__ import print_function

ISSN='issn.txt'
RD='rd_issn.txt'

with open(ISSN) as f:
	issn=[]
	for line in f:
		issn.append(line.split('||')[0])

with open(RD) as f: rd = f.read().splitlines()

print('ISSN: ' + str(len(issn)))
print('RD: ' + str(len(rd)))

CNT=0

for i in issn:
	if i in rd:
		CNT+=1

print('IN: ' + str(CNT))
print('NO: ' + str(len(issn) - CNT))
