#!/usr/bin/python3
#
# Vufind - Export format module
#

from datetime import datetime

# PDF
from reportlab.pdfgen.canvas import Canvas
from reportlab.lib import pagesizes
from reportlab.lib.colors import lightgrey
#from reportlab.lib.styles import ParagraphStyle
from reportlab.platypus import Spacer,Paragraph,Table,Frame
from reportlab.graphics import renderPDF
from reportlab.pdfbase import pdfmetrics
from reportlab.pdfbase.ttfonts import TTFont
from svglib.svglib import svg2rlg

# VAR

LOGO='/usr/local/bin/export/logo.svg'
ARROW='/usr/local/bin/export/arrow-right-short.svg'
HEADER='Česká literární bibliografie'
WARN="Využitím zdrojů České literární bibliografie se uživatel zavazuje odkázat na její využití v každé publikaci, kvalifikační práci či jiném výstupu, a to následující formou: 'Při vzniku práce [knihy/studie/...] byly využity zdroje výzkumné infrastruktury Česká literární bibliografie – https://clb.ucl.cas.cz/ (kód ORJ: 90136)."
FOOT='Činnost výzkumné infrastruktury České literární bibliografie je od roku 2016 podporována Ministerstvem školství, mládeže a tělovýchovy v rámci aktivit na podporu výzkumných infrastruktur (kódy projektů LM2015059 a LM2018136).'
ADDRESS='Česká literární bibliografie © ' + datetime.now().strftime('%Y') +  ' clb@ucl.cas.cz Na Florenci, 1420/3, 110 00 Praha'

# DEF

def name_to_upper(name):
	n = name.strip(', ')
	if len(n.split(',')) == 2:
		return n.split(',')[0].strip().upper() + ', ' + n.split(',')[1].strip() 
	return n

def scale(drawing, scale_factor):
    sx = scale_factor
    sy = scale_factor
    drawing.width = drawing.minWidth() * sx
    drawing.height = drawing.height * sy
    drawing.scale(sx, sy)
    return drawing

def get_card(record):
	ret=[]
	if 'info_resource_str_mv' in record:
		ret.append(Paragraph('<para align="right"><font name="OpenSans-Regular" size="8">' + record['info_resource_str_mv'][0] + ', ' + record['id'] + '</font></para>'))
	else:
		ret.append(Paragraph('<para align="right"><font name="OpenSans-Regular" size="8">' + record['id'] + '</font></para>'))
	ret.append(Spacer(1,15))
	if 'export_100a_str' in record:
		ret.append(Paragraph('<font name="OpenSans-Bold">' + name_to_upper(record['export_100a_str']) + '</font>'))
		if 'export_100bc_str' in record:
			ret.append(Paragraph('<font name="OpenSans-Regular">' + record['export_100bc_str'] + '</font>'))
		ret.append(Spacer(1,15))
	if 'export_245_str' in record:
		ret.append(Paragraph('<font name="OpenSans-Regular">' + record['export_245_str'] + '</font>'))
	if 'export_260264_str_mv' in record:
		ret.append(Paragraph('<font name="OpenSans-Regular">' + ' '.join(record['export_260264_str_mv']) + '</font>'))
	if 'export_490_str_mv' in record:
		ret.append(Paragraph('<font name="OpenSans-Regular">' + ' '.join(record['export_490_str_mv']) + '</font>'))
	if 'article_resource_str_mv' in record:
		if 'export_773g_str_mv' in record:
			ret.append(Paragraph('<font name="OpenSans-Regular">In: ' +
				' '.join(record['article_resource_str_mv']) +
				'. ' +
				' '.join(record['export_773g_str_mv']) +
			'</font>'))
		else:
			ret.append(Paragraph('<font name="OpenSans-Regular">In: ' + ' '.join(record['article_resource_str_mv']) + '</font>'))
	if 'export_520a_str_mv' in record:
		ret.append(Paragraph('<font name="OpenSans-Regular">[' + ' '.join(record['export_520a_str_mv']) + ']</font>'))
	if 'export_6xx_str_mv' in record:
		ret.append(Paragraph('<font name="OpenSans-Regular">-&gt;' + ' '.join(record['export_6xx_str_mv']) + '</font>'))
	if 'export_787_str_mv' in record:
		for sub in record['export_787_str_mv']:
			ret.append(Paragraph('<font name="OpenSans-Regular">-&gt;' + sub + '</font>'))
	return ret

def get_docx(data):
	return io.BytesIO(b'Boo!')

def get_pdf(data):
	ret = io.BytesIO()
	logo = svg2rlg(LOGO)
	# init
	pdf = Canvas(ret, pagesize=pagesizes.A4)
	pdf.setTitle('ČLB - Vufind')
	pdfmetrics.registerFont(TTFont('OpenSans-Regular', 'OpenSans-Regular.ttf'))
	pdfmetrics.registerFont(TTFont('OpenSans-Bold', 'OpenSans-Bold.ttf'))
	# header
	renderPDF.draw(scale(logo, 1/16), pdf, 10, 820)
	pdf.setFont('OpenSans-Bold', 10)
	pdf.drawString(35, 820, HEADER)
	pdf.setLineWidth(0)
	pdf.setStrokeColor(lightgrey)
	pdf.line(10, 810, 585, 810)
	# data
	frame = Frame(60, 40, 470, 750)
	for record in data['response']['docs']:
		#print(record)
		data = [[[get_card(record)]]]
		if frame.add(Table(data, style=[('BOX', (0,0), (0,0), 0, lightgrey)]), pdf) == 0:
			pdf.showPage()
			pdf.setLineWidth(0)
			pdf.setStrokeColor(lightgrey)
			frame = Frame(60, 40, 470, 750)
			frame.add(Table(data, style=[('BOX', (0,0), (0,0), 0, lightgrey)]), pdf)
		frame.add(Spacer(1,20), pdf)

	# footer
	pdf.setLineWidth(0)
	pdf.setStrokeColor(lightgrey)
	pdf.line(10, 25, 585, 25)
	pdf.setFont('OpenSans-Regular', 8)
	pdf.drawString(15, 10, ADDRESS)
	# write
	pdf.save()
	ret.seek(0)
	print('[*] PDF.')
	return ret

def get_docx(data):
	return io.BytesIO(b'Boo!')

