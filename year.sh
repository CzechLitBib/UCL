#!/bin/bash
#
# Gen yearly stat.
#

HEADER='<html>
<head><meta charset="utf-8"></head>
<body>
<br>
<b>OAI-PMH 2.0 MARCXML</b><br>
<b>--------------------------------</b><br><br>
<b>[2019]</b><br><br>
<table>
<tr>
<td>
<table>
<tr><td align="right"><b>měsíc</b>:</td></tr>
<tr><td align="right"><b>celkem</b>:</td></tr>
<tr><td align="right"><b>chyb</b>:</td></tr>
</table>
</td>
<td>
</td>'

FOOTER='</tr>
</table>
</body>
</html>'

TEMPLATE='<td>
<table>
<tr><td align="right" width="80"><a style="text-decoration:none" href="jan.html">MONTH</a></td></tr>
<tr><td align="right" width="80">TOTAL</td></tr>
<tr><td align="right" width="80">MATCH</td></tr>
</table>
</td>'

echo $HEADER >> /var/www/html/index.html

#--------------------

./oai-marc.py --set UCLA --from '2019-01-01 00:00:00' --until '2019-01-31 23:59:59' --check > stat.log 2>&1
mv oai-marc.html /var/www/html/jan.html
echo $TEMPLATE | \
	sed "s/MONTH/Leden/" | \
	sed "s/TOTAL/$(grep TOTAL stat.log | cut -d\  -f2)/" | \
	sed "s/MATCH/$(grep MATCH stat.log | cut -d\  -f2)/" >> /var/www/html/index.html
rm stat.log
sleep 300

./oai-marc.py --set UCLA --from '2019-02-01 00:00:00' --until '2019-02-28 23:59:59' --check > stat.log 2>&1
mv oai-marc.html /var/www/html/feb.html
echo $TEMPLATE | \
	sed "s/MONTH/Únor/" | \
	sed "s/TOTAL/$(grep TOTAL stat.log | cut -d\  -f2)/" | \
	sed "s/MATCH/$(grep MATCH stat.log | cut -d\  -f2)/" >> /var/www/html/index.html
rm stat.log
sleep 300

./oai-marc.py --set UCLA --from '2019-03-01 00:00:00' --until '2019-03-31 23:59:59' --check > stat.log 2>&1
mv oai-marc.html /var/www/html/mar.html
echo $TEMPLATE | \
	sed "s/MONTH/Březen/" | \
	sed "s/TOTAL/$(grep TOTAL stat.log | cut -d\  -f2)/" | \
	sed "s/MATCH/$(grep MATCH stat.log | cut -d\  -f2)/" >> /var/www/html/index.html
rm stat.log
sleep 300

./oai-marc.py --set UCLA --from '2019-04-01 00:00:00' --until '2019-04-30 23:59:59' --check > stat.log 2>&1
mv oai-marc.html /var/www/html/apr.html
echo $TEMPLATE | \
	sed "s/MONTH/Duben/" | \
	sed "s/TOTAL/$(grep TOTAL stat.log | cut -d\  -f2)/" | \
	sed "s/MATCH/$(grep MATCH stat.log | cut -d\  -f2)/" >> /var/www/html/index.html
rm stat.log
sleep 300

/oai-marc.py --set UCLA --from '2019-05-01 00:00:00' --until '2019-05-31 23:59:59' --check > stat.log 2>&1
mv oai-marc.html /var/www/html/may.html
echo $TEMPLATE | \
	sed "s/MONTH/Květen/" | \
	sed "s/TOTAL/$(grep TOTAL stat.log | cut -d\  -f2)/" | \
	sed "s/MATCH/$(grep MATCH stat.log | cut -d\  -f2)/" >> /var/www/html/index.html
rm stat.log
sleep 300

/oai-marc.py --set UCLA --from '2019-06-01 00:00:00' --until '2019-06-30 23:59:59' --check > stat.log 2>&1
mv oai-marc.html /var/www/html/jun.html
echo $TEMPLATE | \
	sed "s/MONTH/Červen/" | \
	sed "s/TOTAL/$(grep TOTAL stat.log | cut -d\  -f2)/" | \
	sed "s/MATCH/$(grep MATCH stat.log | cut -d\  -f2)/" >> /var/www/html/index.html
rm stat.log
sleep 300

/oai-marc.py --set UCLA --from '2019-07-01 00:00:00' --until '2019-07-31 23:59:59' --check > stat.log 2>&1
mv oai-marc.html /var/www/html/jul.html
echo $TEMPLATE | \
	sed "s/MONTH/Červenec/" | \
	sed "s/TOTAL/$(grep TOTAL stat.log | cut -d\  -f2)/" | \
	sed "s/MATCH/$(grep MATCH stat.log | cut -d\  -f2)/" >> /var/www/html/index.html
rm stat.log
sleep 300

/oai-marc.py --set UCLA --from '2019-08-01 00:00:00' --until '2019-08-31 23:59:59' --check > stat.log 2>&1
mv oai-marc.html /var/www/html/aug.html
echo $TEMPLATE | \
	sed "s/MONTH/Srpen/" | \
	sed "s/TOTAL/$(grep TOTAL stat.log | cut -d\  -f2)/" | \
	sed "s/MATCH/$(grep MATCH stat.log | cut -d\  -f2)/" >> /var/www/html/index.html
rm stat.log
sleep 300

/oai-marc.py --set UCLA --from '2019-09-01 00:00:00' --until '2019-09-30 23:59:59' --check > stat.log 2>&1
mv oai-marc.html /var/www/html/sep.html
echo $TEMPLATE | \
	sed "s/MONTH/Září/" | \
	sed "s/TOTAL/$(grep TOTAL stat.log | cut -d\  -f2)/" | \
	sed "s/MATCH/$(grep MATCH stat.log | cut -d\  -f2)/" >> /var/www/html/index.html
rm stat.log
sleep 300

/oai-marc.py --set UCLA --from '2019-10-01 00:00:00' --until '2019-10-31 23:59:59' --check > stat.log 2>&1
mv oai-marc.html /var/www/html/oct.html
echo $TEMPLATE | \
	sed "s/MONTH/Říjen/" | \
	sed "s/TOTAL/$(grep TOTAL stat.log | cut -d\  -f2)/" | \
	sed "s/MATCH/$(grep MATCH stat.log | cut -d\  -f2)/" >> /var/www/html/index.html
rm stat.log
sleep 300

/oai-marc.py --set UCLA --from '2019-11-01 00:00:00' --until '2019-11-30 23:59:59' --check > stat.log 2>&1
mv oai-marc.html /var/www/html/nov.html
echo $TEMPLATE | \
	sed "s/MONTH/Listopad/" | \
	sed "s/TOTAL/$(grep TOTAL stat.log | cut -d\  -f2)/" | \
	sed "s/MATCH/$(grep MATCH stat.log | cut -d\  -f2)/" >> /var/www/html/index.html
rm stat.log
sleep 300

/oai-marc.py --set UCLA --from '2019-12-01 00:00:00' --until '2019-12-31 23:59:59' --check > stat.log 2>&1
mv oai-marc.html /var/www/html/dec.html
echo $TEMPLATE | \
	sed "s/MONTH/Prosinec/" | \
	sed "s/TOTAL/$(grep TOTAL stat.log | cut -d\  -f2)/" | \
	sed "s/MATCH/$(grep MATCH stat.log | cut -d\  -f2)/" >> /var/www/html/index.html
rm stat.log

#--------------------

echo $FOOTER >> /var/www/html/index.html

