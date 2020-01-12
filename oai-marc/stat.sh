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
<body style="background-color:black;">
<pre><font size="4" color="white">'
HTML_FOOTER='</font>
</pre>
</body>
</html>'

#-----------------

for I in $(eval echo {$CODE_START..$CODE_END}); do
	COUNT=$(grep ">$I<" $DATA_PATH 2>/dev/null | wc -l)
	if [ $COUNT != 0 ]; then
		DATA=$(echo -ne "$DATA\n[$I] - $COUNT")
	fi
done

echo "$HTML_HEADER" >$STAT
echo "$DATA" | sort -n -k3 | tac >> $STAT
echo "$HTML_FOOTER" >> $STAT

