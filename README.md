
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
           - 260/264_a.txt/_b.txt file.
	   - 773t/x.txt(base RS/RD)

           - 141: if not 100a.strip(' (.smz)') == 773t.strip('[samizdat]'):
                    if not re.sub('^(.*)\(.*$'100a) == re.sub('^(.*)\[.*$', 773t):
           - 155: if 655a not in ('biografické poznámky','bibliografické poznámky','bio-bibliografické poznámky'):
           - 163: if 506 in meta: if a in 506: if 506a == 163: pass
           - 164: fix (Foo, Bar); 110a == 245c [=(.*)]: pass

           - List all DISABLED rules
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

