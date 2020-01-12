
DESCRIPTION

Harverst OAI-PMH 2.0 record set and validate data.

INSTALL
<pre>
apt-get install python-setuptools python-lxml python-six

<a href="https://pypi.org/project/pyoai/#files">pyoai-2.5.0.tar.gz</a>

tar xzf pyoai-2.5.0.tar.gz
cd pyoai-2.5.0
python -B setup.py install

<a href="https://pypi.org/project/pymarc/#files">pymarc-3.1.13.tar.gz</a>

tar xzf pymarc-3.1.13.tar.gz
cd pymarc-3.1.13
python -B setup.py install
</pre>
FILE
<pre>
     oai-marc.py - OAI-OMH 2.0 MARCXML record validation.
         year.sh - Yearly HTML statistic.
         stat.sh - Error code statistic.

country_code.txt - MARC country code file.
   lang_code.txt - MARC language code file.
   role_code.txt - MARC core code file.
  error_code.txt - MARC error code file.
</pre>
SOURCE

https://github.com/KyomaHooin/UCL

