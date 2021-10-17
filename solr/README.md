
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

/opt/solrmarc/https:/index.properties:

LDR-8 = 000[7]
008-710 = 008[7-10]
100 = 100
600 = 600
700 = 700
964 = 964
773-t = 773t
773-9 = 7739

cp import /opt/solrmarc

. /opt/solrmarc/import ucla.xml
</pre>
FILE
<pre>
          import - Import MARCXML file.
  solr-schema.py - Solr schema tool.
            solr - Solr INIT file.

index.properties - SolrMARC index.
log4j.properties - SolrMARC Logging.
</pre>

SOURCE

https://github.com/KyomaHooin/UCL
