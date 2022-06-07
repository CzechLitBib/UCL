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

# INIT

# DEF

def prevent_document_break(document):
	tags = document.element.xpath('//w:tr')
	rows = len(tags)
	for row in range(0, rows):
		tag = tags[row]  # Specify which <w:r> tag you want
		child = OxmlElement('w:trPr.w:cantSplit')  # Create arbitrary tag
		tag.append(child)  # Append in the new tag

#def preventDocumentBreak(document):
#	tags = document.element.xpath('//w:tr')
#	rows = len(tags)
#	for row in range(0,rows):
#		tag = tags[row]
#		child = OxmlElement('w:cantSplit')
#		tag.append(child)

def name_to_upper(name):
	n = name.strip(', ')
	if len(n.split(',')) == 2:
		return n.split(',')[0].strip().upper() + ', ' + n.split(',')[1].strip() 
	return n

def card(record):
	ret=[]
	if 'info_resource_str_mv' in record:
		ret.append(Paragraph(
			'<para align="right"><font name="OpenSans-Regular" size="8">' +
			record['info_resource_str_mv'][0] + ', ' +
			record['id'] +
			'</font></para>'
		))
	else:
		ret.append(Paragraph(
			'<para align="right"><font name="OpenSans-Regular" size="8">' +
			record['id'] +
			'</font></para>'
		))
	ret.append(Spacer(1,15))
	if 'export_100a_str' in record:
		ret.append(Paragraph(
			'<font name="OpenSans-Bold">' +
			name_to_upper(record['export_100a_str']) +
			'</font>'
		))
		if 'export_100bc_str' in record:
			ret.append(Paragraph(
				'<font name="OpenSans-Regular">' +
				record['export_100bc_str'] +
				'</font>'
			))
		ret.append(Spacer(1,15))
	if 'export_245_str' in record:
		ret.append(Paragraph(
			'<font name="OpenSans-Regular">' +
			record['export_245_str'] +
			'</font>'
		))
		ret.append(Spacer(1,15))
	if 'export_260264_str_mv' in record:
		ret.append(Paragraph(
			'<font name="OpenSans-Regular">' +
			' '.join(record['export_260264_str_mv']) +
			'</font>'
		))
	if 'export_490_str_mv' in record:
		ret.append(Paragraph(
			'<font name="OpenSans-Regular">' +
			' '.join(record['export_490_str_mv']) +
			'</font>'
		))
	if 'article_resource_str_mv' in record:
		if 'export_773g_str_mv' in record:
			ret.append(Paragraph(
				'<font name="OpenSans-Regular">In: ' +
				' '.join(record['article_resource_str_mv']) +
				'. ' +
				' '.join(record['export_773g_str_mv']) +
				'</font>'
			))
		else:
			ret.append(Paragraph(
				'<font name="OpenSans-Regular">In: ' +
				' '.join(record['article_resource_str_mv']) +
				'</font>'
			))
		ret.append(Spacer(1,15))
	if 'export_520a_str_mv' in record:
		ret.append(Paragraph(
			'<font name="OpenSans-Regular">[' +
			' '.join(record['export_520a_str_mv']) +
			']</font>'
		))
		ret.append(Spacer(1,15))
	if 'export_6xx_str_mv' in record:
		ret.append(Paragraph(
			'<font name="OpenSans-Regular">' +
			' '.join(record['export_6xx_str_mv']) +
			'</font>', style=ParagraphStyle('bullet', bulletText='\u279c')
		))
	if 'export_787_str_mv' in record:
		for sub in record['export_787_str_mv']:
			ret.append(Paragraph(
				'<font name="OpenSans-Regular">' +
				sub +
				'</font>', style=ParagraphStyle('bullet', bulletText='\u279c')
			))
	return ret


def docx(data):
	#
	#
	# keep_next + cantsplit! + spacer
	#
	ret = BytesIO()
	# init
	doc = Document()
	# head
	#doc.add_picture(LOGO, width=Inches(1.0))
	# data
	for i in range(0,10):
	#	table = doc.add_table(rows=1, cols=1)
	#	table.style = 'Table Grid'
		#table.autofit = False
	#	cell = table.cell(0,0)
	#	par = cell.add_paragraph('123456789')
	#	par.alignment = WD_ALIGN_PARAGRAPH.RIGHT
	#	par.keep_with_next = True
		for j in range(0,8):
	#		par = cell.add_paragraph('text')
	#		par.keep_with_next = True
	#	par = cell.add_paragraph('last')
	#	doc.add_paragraph()
			par = doc.add_paragraph('text')
			par.paragraph_format.keep_with_next = True
		doc.add_paragraph('last')

	#table.style()
	#doc.add_page_break()
	# patch
	prevent_document_break(doc)
	# foot
	#write
	doc.save(ret)
	ret.seek(0)
	print('[*] DOCX.')
	return ret

