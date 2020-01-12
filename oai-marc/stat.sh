#!/bin/bash
#
# OAI-PMH 2.0 MARCXML Error code statistic.
#

STAT='/var/www/html/stat.html'

DATA_PATH='/var/www/html/*.html'
DATA=''

CODE_START=000
CODE_END=123

HTML_HEADER='<html>
<head><meta charset="utf-8"></head>
<body style="background-color:black;color:white;">
<br><div align="center"><table>'
HTML_FOOTER='</table></div><br>
</body>
</html>'

#-----------------

readarray -t ERROR_CODE < error_code.txt

get_error () {
	for ((i=0; i < ${#ERROR_CODE[@]};i++)); do
		CODE=$(echo "${ERROR_CODE[$i]}" | cut -c1-3);
		ERROR=$(echo "${ERROR_CODE[$i]}" | cut -c5-);
 		if [ "$1" == "$CODE" ]; then
			echo "$ERROR";
		fi
	done
}

#-----------------

rm $STAT 2>/dev/null

for I in $(eval echo {$CODE_START..$CODE_END}); do
	COUNT=$(grep ">$I<" $DATA_PATH 2>/dev/null | wc -l);
	if [ $COUNT != 0 ]; then
		DATA=$(echo -ne "$DATA\n<tr><td align='right'> $COUNT </td><td>-</td><td>[<font color='gold'>$I</font>]</td><td> $(get_error $I)</td></tr>");
	fi
done

echo "$HTML_HEADER" >$STAT
echo "$DATA" | sort -n -k3 | tac >> $STAT
echo "$HTML_FOOTER" >> $STAT

