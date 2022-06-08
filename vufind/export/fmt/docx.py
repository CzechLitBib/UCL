#!/usr/bin/python3
#
# Vufind - Export DOCX format module
#

from io import BytesIO
from datetime import datetime

#DOCX
from docx import Document
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.shared import Inches
from docx.oxml.shared import OxmlElement

# VAR

LOGO='/usr/local/bin/export/logo.png'
HEADER='Česká literární bibliografie'
WARN="Využitím zdrojů České literární bibliografie se uživatel zavazuje odkázat na její využití v každé publikaci, kvalifikační práci či jiném výstupu, a to následující formou: 'Při vzniku práce [knihy/studie/...] byly využity zdroje výzkumné infrastruktury Česká literární bibliografie – https://clb.ucl.cas.cz/ (kód ORJ: 90136)."
FOOT='Činnost výzkumné infrastruktury České literární bibliografie je od roku 2016 podporována Ministerstvem školství, mládeže a tělovýchovy v&nbsp;rámci aktivit na podporu výzkumných infrastruktur (kódy projektů LM2015059 a LM2018136).'
ADDRESS='Česká literární bibliografie © ' + datetime.now().strftime('%Y') +  ' clb@ucl.cas.cz Na Florenci, 1420/3, 110 00 Praha'

# DEF

def name_to_upper(name):
	n = name.strip(', ')
	if len(n.split(',')) == 2:
		return n.split(',')[0].strip().upper() + ', ' + n.split(',')[1].strip() 
	return n

def name_to_upper(name):
	n = name.strip(', ')
	if len(n.split(',')) == 2:
		return n.split(',')[0].strip().upper() + ', ' + n.split(',')[1].strip() 
	return n

def docx(data):
	ret = BytesIO()
	# init
	doc = Document()
	# head
	#doc.add_picture(LOGO, width=Inches(1.0))
	# data
	for record in data['response']['docs']:
		if 'info_resource_str_mv' in record:
			par = doc.add_paragraph(record['info_resource_str_mv'][0] + ', ' + record['id'])
		else:
			par = doc.add_paragraph(record['id'])
		par.paragraph_format.keep_with_next = True
		par.paragraph_format.space_after = 10
		par.alignment = WD_ALIGN_PARAGRAPH.RIGHT
		if 'export_100a_str' in record:
			par = doc.add_paragraph()
			par.add_run(name_to_upper(record['export_100a_str'])).bold = True
			if 'export_100bc_str' in record:
				par.add_run(name_to_upper(record['export_100bc_str']))
			par.paragraph_format.keep_with_next = True
			par.paragraph_format.space_after = 10
		if 'export_245_str' in record:
			par = doc.add_paragraph(record['export_245_str'])
			par.paragraph_format.keep_with_next = True
			par.paragraph_format.space_after = 10
		if 'export_260264_str_mv' in record:
			par = doc.add_paragraph(record['export_260264_str_mv'])
			par.paragraph_format.keep_with_next = True
			par.paragraph_format.space_after = 10
		if 'export_490_str_mv' in record:
			par = doc.add_paragraph(record['export_490_str_mv'])
			par.paragraph_format.keep_with_next = True
			par.paragraph_format.space_after = 10
		if 'article_resource_str_mv' in record:
			par = doc.add_paragraph()
			par.add_run('In:' + ' '.join(record['article_resource_str_mv']))
			if 'export_773g_str_mv' in record:
				par.add_run('. ' + ' '.join(record['export_773g_str_mv']))
			par.paragraph_format.keep_with_next = True
			par.paragraph_format.space_after = 10
		if 'export_520a_str_mv' in record:
			par = doc.add_paragraph(' '.join(record['export_520a_str_mv']))
			par.paragraph_format.keep_with_next = True
			par.paragraph_format.space_after = 10
		if 'export_6xx_str_mv' in record:
			par = doc.add_paragraph(' '.join(record['export_6xx_str_mv']))
			par.paragraph_format.keep_with_next = True
			par.paragraph_format.space_after = 10
		if 'export_787_str_mv' in record:
			for sub in record['export_787_str_mv']:
				par = doc.add_paragraph()
				par.add_run('\u279c ').bold = True
				par.add_run(sub)
				par.paragraph_format.keep_with_next = True
				par.paragraph_format.space_after = 5
		par = doc.add_paragraph()

	# foot
	#write
	doc.save(ret)
	ret.seek(0)
	print('[*] DOCX.')
	return ret

