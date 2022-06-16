#!/usr/bin/python3
#
# Vufind - Export DOCX format module
#

from io import BytesIO
from datetime import datetime

#DOCX

from docx import Document
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.shared import Inches, Cm
from docx.oxml.shared import OxmlElement, qn
from docx.opc.constants import RELATIONSHIP_TYPE as RT

# VAR

RESOURCE = {
	'Literary Samizdat Bibliography':'Bibliografie samizdatu',
	'Literary Web Bibliography':'Bibliografie internetu',
	'Literary Exile Bibliography':'Bibliografie exilu',
	'Retrospective Bibliography (up to 1945)':'Retrospektivní bibliografie (do roku 1945)',
	'Current Bibliography (after 1945)':'Současná bibliografie (po roce 1945)',
	'ICL Catalogue':'Katalog UCL',
	'Database of Processed Journals':'Databáze excerpovaných časopisů',
	'Czech Literary Authorities Database':'České literární osobnosti',
	'Polonica in Czech Samizdat':'Polonika v českém samizdatu',
	'Database of Foreign Bohemica':'Databáze zahraničních bohemik',
	'Book Series Database':'Databáze knižních edic',
	'Czech Literature in Translation':'Databáze překladu české literatury'
}

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

def hyperlink(par, text, url):
	part = par.part# document.xml.rels => relation ID
	rid = part.relate_to(url, RT.HYPERLINK, is_external=True)

	hyperlink = OxmlElement('w:hyperlink')
	hyperlink.set(qn('r:id'), rid, )

	run = OxmlElement('w:r')
	rpr = OxmlElement('w:rPr')

	run.append(rpr)
	run.text = text

	hyperlink.append(run)
	par._p.append(hyperlink)

	return hyperlink

def docx(data, lang):
	ret = BytesIO()
	# init
	doc = Document()
	sections = doc.sections
	for section in sections:
		section.left_margin = Cm(1.5)
		section.right_margin = Cm(1.5)
	# head
	#header = sections[0].header
	#par = header.paragraphs[0]
	#run = par.add_run()
	#run.add_picture(LOGO,width=Inches(1.0))
	#par.text = HEADER
	# data
	for record in data['response']['docs']:
		par = doc.add_paragraph()
		if 'info_resource_str_mv' in record:
			resource = record['info_resource_str_mv'][0]
			if lang == 'cs': resource = RESOURCE[record['info_resource_str_mv'][0]]
			par.add_run(resource + ', ')
			hyperlink(par, record['id'], 'http://vufind2-dev.ucl.cas.cz/Record/' + record['id'])
		else:
			hyperlink(par, record['id'], 'http://vufind2-dev.ucl.cas.cz/Record/' + record['id'])
		par.paragraph_format.keep_with_next = True
		par.alignment = WD_ALIGN_PARAGRAPH.RIGHT
		if 'export_100a_str' in record:
			par = doc.add_paragraph()
			par.add_run(name_to_upper(record['export_100a_str'])).bold = True
			if 'export_100bc_str' in record:
				par.add_run(name_to_upper(record['export_100bc_str']))
			par.paragraph_format.keep_with_next = True
		if 'export_245_str' in record:
			par = doc.add_paragraph(record['export_245_str'])
			par.paragraph_format.keep_with_next = True
		if 'export_260264_str_mv' in record:
			par = doc.add_paragraph(' '.join(record['export_260264_str_mv']))
			par.paragraph_format.keep_with_next = True
		if 'export_490_str_mv' in record:
			par = doc.add_paragraph('(' + ' '.join(record['export_490_str_mv']) + ')')
			par.paragraph_format.keep_with_next = True
		if 'export_773tg_str_mv' in record:
			par = doc.add_paragraph('In: ' + record['export_773tg_str_mv'][0])
			par.paragraph_format.keep_with_next = True
			for sub in record['export_773tg_str_mv'][1:]:
				par = doc.add_paragraph(sub)
				par.paragraph_format.keep_with_next = True
		if 'export_520a_str_mv' in record:
			par = doc.add_paragraph('[' + ' '.join(record['export_520a_str_mv']) + ']')
			par.paragraph_format.keep_with_next = True
		if 'export_6xx_str_mv' in record:
			par = doc.add_paragraph()
			par.add_run('\u279c  ')
			par.add_run('; '.join(record['export_6xx_str_mv']))
			par.paragraph_format.keep_with_next = True
		if 'export_787_str_mv' in record:
			for sub in record['export_787_str_mv']:
				par = doc.add_paragraph()
				par.add_run('\u279c  ')
				par.add_run(sub)
				par.paragraph_format.keep_with_next = True
				par.paragraph_format.space_after = 5
		par = doc.add_paragraph()
	# foot
	#foot = sections[-1].footer
	#foot.text = ADDRESS
	#write
	doc.save(ret)
	ret.seek(0)
	return ret

