#!/usr/bin/python3

import sys

buff = {}

with open('KKS16_plne_27843.txt', 'r') as f:
	for line in f:
		s = line.split('-')

		#try:
		name = s[1].upper().strip()
		shelf = int(s[2])
		verse = int(s[3].replace('.tif','').replace('.png','').strip())
		#except:
		#	print(line)
		#	name = s[1].upper().strip()
		#	shelf = int(s[3])
		#	verse = int(s[4].replace('.tif','').replace('.png','').strip())
	
		if name not in buff:
			buff[name] = {}

		if shelf not in buff[name]:
			buff[name][shelf] = []
		#else:
		#	print("Dup shelf: " + name + ' -> ' + str(shelf))

		if verse not in buff[name][shelf]:
			buff[name][shelf].append(verse)
		else:
			print("Dup verse: " + name + ' -> ' + str(shelf) + ' -> ' + str(verse))

with open('KKS16_prazdne_31725.txt', 'r') as f:
	for line in f:
		s = line.split('-')

		#try:
		name = s[1].upper().strip()
		shelf = int(s[2])
		verse = int(s[3].replace('.tif','').replace('.png','').strip())
		#except:
		#	print(line)
		#	name = s[1].upper().strip()
		#	shelf = int(s[3])
		#	verse = int(s[4].replace('.tif','').replace('.png','').strip())

		if name not in buff:
			buff[name] = {}

		if shelf not in buff[name]:
			buff[name][shelf] = []
		#else:
		#	print("Dup shelf: " + name + ' -> ' + str(shelf))

		if verse not in buff[name][shelf]:
			buff[name][shelf].append(verse)
		else:
			print("Dup verse: " + name + ' -> ' + str(shelf) + ' -> ' + str(verse))

for name in buff:
	k = list(buff[name].keys())
	i=0
	l=len(k)
	while True:
		if i + 1 == l: break
		if k[i+1] - k[i] != 1:
			print('Shelf hole: ' + name + ' -> ' + str(k[i]))
		i+=1

for name in buff:
	for shelf in buff[name]:
		k = buff[name][shelf]
		if len(k) % 2 != 0:
			print('Verse modulo: ' + name + ' -> ' + str(shelf))
		i=0
		l=len(k)
		while True:
			if i + 1 == l: break
			if k[i+1] - k[i] != 1:
				print('Verse order: ' + name + ' -> ' + str(shelf))
			i+=1

