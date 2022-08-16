#!/usr/bin/python3

import sys

buff = {}

with open('KKS15_vypis_disku_150910.txt', 'r') as f:
	for line in f:
		try:
			name, fn = line.split('\\')
		except:
			broken = line.split('\\')
			name = broken[2]
			fn = broken[3]

		name = name.upper().strip()
		fn = int(fn.replace('.tif','').replace('x7','').strip())

		if name not in buff:
			buff[name] = []

		if fn not in buff[name]:
			buff[name].append(fn)
		else:
			print('Dup: ' + str(fn))

for name in buff:
	i=0
	k=sorted(buff[name])
	l=len(k)
	while True:
		if i + 1 == l: break
		if k[i+1] - k[i] != 1:
			print('Hole: ' + name + ' -> ' + str(k[i]))
		i+=1

