
DESCRIPTION

Solr / MARCSolr.

TODO
<pre>
-muti-core
-field_ + subfield_ + local_ prefix
</pre>
INSTALL
<pre>
# SOLR

apt-get install openjdk-11-jre-headless

cd /opt

wget 'https://dlcdn.apache.org/lucene/solr/8.10.0/solr-8.10.0.tgz'

adduser xxx

xxx:xxxxxxxxxx

su xxx

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
BUG
<pre>
FMT - Invalid control field.
072 - Non-repeatable.
520 - Non-repeatable.
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
