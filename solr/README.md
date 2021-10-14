
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

Schema > Add Field > ..

cp solr /etc/init.d/

systemctl enable solr

# SOLRMARC

wget 'https://github.com/solrmarc/solrmarc/releases/download/3.4/simple_install_package_3.4.zip'

/opt/solrmarc/https:/index.properties:

leader_8 = 000[7]
year_008 = 008[7-10]
tag_100 = 100
tag_600 = 600
tag_700 = 700
tag_964 = 964
sub_773t = 773t
sub_7739 = 7739

cp import /opt/solrmarc

. /opt/solrmarc/import ucla.xml
</pre>
FILE
<pre>
          import - Import MARC.xml.
index.properties - MARC to Solr index.
            solr - Solr init file.
</pre>

SOURCE

https://github.com/KyomaHooin/UCL
