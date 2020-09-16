#!/usr/bin/python
# -*- coding: utf-8 -*-
#
# Aleph KAT/SIF  to CSV 
#
# SysNo; SIF; 1. výskyt CAT-c rok; 1. výskyt CAT-c měsíc; CATa; CATc-rok; CATc-měsíc; CATc cele
#

from __future__ import print_function

import sys,os,re

IN='uclacatsif.bib'
OUT='uclcatsif.csv'

SIF_MAP={
'AG':'UCLAG',
'DM':'UCLDM',
'DAN':'UCLDR',
'FAP':'UCLFP',
'GR':'UCLGR',
'NÚS':'UCLJF',
'JCH':'UCLJCH',
'JHK':'UCLJK',
'JS':'UCLJS',
'KB':'UCLKB',
'LS':'UCLLS',
'LUV':'UCLLUV',
'MF':'UCLMF',
'IM':'UCLMI',
'MAK':'UCLMK',
'SKU':'UCLMS',
'PHA':'UCLPH',
'PET':'UCLPL',
'PAV':'UCLPN',
'PV':'UCLPV',
'RCE':'UCLRCE',
'RE':'UCLRE',
'SRA':'UCLST',
'TP':'UCLTP',
'VM':'UCLVM'
}

with open(IN, 'r') as f:
	for line in f:
		
