
DESCRIPTION

Harverst OAI-PMH 2.0 record set and validate data.

INSTALL
<pre>
apt-get install python3-lxml python3-six [yaz-client]

<a href="https://pypi.org/project/pyoai/#files">pyoai-2.5.0.tar.gz</a>

tar xzf pyoai-2.5.0.tar.gz
cd pyoai-2.5.0

patch pyoai.patch

python3 setup.py install

<a href="https://pypi.org/project/pymarc/#files">pymarc-4.0.0.tar.gz</a>

tar xzf pymarc-4.0.0.tar.gz
cd pymarc-4.0.0
python3 setup.py install

/usr/local/bin/:

oai-7
oai-kat
oai-5xx
oai-marc
oai-citace
oai-recenze

</pre>
FILE
<pre>
        oai-marc - OAI-OMH 2.0 MARCXML record validation.
         oai-5xx - Send dumped 245/246/5xx text for correction. 
         oai-mdt - Z39.50 yaz-client wrapper.
           oai-7 - Evaluate subfield "7" data. 
         oai-kat - Evaluate field "KAT" data. 
       oai-7-xml - Evaluate subfield "7" datafrom file.
        oai-file - Debug & testing.
     oai-recenze - Subset match notify.
      oai-citace - Regular Vufind citace update.

  field.py.patch - Allow non-standard control field(FMT).
     pyoai.patch - Python3 test file patch.

     country.txt - MARC country code file.
        lang.txt - MARC language code file.
        role.txt - MARC role code file.
         sif.txt - MARC sif code file.
     recenze.csv - Data file oai-recenze.
</pre>
SOURCE

https://github.com/KyomaHooin/UCL

