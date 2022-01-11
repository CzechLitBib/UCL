
DESCRIPTION

Solr / MARCSolr.

INSTALL
<pre>
# SOLR

apt-get install openjdk-11-jre-headless

cd /opt

wget 'https://dlcdn.apache.org/lucene/solr/8.10.0/solr-8.10.0.tgz'

adduser xxx

xxx:xxxxxxxxxx

su xxx

/opt/solr-8.10.0/bin/solr.in.sh:

SOLR_OPTS="$SOLR_OPTS -Dlog4j2.formatMsgNoLookups=true"
SOLR_ULIMIT_CHECKS=false

./opt/solr-8.10.0/bin/solr start

WARNING: Using _default configset with data driven schema functionality. NOT RECOMMENDED for production use.
         To turn off: bin/solr config -c core -p 8983 -action set-user-property -property update.autoCreateFields -value false

./opt/solr-8.10.0/bin/solr create -c core

cp solr-schema.py /opt

. /opt/solr-schema.py

cp solr /etc/init.d/

systemctl enable solr

# SOLRMARC

wget 'https://github.com/solrmarc/solrmarc/releases/download/3.4/simple_install_package_3.4.zip'

cp import /opt/solrmarc

. /opt/solrmarc/import ucla.xml
</pre>
SOLRMARC - BUG
<pre>
      FMT - Invalid control field.
      SIF - Non-repeatable (CLO).
      041 - Non-repeatable (CLO).
      072 - Non-repeatable.
      100 - Non-repeatable (CLO).
      110 - Non-repeatable (CLO).
      260 - Non-repeatable (CLO).
      520 - Non-repeatable.
      910 - Non-repeatable (CLO).
001232606 - 506 "control field". (CLO)
      LDR - Index out of bounds 2x. (CLO)
002190246 - LDR '2 2  nas a 2     4i'
</pre>
SOLR - BUG
<pre>
CLO ID:
d168cff7-4010-4d35-a182-e0847cc7ba3d
e40ff700-b24f-4721-993b-25802f9285bb
</pre>
FILE
<pre>
      field_all.txt - All fields.
   field_string.txt - Non-repeatable fields.
   subfield_all.txt - All subfields.
subfield_string.txt - Non-repeatable subfields.
    special_all.txt - Special fields.

             import - Import MARCXML file.
           index.py - Gen solrmarc mapping.
          schema.sh - Wrap solr-schema.py.
     solr-schema.py - Solr schema tool.
               solr - Solr INIT file.

   index.properties - SolrMARC index.
   log4j.properties - SolrMARC Logging.
</pre>

SOURCE

https://github.com/KyomaHooin/UCL
