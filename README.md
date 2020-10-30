
DESCRIPTION

Library support software.

BACKUP
<pre>
crontab -e
0 0     * * *   cd ~/UCL && git add . && git commit -m "Git auto backup." && git push origin master >> ~/git.log 2>&1 &
</pre>
FILE
<pre>
 oai-marc/ - OAI-PMH 2.0 MARCXML record tools.
json-marc/ - Custom JSON to MARCXML convertor.
 locative/ - Get word's locative with <a href="https://lindat.mff.cuni.cz/services/morphodita/">LINDAT MorphoDiTa</a> REST API.
 korektor/ - Spellcheck with <a href="https://lindat.mff.cuni.cz/services/korektor/">LINDAT Korektor</a> REST API.
</pre>
SOURCE

https://github.com/KyomaHooin/UCL

