#!/bin/bash
#
# OAI-PMH 2.0 MARCXML Yearly statistic.
#


DATA_PATH='/var/www/html/year'
ROOT_PATH='/var/www/html'

HEADER='<html>
<head><meta charset="utf-8"></head>
<body style="background-color:#000;color:#6DAE42;">
<br>
<b>OAI-PMH 2.0 MARCXML</b><br>
<b>--------------------------------</b><br><br>
<b>[<font color="gold">2019</font>]</b><br><br>
<table>
<tr>
<td>
<table>
<tr><td align="right"><font color="white"><b>měsíc</b></font>:</td></tr>
<tr><td align="right"><font color="white"><b>celkem</b></font>:</td></tr>
<tr><td align="right"><font color="white"><b>chyba</b></font>:</td></tr>
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
<tr><td align="right" width="80"><a style="color:#6DAE42;" href="year/MNT.html">MONTH</a></td></tr>
<tr><td align="right" width="80"><font color="white">TOTAL</font></td></tr>
<tr><td align="right" width="80"><font color="white">MATCH</font></td></tr>
</table>
</td>'

# INIT -------------------

mkdir $DATA_PATH 2>/dev/null
rm "$DATA_PATH/*.html" 2>/dev/null
echo "$HEADER" >> "$ROOT_PATH/index.html"

# MAIN --------------------

./oai-marc.py --set UCLA --from '2019-01-01 00:00:00' --until '2019-02-01 00:00:00' --check > stat.log 2>&1
mv oai-marc.html "$DATA_PATH/jan.html"
echo "$TEMPLATE" | \
	sed "s/MNT/jan/" | \
	sed "s/MONTH/Leden/" | \
	sed "s/TOTAL/$(grep TOTAL stat.log | cut -d\  -f2)/" | \
	sed "s/MATCH/$(grep MATCH stat.log | cut -d\  -f2)/" >> "$ROOT_PATH/index.html"
rm stat.log
sleep 10

./oai-marc.py --set UCLA --from '2019-02-01 00:00:00' --until '2019-03-01 00:00:00' --check > stat.log 2>&1
mv oai-marc.html "$DATA_PATH/feb.html"
echo "$TEMPLATE" | \
	sed "s/MNT/feb/" | \
	sed "s/MONTH/Únor/" | \
	sed "s/TOTAL/$(grep TOTAL stat.log | cut -d\  -f2)/" | \
	sed "s/MATCH/$(grep MATCH stat.log | cut -d\  -f2)/" >> "$ROOT_PATH/index.html"
rm stat.log
sleep 10

./oai-marc.py --set UCLA --from '2019-03-01 00:00:00' --until '2019-04-01 00:00:00' --check > stat.log 2>&1
mv oai-marc.html "$DATA_PATH/mar.html"
echo "$TEMPLATE" | \
	sed "s/MNT/mar/" | \
	sed "s/MONTH/Březen/" | \
	sed "s/TOTAL/$(grep TOTAL stat.log | cut -d\  -f2)/" | \
	sed "s/MATCH/$(grep MATCH stat.log | cut -d\  -f2)/" >> "$ROOT_PATH/index.html"
rm stat.log
sleep 10

./oai-marc.py --set UCLA --from '2019-04-01 00:00:00' --until '2019-05-01 00:00:00' --check > stat.log 2>&1
mv oai-marc.html "$DATA_PATH/apr.html"
echo "$TEMPLATE" | \
	sed "s/MNT/apr/" | \
	sed "s/MONTH/Duben/" | \
	sed "s/TOTAL/$(grep TOTAL stat.log | cut -d\  -f2)/" | \
	sed "s/MATCH/$(grep MATCH stat.log | cut -d\  -f2)/" >> "$ROOT_PATH/index.html"
rm stat.log
sleep 10

./oai-marc.py --set UCLA --from '2019-05-01 00:00:00' --until '2019-06-01 00:00:00' --check > stat.log 2>&1
mv oai-marc.html "$DATA_PATH/may.html"
echo "$TEMPLATE" | \
	sed "s/MNT/may/" | \
	sed "s/MONTH/Květen/" | \
	sed "s/TOTAL/$(grep TOTAL stat.log | cut -d\  -f2)/" | \
	sed "s/MATCH/$(grep MATCH stat.log | cut -d\  -f2)/" >> "$ROOT_PATH/index.html"
rm stat.log
sleep 10

./oai-marc.py --set UCLA --from '2019-06-01 00:00:00' --until '2019-07-01 00:00:00' --check > stat.log 2>&1
mv oai-marc.html "$DATA_PATH/jun.html"
echo "$TEMPLATE" | \
	sed "s/MNT/jun/" | \
	sed "s/MONTH/Červen/" | \
	sed "s/TOTAL/$(grep TOTAL stat.log | cut -d\  -f2)/" | \
	sed "s/MATCH/$(grep MATCH stat.log | cut -d\  -f2)/" >> "$ROOT_PATH/index.html"
rm stat.log
sleep 10

./oai-marc.py --set UCLA --from '2019-07-01 00:00:00' --until '2019-08-01 00:00:00' --check > stat.log 2>&1
mv oai-marc.html "$DATA_PATH/jul.html"
echo "$TEMPLATE" | \
	sed "s/MNT/jul/" | \
	sed "s/MONTH/Červenec/" | \
	sed "s/TOTAL/$(grep TOTAL stat.log | cut -d\  -f2)/" | \
	sed "s/MATCH/$(grep MATCH stat.log | cut -d\  -f2)/" >> "$ROOT_PATH/index.html"
rm stat.log
sleep 10

./oai-marc.py --set UCLA --from '2019-08-01 00:00:00' --until '2019-09-01 00:00:00' --check > stat.log 2>&1
mv oai-marc.html "$DATA_PATH/aug.html"
echo "$TEMPLATE" | \
	sed "s/MNT/aug/" | \
	sed "s/MONTH/Srpen/" | \
	sed "s/TOTAL/$(grep TOTAL stat.log | cut -d\  -f2)/" | \
	sed "s/MATCH/$(grep MATCH stat.log | cut -d\  -f2)/" >> "$ROOT_PATH/index.html"
rm stat.log
sleep 10

./oai-marc.py --set UCLA --from '2019-09-01 00:00:00' --until '2019-10-01 00:00:00' --check > stat.log 2>&1
mv oai-marc.html "$DATA_PATH/sep.html"
echo "$TEMPLATE" | \
	sed "s/MNT/sep/" | \
	sed "s/MONTH/Září/" | \
	sed "s/TOTAL/$(grep TOTAL stat.log | cut -d\  -f2)/" | \
	sed "s/MATCH/$(grep MATCH stat.log | cut -d\  -f2)/" >> "$ROOT_PATH/index.html"
rm stat.log
sleep 10

./oai-marc.py --set UCLA --from '2019-10-01 00:00:00' --until '2019-11-01 00:00:00' --check > stat.log 2>&1
mv oai-marc.html "$DATA_PATH/oct.html"
echo "$TEMPLATE" | \
	sed "s/MNT/oct/" | \
	sed "s/MONTH/Říjen/" | \
	sed "s/TOTAL/$(grep TOTAL stat.log | cut -d\  -f2)/" | \
	sed "s/MATCH/$(grep MATCH stat.log | cut -d\  -f2)/" >> "$ROOT_PATH/index.html"
rm stat.log
sleep 10

./oai-marc.py --set UCLA --from '2019-11-01 00:00:00' --until '2019-12-01 00:00:00' --check > stat.log 2>&1
mv oai-marc.html "$DATA_PATH/nov.html"
echo "$TEMPLATE" | \
	sed "s/MNT/nov/" | \
	sed "s/MONTH/Listopad/" | \
	sed "s/TOTAL/$(grep TOTAL stat.log | cut -d\  -f2)/" | \
	sed "s/MATCH/$(grep MATCH stat.log | cut -d\  -f2)/" >> "$ROOT_PATH/index.html"
rm stat.log
sleep 10

./oai-marc.py --set UCLA --from '2019-12-01 00:00:00' --until '2020-01-01 00:00:00' --check > stat.log 2>&1
mv oai-marc.html "$DATA_PATH/dec.html"
echo "$TEMPLATE" | \
	sed "s/MNT/dec/" | \
	sed "s/MONTH/Prosinec/" | \
	sed "s/TOTAL/$(grep TOTAL stat.log | cut -d\  -f2)/" | \
	sed "s/MATCH/$(grep MATCH stat.log | cut -d\  -f2)/" >> "$ROOT_PATH/index.html"
rm stat.log

# EXIT --------------------

echo "$FOOTER" >> "$ROOT_PATH/index.html"

./stat.sh

exit 0

