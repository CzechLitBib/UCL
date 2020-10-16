#!/usr/bin/python
# -*- coding: utf-8 -*-

from __future__ import print_function

ISSN='issn.txt'
RD='rd_issn_utf-8.txt'

out = open('stat.txt', 'w')
bad = open('bad.txt', 'w')


with open(ISSN) as f:
	issn={}
	for line in f:
		part = line.split('||')
		issn[part[0]] = [part[1], part[2].strip()] # ISSN, CNT, NAME

with open(RD) as f:
	rd={}
	for line in f:
		part = line.split(',')
		if part[0] not in rd:
			rd[part[0]] = [[part[2], part[3].strip()]]
		else:
			tmp = rd[part[0]]
			tmp.append([part[2], part[3].strip()])
			rd[part[0]] = tmp


for i in issn:
	print(issn[i][0] + '||' + i + '||' + issn[i][1])
	if i in rd:
		for link in rd[i]:
			out.write(issn[i][0] + '||' + i + '||' + issn[i][1]  + '||' + link[0] + '||' + link[1] + '\n')
	else:
		bad.write(i + '\n')

out.close()
bad.close()

