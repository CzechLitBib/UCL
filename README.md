
DESCRIPTION

Library support software.

INSTALL
<pre>
apt-get install python-setuptools python-lxml python-six

wget https://files.pythonhosted.org/packages/21/3c/0ad6e6d50fc355be718fe667541797a27d0252641983b7925df685ef2163/pyoai-2.5.0.tar.gz
tar xzf pyoai-2.5.0.tar.gz
cd pyoai-2.5.0
python -B setup.py install

wget https://files.pythonhosted.org/packages/67/3e/1c4b239d179b2a24e8288ad4ae8f87a667bf5acb4c7907c68e3539ab9284/pymarc-3.1.13.tar.gz
tar xzf pymarc-3.1.13.tar.gz
cd pymarc-3.1.13
python -B setup.py install
</pre>
FILE
<pre>
 oai-marc/ - OAI-PMH 2.0 MARCXML record validator.
json-marc/ - Custom JSON to MARCXML convertor.
</pre>
SOURCE

https://github.com/KyomaHooin/UCL

