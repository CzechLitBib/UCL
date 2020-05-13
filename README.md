
DESCRIPTION

Library support software.

TODO
<pre>
773to856   FIX: doubles..
           FIX: rebuild 651 DGARCH
           CHECK: [\d+] URL

MARC -
           - 159/160 - fix I,II, Roman
           - 156/162 - fix [A-Z], [a-z] test + diacritic..
           - 260/264 a/b table.
           - oai-test + oai-marc -
	   - fix 773t(g)
           - 138 - Fix B80 regexp.
           - 100(600) join error code.
           - 125(ZAR) repeatability
           - 164 comma regexp fix
           - 148/149/156 CSV split fix
           - 98 error join?
           - 132 CNB regexp check.
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

